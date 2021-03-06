<?php


namespace AppBundle\Security;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /* @var $tokenProvider \AppBundle\Security\TokenProvider */
    private $jwtEncoder;
    private $em;
    public function __construct(JWTEncoderInterface $jwtEncoder, EntityManager $em)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->em = $em;
    }

    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );
        $token = $extractor->extract($request);
        if (!$token) {
            return null;
        }
        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
        } catch (JWTDecodeFailureException $e) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }
        if ($data === false) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }
        $username = $data['username'];
        return $this->em
            ->getRepository('AppBundle:User')
            ->findOneBy(['username' => $username]);
}
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception){}
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey){}
    public function supportsRememberMe()
    {
        return false;
    }
    public function start(Request $request, AuthenticationException $authException = null){}
}