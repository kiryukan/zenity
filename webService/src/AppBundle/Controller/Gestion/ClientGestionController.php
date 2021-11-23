<?php

namespace AppBundle\Controller\Gestion;

use AppBundle\Entity\Base\Base;
use AppBundle\Entity\Client;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Base\Instance;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ClientGestionController extends Controller
{

    //---------------------------Clients-------------------------------------------//
    /**
     * @Route("/gestion/client", name="manageClient")
     */
    public function manageClientAction(Request $request)
    {
        return $this->render('gestion/clientGestion.html.twig');
    }
    /**
     * @Route("/gestion/addClient", name="addClient")
     */
    public function addClient(Request $request){
        $em = $this->getDoctrine()->getManager();
        $clientName = $request->get('name');
        /* @var $registrationDate \DateTime */
        $registrationDate = new \DateTime();
        /* @var $client Client */
        $client = new Client();
        $client->setName($clientName);
        $client->setRegistrationDate($registrationDate);
        $em->persist($client);
        $em->flush();
        return $this->render('gestion/clientGestion.html.twig');
    }
    //-------
    /*~~~~~~~~~~~~~~~~~~~~~~AJAX~~~~~~~~~~~~~~~~~~~~*/
    /**
     * @Route("/gestion/deleteClient", name="deleteClient")
     */
    public function deleteClient(Request $request){
        $em = $this->getDoctrine()->getManager();
        $clientName = $request->get('name');
        /* @var $registrationDate \DateTime */
        $registrationDate = new \DateTime();

        /* @var $client Client */
        $client = $em->getRepository("AppBundle\\Entity\\Client")->findOneBy(["name"=>$clientName]);
        $em->remove($client);
        $em->flush();
        return new JsonResponse();
    }

}
