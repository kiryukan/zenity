<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Client;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class SecurityController extends Controller
{
// src/AppBundle/Controller/SecurityController.php

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/login", name="login")
     */
    public function gestionLogin(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
    /**
     * @param Request $request
     * @Route("/api/register", name="register")
     */
    public function register(Request $request){
        $em = $this->getDoctrine()->getManager();
        $clientName = $request->get('companyName');
        /* @var $registrationDate \DateTime */
        $registrationDate = new \DateTime();
        /* @var $client Client */
        $client = new Client();
        $client->setName($clientName);
        $client->setRegistrationDate($registrationDate);
        $em->persist($client);
        $user = new User();
        $login = $request->get('login');
        $user->setUsername($login)
        ->setPassword(
            hash(
                $this->getParameter('encryption_algorithm'),
                $request->get('password')
            )
        )->setEmail(
            $request->get('email')
        );
        $user->addClient($client);
        $em->persist($user);
        $token = "";
        $em->flush();
        if($user->getToken() == null){
            $token = $this->get('token_provider')->generateToken($request->get("fingerPrint"),$login,new \DateTime('now'));
            $user->setToken($token);
        }else{
            $token = $user->getToken();
        }
        return new JsonResponse(['token'=>$token]);
    }
    /**
     * @param Request $request
     * @Route("/api/login", name="webServiceToken")
     */
    public function tokenProvider(Request $request){
        $login = $request->get("login");
        if($this->loginSuccess($request->get("login"),$request->get('password'))){
            $em = $this->getDoctrine()->getManager();
            //$clientId = $em->getRepository('AppBundle\Entity\User')->findOneByUsername($request->get("login"))->getId();
            $dateTime = new \DateTime('now');
            /** @var User $user */
            $user = $em->getRepository('AppBundle\Entity\User')->findOneByUsername($request->get("login"));
            if($user->getToken() == null){
                $token = $this->get('token_provider')->generateToken($request->get("fingerPrint"),$login,$dateTime);
                $user->setToken($token);
            }else{
                $token = $user->getToken();
            }

            $em->persist($user);
            $em->flush();
            return new JsonResponse(['token'=>$token]);
        }else{
            throw new BadCredentialsException();
        }
    }
    /**
     * @param Request $request
     * @Route("/api/logout", name="unlogAction")
     */
    public function unlogAction(Request $request){
        $login = $userName = $this->get('api_key_user_provider')->getUsernameForApiKey($request->get('token'));
        $em = $this->getDoctrine()->getManager();
        /* @var $user User */
        $user = $em->getRepository('AppBundle\Entity\User')->findOneByUsername($login);
        if($user){
            $user->setToken(null);
            $em->persist($user);
            $em->flush();
            return new JsonResponse();
        }
        return new JsonResponse('',403);
    }
    private function loginSuccess($login,$password){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle\Entity\User')->findOneByUsername($login);
        $password = hash(
            $this->getParameter('encryption_algorithm'),
            $password
        );
        if ($user && $user->getPassword() === $password ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @Route("/api/changePassword",name="api-changePassword")
     */
    public function changePassword(Request $request){
        $login = $userName = $this->get('api_key_user_provider')->getUsernameForApiKey($request->get('token'));
        $em = $this->getDoctrine()->getManager();
        /* @var $user User */
        $user = $em->getRepository('AppBundle\Entity\User')->findOneByUsername($login);
        if($request->get('password') === null){
            return $this->render('security/changePassword.html.twig',[
                'userName'=>$login,
                'token'=>$request->get('token')
            ]);
        }
        if($user){
            $password = hash(
                $this->getParameter('encryption_algorithm'),
                $request->get('password')
            );
            $user->setPassword($password);
            $user->setToken(null);
            $em->persist($user);
            $em->flush();
              return $this->render('security/changePasswordSuccess.html.twig', array(
            ));
        }
        return new JsonResponse('Access denied',403);
    }
    /**
     * @Route("/resetPasswordByMail",name="api-emailChangePassword")
     */
    public function emailNewPassword(Request $request){
        $em = $this->getDoctrine()->getManager();
        $email = $request->get('email');
        /* @var $user User */
        $user = $em->getRepository('AppBundle\Entity\User')->findOneByEmail($email);
        if($user){
            $token = hash('sha512',rand());
            $user->setToken($token);
            $em->persist($user);
            $em->flush();
            $baseUrl = $request->getSchemeAndHttpHost().'webService';
            $passwordUrl = $baseUrl.'webService/api/changePassword?token='.$token;
            $message = \Swift_Message::newInstance()
                ->setSubject('[ZENITY] password request')
                ->setFrom($this->getParameter('mailer_default_sender'))
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        'emails/newPassword.html.twig',
                    [
                        'username' => $user->getUsername(),
                        'passwordUrl'=> $passwordUrl
                    ]
                    ),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($message);
            return $this->render(
                'emails/newPassword.html.twig',
                [
                    'username' => $user->getUsername(),
                    'passwordUrl'=> $passwordUrl
                ]);/**/
        }else{
            return new Response('',403);
        }
    }

        /**
     * @param Request $request
     * @Route("/api", name="testConnection")
     */
    public function testConnection(Request $request){
        $username = $this->get('api_key_user_provider')->getUsernameForApiKey($request->get('token'));
        if( empty($request->get('token')) || $request->get('token') == '') {
            return new Response('',403);
        }
        else if($username === null){
            return new Response('',403);

        }else{
            return new JsonResponse($username);
        }
    }
}
