<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use App\Model\User\UseCase\Network\Auth;

class FacebookAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $router;
    private $userProvider;
    private $handler;

    public function __construct(
        ClientRegistry $clientRegistry,
        RouterInterface $router,
        UserProviderInterface $userProvider,
        Auth\Handler $handler
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->userProvider = $userProvider;
        $this->handler = $handler;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'auth.facebook.check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('facebook_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                $facebookUser = $client->fetchUserFromToken($accessToken);

                $identity = $facebookUser->getId();
                $firstName = $facebookUser->getFirstName();
                $lastName = $facebookUser->getLastName();
                $network = 'facebook';

                $username = sprintf('%s:%s', $network, $identity);
                try {
                    $user = $this->userProvider->loadUserByUsername($username);
                } catch (UserNotFoundException $e) {
                    $this->handler->handle(new Auth\Command($network, $identity, $firstName, $lastName));
                    $user = $this->userProvider->loadUserByUsername($username);
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('home');
        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}
