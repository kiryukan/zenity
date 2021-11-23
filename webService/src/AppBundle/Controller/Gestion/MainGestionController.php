<?php
/**
 * This page is just for routing the main page, yes it take ~20 lines to do that FUCK PHP !!!
 */
namespace AppBundle\Controller\Gestion;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class MainGestionController extends Controller
{
    /**
     * @Route("webService/gestion/", name="gestionIndex")
     */
    public function homePage(Request $request)
    {
        //test
        return $this->render('gestion/homePage.html.twig', []);
    }
}