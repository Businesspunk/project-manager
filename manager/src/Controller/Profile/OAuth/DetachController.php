<?php

namespace App\Controller\Profile\OAuth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Model\User\UseCase\Network\Detach;
use Symfony\Component\Routing\Annotation\Route;

class DetachController extends AbstractController
{
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
            $this->addFlash('success','Network was successfully detached');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('profile');
    }
}