<?php

namespace AppBundle\Controller\Gestion;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class AccountGestionController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/gestion/account", name="gestion_account")

     */
    public function accountGestionAction(Request $request){
        return $this->render('gestion/accountGestion.html.twig', [
        ]);
    }
    /**
     * @param Request $request
     * @Route("/gestion/createUser", name="gestion-createUser")
     */
    public function createUser(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setUsername(
            $request->get('userName')
        )->setPassword(
            hash(
                $this->getParameter('encryption_algorithm'),
                $request->get('password')
            )
        )->setEmail(
            $request->get('email')
        );
        foreach ($request->get('clients') as $clientId) {
            $user->addClient(
                $em->getRepository('AppBundle\Entity\Client')->findOneById($clientId)
            );
        }
        $em->persist($user);
        $em->flush();
        return $this->render('gestion/homePage.html.twig');
    }
    /**
     * @param Request $request
     * @Route("/gestion/updateUser", name="gestion-updateUser")
     */
    public function updateUserClients(Request $request){
        $em = $this->getDoctrine()->getManager();
        /* @var $user User */
        $user = $em->getRepository('AppBundle\Entity\User')->findOneById($request->get('userId'));
        if(!$user) return new JsonResponse('userNotFound',403);
        if($request->get('email') != null)$user->setEmail($request->get('email'));
        if($request->get('username') != null)$user->setUsername($request->get('username'));

        ($request->get('isActive') == true )?$user->setIsActive(true):$user->setIsActive(false);
        $clients = $request->get('clients')?$request->get('clients'):[];
        $user->removeClients();
        foreach ($clients as $clientId) {
            $client = $em->getRepository('AppBundle\Entity\Client')->findOneById($clientId);
            $user->addClient($client);
        }
        $em->merge($user);
        $em->flush();
        return $this->render('gestion/accountGestion.html.twig');
    }
    /**
     * @param Request $request
     * @Route("/gestion/deleteUser", name="gestion-deleteUser")
     */
    public function deleteUserClients(Request $request){
        $em = $this->getDoctrine()->getManager();
        /* @var $user User */
        $user = $em->getRepository('AppBundle\Entity\User')->findOneById($request->get('userId'));
        $em->remove($user);
        $em->flush();
        return $this->render('gestion/accountGestion.html.twig');
    }
}
