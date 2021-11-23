<?php

namespace AppBundle\Controller\Gestion;

use AppBundle\Entity\CloudBase\CloudInstance;
use AppBundle\Entity\CloudBase\Provider;

use AppBundle\Entity\CloudBase\Price;
use AppBundle\Entity\ComplementaryFlow\PerformanceBench;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Base\Instance;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class CloudTestGestionController extends Controller
{
    /**
     * @Route("/gestion/cloud", name="gestion_cloud")
     */
    public function manageCloudAction(Request $request)
    {
      return $this->render("gestion/cloud/mainCloud.html.twig",[]);
    }

    /**
     * @Route("/gestion/cloud-test/paas", name="gestion_cloud_paas")
     */
    /*public function getManageCloudPaasAction(Request $request)
    {
      $pricesPaas=["test1", "test2"];
      return $this->render("gestion/cloud/mainScreenPaas.html.twig",[
        "pricesPaas"=>$pricesPaas,
        "performanceIndicators"=>[
          "cpu",
          "insert",
          "update",
          "sqlPlus"
        ]
      ]);
    }*/

    /**
     * @Route("/gestion/cloud-test/iaas", name="gestion_cloud_iaas")
     */
    /*public function getManageCloudIaasAction(Request $request)
    {
      $pricesIaas=["test1", "test3"];
      return $this->render("gestion/cloud/mainScreenIaas.html.twig",[
          "pricesIaas"=>$pricesIaas,
          "performanceIndicators"=>[
            "cpu",
            "insert",
            "update",
            "sqlPlus"
          ]
        ]);
    }*/
}