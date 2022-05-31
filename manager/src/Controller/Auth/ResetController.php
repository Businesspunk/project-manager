<?php

namespace App\Controller\Auth;

use App\Controller\ControllerFlashTrait;
use App\Controller\ErrorHandler;
use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Model\User\UseCase\Reset;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/reset", name="auth.reset")
 */
class ResetController extends AbstractController
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
     * @Route("", name="", methods={"GET","POST"})
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
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/auth/reset/request.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/{token}", name=".reset", methods={"GET","POST"})
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
                $this->addExceptionFlash($e);
                $this->errorHandler->handle($e);
            }
        }

        return $this->render('app/auth/reset/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
