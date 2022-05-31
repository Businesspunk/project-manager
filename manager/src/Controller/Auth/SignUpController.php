<?php

namespace App\Controller\Auth;

use App\Controller\ControllerFlashTrait;
use App\Controller\ErrorHandler;
use App\Model\User\UseCase\SignUp;
use App\ReadModel\User\UserFetcher;
use App\Security\LoginFormAuthenticator;
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
    use ControllerFlashTrait;

    private $translator;
    private $errorHandler;

    public function __construct(ErrorHandler $errorHandler, TranslatorInterface $translator)
    {
        $this->errorHandler = $errorHandler;
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
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
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
            $this->addExceptionFlash($e);
            $this->errorHandler->handle($e);
        }
        return $this->redirectToRoute('home');
    }
}
