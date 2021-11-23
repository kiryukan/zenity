<?php

namespace AppBundle\Controller\Gestion;

use AppBundle\Entity\CloudBase\CloudInstance;
use AppBundle\Entity\CloudBase\Provider;

use AppBundle\Entity\CloudBase\Price;
use AppBundle\Entity\ComplementaryFlow\PerformanceBench;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Base\Instance;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
/*const PRICES_NAME_PAAS = [
  "BYOL",
  "SE",
  "EE",
  "EE1",
  "EE2",
  "EE3"
];*/
class CloudPaasGestionController extends Controller
{
    /**
     * @Route("/gestion/cloud/paas", name="gestion_cloud_paas")
     */
    public function manageCloudActionPaas(Request $request)
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
    }

    //-------
    /*~~~~~~~~~~~~~~~~~~~~~AJAX~~~~~~~~~~~~~~~~~~~~*/
    /**
     * @Route("/gestion/cloudInstancesPaas/getTree", name="getTree")
     */
    public function getTree(Request $request){
        $em = $this->getDoctrine()->getManager();
        $queryForInstance = $em->createQueryBuilder()
            ->select("cloudInstance.name,cloudInstance.id")
            ->from("AppBundle\Entity\CloudBase\CloudInstance","cloudInstance")
            ->where("cloudInstance.provider = :provider");
        $providers = $em->getRepository("AppBundle\Entity\CloudBase\Provider")->findAll(Query::HYDRATE_ARRAY);
        $tree = [];
        foreach ($providers as $key => $provider) {
          $cloudInstances = $queryForInstance->setParameter('provider',$provider->getId())
          ->getQuery()
          ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
          $tree[] = [
            'id'=>$provider->getId(),
            'text'=>$provider->getName(),
            'isRoot'=>true,
            'nodes'=>array_map(function($instance){
              return [
                'text'=>'<input type="hidden" class="btn btn-del-instance" id="delete-instance-'.$instance['id'].'" value="X">&nbsp&nbsp'.$instance['name'],
                'id'=>$instance['id']
              ];
            },$cloudInstances)
          ];
        }
        return new JsonResponse($tree);
    }
    /**
     * @Route("/gestion/cloudInstancesPaas/getCloudInstance/{instance_id}", name="getCloudInstance")
     */
    public function getCloudInstance(Request $request,$instance_id){
      $em = $this->getDoctrine()->getManager();
      $instance_array = $em->createQueryBuilder()
          ->select("cloudInstance,performanceBench,licencePrices")
          ->from("AppBundle\Entity\CloudBase\CloudInstance","cloudInstance")
          ->leftjoin("cloudInstance.performanceBench","performanceBench")
          ->leftjoin("cloudInstance.licencePrices","licencePrices")
          ->where("cloudInstance.id = :instance_id")
          ->setParameter('instance_id',$instance_id)
          ->getQuery()
          ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      if (sizeOf($instance_array) == 0) return new JsonResponse();
      unset($instance_array[0]["performanceBench"]["id"]);
      unset($instance_array[0]["performanceBench"]["timestamp"]);
      return new JsonResponse($instance_array[0]);
    }
    /**
     * @Route("/gestion/cloudInstancesPaas/updateCloudInstance", name="updateCloudInstance")
     */
    public function updateCloudInstance(Request $request){
      $msg = "rien enregistré";
      $em = $this->getDoctrine()->getManager();
      $formData = $request->request->all();
      $cloudInstance = $em->getRepository("AppBundle\Entity\CloudBase\CloudInstance")
        ->findOneById($formData['id']);
      $cloudInstance->setName($formData['name']);
      $cloudInstance->setTechnicalInfos($formData['specSheet']);
      $cloudInstance->setBaseStorageCapacity($formData['baseStorage']);
      $cloudInstance->setCostPerGo($formData['costPerGo']);
      $cloudInstance->setCostPerCpu($formData['costPerCpu']);
      $cloudInstance->setMinCpuCount($formData['minCpu']);
      $cloudInstance->setMaxCpuCount($formData['maxCpu']);
      $cloudInstance->setCpuCountRule($formData['cpuCountRule']);

      $em->persist($cloudInstance);

      if($cloudInstance->getPerformanceBench() != null){
        $performanceBench = $cloudInstance->getPerformanceBench();
        $ressourceProvider = $this->get('ressource.performancebench');
        $perfFormData = array_change_key_case($formData['performanceBench'],CASE_UPPER );
        $ressourceProvider->updateFromArray($performanceBench,$perfFormData);
        $em->persist($performanceBench);
      }else{
        $msg = "performance bench is null";
      }
      $em->flush();
      $msg = "enregistré";
      return new JsonResponse($msg);
    }


    /**
     * @Route("/gestion/cloudInstancesPaas/createCloudInstance", name="createCloudInstance")
     */
    public function createCloudInstance(Request $request){
      $em = $this->getDoctrine()->getManager();
      $instance = new CloudInstance();
      $performanceBench = new performanceBench();
      $provider_name= $request->query->get("provider_name")??'Oracle';
      $provider = $em->getRepository("AppBundle\Entity\CloudBase\Provider")->findOneBy(['name'=>$provider_name]);
      $instance->setProvider($provider);
      foreach (PRICES_NAME_IAAS as $priceName) {
        $price = new Price();
        $price->setName($priceName);
        $price->setValue(0);
        $em->persist($price);
        $instance->addLicencePrice($price);
      }
      $instance->setPerformanceBench($performanceBench);
      $instance->setName($request->query->get("instance_name")??'unamed');
      $em->persist($instance);
      $em->flush();
      return new JsonResponse($provider->getId());
    }
    /**
    * @Route("/gestion/cloudInstancesPaas/createProvider", name="createProvider")
    */
    public function createProvider(Request $request){
      $em = $this->getDoctrine()->getManager();
      $provider = new Provider();
      $provider_name= $request->query->get("provider_name")??'Oracle';
      $provider->setName($provider_name);

      $em->persist($provider);
      $em->flush();
      return new JsonResponse($provider->getId());
    }
    /**
     * @Route("/gestion/cloudInstancesPaas/deleteProvider", name="deleteProvider")
     */
    public function deleteProvider(Request $request){
      $em = $this->getDoctrine()->getManager();
      $provider = $em->getRepository("AppBundle\Entity\CloudBase\Provider")->findOneBy(['id'=>$request->query->get("provider_id")]);
      $em->remove($provider);
      $em->flush();
      return new JsonResponse();
    }
    /**
     * @Route("/gestion/cloudInstancesPaas/deleteInstance", name="deleteInstance")
     */
    public function deleteInstance(Request $request){
      $em = $this->getDoctrine()->getManager();
      $instance_id = $request->request->get('instance_id');
      $instance = $em->getRepository("AppBundle\Entity\CloudBase\CloudInstance")->findOneById($instance_id);
      $em->remove($instance);
      $em->flush();
      return new JsonResponse();
    }

    // ADDITIONAL FEATURES
    //--------------------
    /**
     * @Route("/gestion/cloudInstancesPaas/prices/deletePrice", name="deletePrice")
     */
    public function deletePrice(Request $request){
      $em = $this->getDoctrine()->getManager();
      $licenceId = $request->request->get('licence_id');
      $price = $em->getRepository("AppBundle\Entity\CloudBase\Price")->findOneById($licenceId);
        $em->remove($price);
        $em->flush();
        return new JsonResponse();
    }

    /**
     * @Route("/gestion/cloudInstancesPaas/prices/addPrice", name="addPrice")
     */
    public function addPrice(Request $request){
      $msg = "";
      try{
        $this->em = $this->getDoctrine()->getManager();
      }catch(\Exception $e){
        $msg = $e;
      }
      $instanceId = $request->request->get('instance_id');
      $licenceName = $request->request->get('newLicenceName');
      $licencePrice = $request->request->get('newLicencePrice');

      $queryInsertPrice = "INSERT INTO zenityService.prices VALUES(null, $instanceId, '$licenceName', $licencePrice, 'test');";
      try{
        $queryInsertPriceStatement = $this->em->getConnection()->prepare($queryInsertPrice);
        $msg = "prepare ok";
      }
      catch(\Exception $e){
        $msg = "prepare not working ".$e;
      }
      
      $queryInsertPriceStatement->execute();
      $insertResult = $queryInsertPriceStatement->fetchAll();
      return new JsonResponse("test response: ".$msg);
    }

    /**
     * @Route("/gestion/cloudInstancesPaas/createNewCloudInstance", name="createNewCloudInstance")
     */
    public function createNewCloudInstance(Request $request){
      $msg = "rien";
      try{
        $this->em = $this->getDoctrine()->getManager();
        $providerId = $request->request->get('provider_id');
        $cloudInstanceName = $request->request->get('cloud_instance_name');

        $provider = $this->em->getRepository("AppBundle\Entity\CloudBase\Provider")->findOneBy(['id'=>$providerId]);
        if($provider){
          $cloudInstance = new CloudInstance();
          $cloudInstance->setName($cloudInstanceName);
          $cloudInstance->setProvider($provider);

          $this->em->persist($cloudInstance);
          $this->em->flush();

          $msg = "provider ok";
        }else{
          $msg = "provider not found";
        }
      }catch(\Exception $e){
        $msg = $e;
      }
      return new JsonResponse("add cloud instance: ".$msg);
    }

    /**
     * @Route("/gestion/cloudInstancesPaas/deleteCloudInstance", name="deleteCloudInstance")
     */
    public function deleteCloudInstance(Request $request){
      $msg = "rien";
      try{
        $this->em = $this->getDoctrine()->getManager();
        $cloudInstanceId = $request->request->get('cloud_instance_id');
        $cloudInstance = $this->em->getRepository("AppBundle\Entity\CloudBase\CloudInstance")->findOneById($cloudInstanceId);
        $this->em->remove($cloudInstance);
        $this->em->flush();
        $msg = "ok, cloud instance deleted";
      }catch(\Exception $e){
        $msg = $e;
      }
      return new JsonResponse("remove cloud instance: ".$msg);
    }

}