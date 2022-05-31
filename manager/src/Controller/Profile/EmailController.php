<?php

namespace App\Controller\Profile;

use App\Controller\ControllerFlashTrait;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\UseCase\Email\Request as ChangeEmail;
use App\Model\User\UseCase\Email\Confirm as ConfirmEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/profile/email", name="profile")
 */
class EmailController extends AbstractController
{
    use ControllerFlashTrait;

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("", name=".email", methods={"GET","POST"})
     */
    public function index(Request $request, ChangeEmail\Handler $handler): Response
    {
        $command = new ChangeEmail\Command();
        $form = $this->createForm(ChangeEmail\Form::class, $command);
        $command->id = $this->getUser()->getId();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $message = filter_var($this->getUser()->getUsername(), FILTER_VALIDATE_EMAIL)
                            ? 'Check your mailbox of new your new email address'
                            : 'Email is successfully added';

                $this->addFlash('success', $message);
                return $this->redirectToRoute('profile.email');
            } catch (\DomainException $e) {
                $this->addExceptionFlash($e);
            }
        }

        return $this->render('app/profile/email/change.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/confirm_new/{token}", name=".email.confirm_new", methods={"GET","POST"})
     */
    public function confirm(string $token, ConfirmEmail\Handler $handler, UserRepository $users): Response
    {
        $command = new ConfirmEmail\Command($this->getUser()->getId(), $token);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Email is successfully changed');
            return $this->redirectToRoute('auth.login');
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
            return $this->redirectToRoute('profile.email');
        }
    }
}
