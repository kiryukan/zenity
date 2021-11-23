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
/*const PRICES_NAME_IAAS = [
  "BYOL",
  "SE",
  "EE",
  "EE1",
  "EE2",
  "EE3"
];*/
class CloudIaasGestionController extends Controller
{
    /**
     * @Route("/gestion/cloud-test/iaas", name="gestion_cloud_iaas")
     */
    public function manageCloudActionIaas(Request $request)
    {
      $pricesIaas=["test1", "test2"];
      return $this->render("gestion/cloud/mainScreenIaas.html.twig",[
        "pricesName"=>$pricesIaas,
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
     * @Route("/gestion/cloudInstances/getTree", name="getTree")
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
                'text'=>$instance['name'],
                'id'=>$instance['id']
              ];    
            },$cloudInstances)
          ];
        }
        return new JsonResponse($tree);
    }
    /**
     * @Route("/gestion/cloudInstances/getCloudInstance/{instance_id}", name="getCloudInstance")
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
     * @Route("/gestion/cloudInstances/updateCloudInstance", name="updateCloudInstance")
     */
    public function updateCloudInstance(Request $request){
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

      foreach($formData['prices'] as $priceName => $value){
        $price = $em->getRepository("AppBundle\Entity\CloudBase\Price")
          ->findOneBy(['cloudInstance'=>$formData['id'],'name'=>$priceName]);
        if(!$price){
          $price = new Price();
          $price->setName($priceName);
          $cloudInstance->addLicencePrice($price);
          $em->persist($price);
          $em->persist($cloudInstance);
        }
        $price->setValue($value);
        $em->persist($price);
      }

      $performanceBench = $cloudInstance->getPerformanceBench();
      $ressourceProvider = $this->get('ressource.performancebench');
      $perfFormData = array_change_key_case($formData['performanceBench'],CASE_UPPER );
      $ressourceProvider->updateFromArray($performanceBench,$perfFormData);
      $em->persist($performanceBench);
      $em->flush();
      return new JsonResponse();
    }
    /**
     * @Route("/gestion/cloudInstances/createCloudInstance", name="createCloudInstance")
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
    * @Route("/gestion/cloudInstances/createProvider", name="createProvider")
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
     * @Route("/gestion/cloudInstances/deleteProvider", name="deleteProvider")
     */
    public function deleteProvider(Request $request){
      $em = $this->getDoctrine()->getManager();
      $provider = $em->getRepository("AppBundle\Entity\CloudBase\Provider")->findOneBy(['id'=>$request->query->get("provider_id")]);
      $em->remove($provider);
      $em->flush();
      return new JsonResponse();
    }
    /**
     * @Route("/gestion/cloudInstances/deleteInstance", name="deleteInstance")
     */
    public function deleteInstance(Request $request){
      $em = $this->getDoctrine()->getManager();
      $instance_id = $request->request->get('instance_id');
      $instance = $em->getRepository("AppBundle\Entity\CloudBase\CloudInstance")->findOneById($instance_id);
      $em->remove($instance);
      $em->flush();
      return new JsonResponse();
    }
}