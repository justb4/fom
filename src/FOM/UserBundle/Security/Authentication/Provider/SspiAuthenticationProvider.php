<?php
namespace  FOM\UserBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use FOM\UserBundle\Security\Authentication\Token\SspiUserToken;

class SspiAuthenticationProvider implements AuthenticationProviderInterface
{

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->provider = $userProvider;
    }

    public function authenticate(TokenInterface $token) {
        $user = $this->provider->loadUserByUsername($token->getUsername());

        if($user) {
            $authToken = new SspiUserToken(true, $user->getRoles());
            $authToken->setUser($user);
            return $authToken;
        }

        throw new AuthenticationException('No such user.');
    }

    public function supports(TokenInterface $token) {
        return $token instanceof SspiUserToken;
    }

}
