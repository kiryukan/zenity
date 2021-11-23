<?php
// src/AppBundle/Security/ApiKeyUserProvider.php
namespace AppBundle\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ApiKeyUserProvider implements UserProviderInterface
{
    private $em;
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    public function getUsernameForApiKey($apiKey)
    {
        // TODO: generate token

        $user = $this->em->getRepository('AppBundle\Entity\User')->findOneByToken($apiKey);
        if($user){
            return $user->getUsername();
        }else{
            return 'anon';
        }

    }

    public function loadUserByUsername($username)
    {
        if($username != 'anon'){
            return new User(
                $username,
                null,
                // the roles for the user - you may choose to determine
                // these dynamically somehow based on the user
                array('ROLE_API')
            );
        }else{
            return new User(
                'None',
                null,
                // the roles for the user - you may choose to determine
                // these dynamically somehow based on the user
                array('IS_AUTHENTICATED_ANONYMOUSLY')
            );
        }

    }

    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}