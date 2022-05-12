<?php

namespace App\Controller\Auth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/login/facebook", name="auth.facebook")
 */
class FacebookController extends AbstractController
{
    /**
     * @Route("", name=".start")
     */
    public function connect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook_main')
            ->redirect(['public_profile', 'email']);
    }
    /**
     * @Route("/check", name=".check")
     */
    public function check(Request $request, ClientRegistry $clientRegistry)
    {

    }
}