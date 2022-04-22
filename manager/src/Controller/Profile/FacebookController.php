<?php

namespace App\Controller\Profile;

use App\Model\User\UseCase\Network\Attach;
use App\Model\User\UseCase\Network\Detach;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class FacebookController extends AbstractController
{
    /**
     * @Route("/oauth/facebook", name="profile.auth.facebook")
     */
    public function connect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook_attach')
            ->redirect(['public_profile', 'email']);
    }

    /**
     * @Route("/oauth/facebook/check", name="profile.auth.facebook.attach")
     */
    public function attach(ClientRegistry $clientRegistry, Attach\Handler $handler): Response
    {
        $client = $clientRegistry->getClient('facebook_attach');

        $command = new Attach\Command(
            $this->getUser()->getId(),
            'facebook',
            $client->fetchUser()->getId()
        );

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Facebook was successfully added');
        } catch (\Exception $e){
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/oauth/facebook/detach/{network}", name="profile.auth.facebook.detach")
     */
    public function detach(string $network, Detach\Handler $handler): Response
    {
        $command = new Detach\Command(
            $this->getUser()->getId(),
            $network
        );

        try {
            $handler->handle($command);
            $this->addFlash('success','Network was successfully detached');
        } catch (\Exception $e) {
            $this->addFlash('error',$e->getMessage());
        }
        return $this->redirectToRoute('profile');
    }
}