<?php

namespace App\Controller\Auth;

use App\Model\User\UseCase\SignUp;
use App\ReadModel\User\UserFetcher;
use App\Security\LoginFormAuthenticator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/signup", name="auth.signup")
 */
class SignUpController extends AbstractController
{
    private $logger;
    private $translator;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }
    /**
     * @Route("", name="")
     */
    public function request(Request $request, SignUp\Request\Handler $handler): Response
    {
        $command = new SignUp\Request\Command();
        $form = $this->createForm(SignUp\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email');
                return $this->redirectToRoute('auth.signup');
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/auth/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/{token}", name=".confirm")
     */
    public function confirm(
        Request $request,
        string $token,
        SignUp\Confirm\byEmail\Handler $handler,
        LoginFormAuthenticator $formAuthenticator,
        UserAuthenticatorInterface $authenticator,
        UserProviderInterface $userProvider,
        UserFetcher $users
    ): Response {
        if (!$user = $users->findBySignUpConfirmationToken($token)) {
            throw new \DomainException('User is not found');
        }

        $command = new SignUp\Confirm\byEmail\Command($token);
        try {
            $handler->handle($command);
            return $authenticator->authenticateUser(
                $userProvider->loadUserByUsername($user->email),
                $formAuthenticator,
                $request
            );
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }
        return $this->redirectToRoute('home');
    }
}
