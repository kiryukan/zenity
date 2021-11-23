<?php

namespace AppBundle\Controller;
use AppBundle\Services\GraphRessource;
use AppBundle\Services\SnapshotReading;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        $response = new Response();
        $response->setMaxAge(87600);
        // replace this example code with whatever you need
        return $this->render('security/loginPage.html.twig', [
        ],$response);
    }

    /**
     * @Route("/reporting", name="selectReport")
     */
    public function selectReportAction(Request $request)
    {
        $response = new Response();
        $response->setMaxAge(87600);
        // replace this example code with whatever you need
        return $this->render('reporting/selectReport.html.twig', [
        ],$response);
    }
    /**
     * @Route ("/ressources/rapport",name="generateCompleteReport")
     */
    public function generateCompleteReport(Request $request)
    {
        try{
            if($_GET['byol']){
                $response = new Response();
                $response->setMaxAge(87600);
                $date = $_GET['date'];
                $from = explode(' - ',$date)[0];
                $to = explode(' - ',$date)[1];
                return $this->render('reporting/completeReport.html.twig',[
                    'instanceId'=>$_GET['instanceId'],
                    'clientId'=>$_GET['clientId'],
                    'baseId'=>$_GET['baseId'],
                    'from'=>$from,
                    'to'=>$to,
                    'byol'=>"true",
                    'licence'=>$_GET['licence']
                ],$response);
            }
        }catch(\Exception $e){
            $response = new Response();
            $response->setMaxAge(87600);
            $date = $_GET['date'];
            $from = explode(' - ',$date)[0];
            $to = explode(' - ',$date)[1];
            return $this->render('reporting/completeReport.html.twig',[
                'instanceId'=>$_GET['instanceId'],
                'clientId'=>$_GET['clientId'],
                'baseId'=>$_GET['baseId'],
                'from'=>$from,
                'to'=>$to,
                'byol'=>"false",
                'licence'=>$_GET['licence']
            ],$response);
        }
    }
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $this->get('twig')->addGlobal('logged',true);
        return $this->redirectToRoute('index');
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request){
        $this->get('twig')->addGlobal('logged',false);
        return $this->render('security/logout.html.twig', [
        ]);
    }
    /**
     * @Route ("/register",name="register")
     */
    function registerAction(Request $request){
        return $this->render('security/register.html.twig', [

        ]);
    }
}
