<?php

namespace App\Controller\Auth;

use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Model\User\UseCase\Reset;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Route ("/reset", name="auth.reset")
     * @param Request $request
     * @param Reset\Request\Handler $handler
     * @return Response
     */
    public function request(Request $request, Reset\Request\Handler $handler): Response
    {
        $command = new Reset\Request\Command();
        $form = $this->createForm(Reset\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check you email');
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
                $this->logger->error($e->getMessage());
            }
        }

        return $this->render('app/auth/reset/request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/reset/{token}", name="auth.reset.reset")
     * @param string $token
     * @param Request $request
     * @param Reset\Reset\Handler $handler
     * @return Response
     */
    public function reset(string $token, Request $request, Reset\Reset\Handler $handler, UserFetcher $users): Response
    {
        if (!$users->existsByResetToken($token)) {
            $this->addFlash('error', 'Invalid or incorrect token');
            return $this->redirectToRoute('home');
        }
        $command = new Reset\Reset\Command($token);

        $form = $this->createForm(Reset\Reset\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password is successfully changed');
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->addFlash('error', $e->getMessage());
                $this->logger->error($e->getMessage());
            }
        }

        return $this->render('app/auth/reset/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}