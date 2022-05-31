<?php

namespace App\Controller\Profile\OAuth;

use App\Controller\ControllerFlashTrait;
use App\Model\User\UseCase\Network\Attach;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/profile/oauth/facebook", name="profile.auth.facebook")
 */
class FacebookController extends AbstractController
{
    use ControllerFlashTrait;

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("", name="", methods={"GET"})
     */
    public function connect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook_attach')
            ->redirect(['public_profile', 'email']);
    }
    /**
     * @Route("/check", name=".attach", methods={"GET"})
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
        } catch (\DomainException $e) {
            $this->addExceptionFlash($e);
        }
        return $this->redirectToRoute('profile');
    }
}
