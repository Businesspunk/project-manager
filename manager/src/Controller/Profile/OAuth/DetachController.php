<?php

namespace App\Controller\Profile\OAuth;

use App\Controller\ControllerFlashTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Model\User\UseCase\Network\Detach;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DetachController extends AbstractController
{
    use ControllerFlashTrait;

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/oauth/detach/{network}/{identity}", name="profile.auth.detach", methods={"POST"})
     */
    public function detach(string $network, string $identity, Request $request, Detach\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('profile');
        }

        $command = new Detach\Command(
            $this->getUser()->getId(),
            $network,
            $identity
        );

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Network was successfully detached');
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
        }
        return $this->redirectToRoute('profile');
    }
}
