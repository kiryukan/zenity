<?php
/*
* Dear Maintainer,
* Here i've tried to build an REST API,
* why i did not use a symfony bundled one, well because i did'nt have a clue that thing exist
* and in fact it is my second REST API (my first one was on school), so keep that in mind when you read $this
* dont be mad at me please ;)
*/
namespace AppBundle\Controller;

use AppBundle\Entity\Base\Base;
use AppBundle\Entity\Base\Instance;
use AppBundle\Entity\Client;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\Entity\User;
use AppBundle\RessourceProvider\Ressources\EventRessource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Constraints\DateTime;

class RessourceController extends Controller
{
    /**
     * @param Request $request
     * @return Collection
     */
    private function getAuthorizedClients(Request $request)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        $em = $this->getDoctrine()->getManager();
        $routeName = $request->get('_route');
        if (preg_match('/gestion_.*/', $routeName)) {
            $clientsEntities = $em->getRepository('AppBundle\Entity\Client')->findAll();
            return new ArrayCollection($clientsEntities);
        } else {
            $userName = $this->get('api_key_user_provider')->getUsernameForApiKey($request->get('token'));
            $user = $em->getRepository('AppBundle\Entity\User')->findOneByUsername($userName);
            $clientsEntities = $user->getClients();
            return $clientsEntities;
        }
    }
    private function checkAccess( $request, $clientId , $baseId = null, $instanceId = null,callable $callback){
        $em = $this->getDoctrine()->getManager();
        $clientEntity = $em->getRepository('AppBundle\Entity\Client')->findOneById($clientId);
        /* @var $instanceEntity Instance */
        if ($this->getAuthorizedClients($request)->contains($clientEntity)) {
            if ($baseId === null) {
                return call_user_func_array($callback,[$clientEntity,null,null]);
            }
            $basesEntity = $em->getRepository('AppBundle\Entity\Base\Base')->findOneById($baseId);
            if ($clientEntity->getBases()->contains($basesEntity)) {
                if ($instanceId === null) {
                    return call_user_func_array($callback,[$clientEntity,$basesEntity,null]);
                }
                $instanceEntity = $em->getRepository('AppBundle\Entity\Base\Instance')->findOneById($instanceId);
                if ($basesEntity->getInstances()->contains($instanceEntity)) {
                    return call_user_func_array($callback,[$clientEntity,$basesEntity,$instanceEntity,]);
                }
            }
            return new JsonResponse('Ressource Not found', 404);
        } else {
            return new JsonResponse('Access denied', 403);
        }
    }
    /**
     * @Route("/api/ressources/setcache/", name="api_setUserCache")
     * @Route("/gestion/ressources/setcache/",name="gestion_setUserCache")
     */
    public function setUserCache(Request $request){
        $em = $this->getDoctrine()->getManager();
        $userName = $this->get('api_key_user_provider')->getUsernameForApiKey($request->get('token'));
        /* @var $user User */
        $user = $em->getRepository('AppBundle\Entity\User')->findOneByUsername($userName);
        $user->setCache($request->get("cache"));
        $em->persist($user);
        $em->flush();
        return new JsonResponse();

    }
    /**
     * @Route("/api/ressources/cache/", name="api_userCache")
     * @Route("/gestion/ressources/cache/",name="gestion_userCache")
     */
    public function getUserCache(Request $request){
        $em = $this->getDoctrine()->getManager();
        $userName = $this->get('api_key_user_provider')->getUsernameForApiKey($request->get('token'));
        /* @var $user User */
        $user = $em->getRepository('AppBundle\Entity\User')->findOneByUsername($userName);
        return new JsonResponse($user->getCache());
    }
    /**
     * @Route("/api/ressources/instance_uri/", name="api_instanceUri")
     * @Route("/gestion/ressources/instance_uri/",name="gestion_instanceUri")
     */
    public function getInstanceURI(Request $request){
        $instanceId = $request->get('instanceId');
        $em = $this->getDoctrine()->getManager();
        /* @var $instance Instance */
        $instance = $em->getRepository('AppBundle\Entity\Base\Instance')->findOneById($instanceId);
        $base = $instance->getBase();
        $client = $base->getClient();
        $response = new JsonResponse('/'.$client->getId().'/'.$base->getId().'/'.$instanceId);
        return $response->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }
    /**
     * Get the all the client with an account in the app
     * @Route("/api/ressources/", name="clients")
     * @Route("/gestion/ressources/",name="gestion_client")
     */
    public function getClient(Request $request)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        $clientsEntities = $this->getAuthorizedClients($request);
        $clients = [];
        /* @var $client Client */
        foreach ($clientsEntities as $client) {
            $clients[$client->getId()] = array_merge(
                // $this->getAllFieldValue($client),
                [
                    'name'=>$client->getName(),
                    'id'=>$client->getId()
                    // 'nbBase' => $client->countBases(),
                    // 'nbSnapshot' => $client->countSnapshot(),
                ]);
        }
        return new JsonResponse($clients);
    }
    /**
     * Get the all the client with an account in the app
     * @Route("/api/ressources/{clientId}/", name="bases")
     * @Route("/gestion/ressources/{clientId}/",name="gestion_bases")
     */
    public function getBases(Request $request, $clientId)
    {
        return $this->checkAccess($request, $clientId, null,null,function($clientEntity){
            /* @var $clientEntity Client */

            $basesEntities = $clientEntity->getBases();
            $bases = [];
            /* @var $client Client */
            foreach ($basesEntities as $base) {
                $bases[$base->getId()] = $this->getAllFieldValue($base);
            }
            usort($bases, function($a, $b) {return strcmp($a['name'],$b['name']);});
            return new JsonResponse($bases);
        });
    }

    /**
     * Get the all the client with an account in the app
     * @Route("/api/ressources/{clientId}/{baseId}/", name="instances")
     * @Route("/gestion/ressources/{clientId}/{baseId}/",name="gestion_instances")
     */
    public function getInstancesByBase(Request $request, $clientId, $baseId)
    {
       return $this->checkAccess($request, $clientId, $baseId,null,function($clientEntity,$baseEntity){
           return new JsonResponse($this->getInstancesRessource($baseEntity));
       });
    }

    /**
     * Get the all the client with an account in the app
     * @Route("/api/ressources/{clientId}/{baseId}/{instanceId}/", name="api_instanceContent")
     * @Route("/gestion/ressources/{clientId}/{baseId}/{instanceId}/",name="gestion_instanceContent")
     */
    public function getInstanceContentByInstance(Request $request, $clientId, $baseId, $instanceId)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        return $this->checkAccess( $request, $clientId , $baseId , $instanceId , function($clientEntity,$baseEntity,$instanceEntity){
            return new JsonResponse([
                'snapshots'=>$this->getSnapshotsRessource($instanceEntity),
                'advisory'=>$this->getAdvisoryRessource($instanceEntity)
            ]);
        });
    }
    /**
     * Get the all the client with an account in the app
     * @Route("/api/ressources/{clientId}/{baseId}/{instanceId}/snapshots/", name="api_snapshotsByInstance")
     * @Route("/gestion/ressources/{clientId}/{baseId}/{instanceId}/snapshots/",name="gestion_snapshotsByInstance")
     */
    public function getSnapshotsByInstance(Request $request, $clientId, $baseId, $instanceId)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        return $this->checkAccess( $request, $clientId , $baseId , $instanceId , function($clientEntity,$baseEntity,$instanceEntity){
            return new JsonResponse($this->getSnapshotsRessource($instanceEntity));
        });
    }/**
 * Get the all the client with an account in the app
 * @Route("/api/ressources/{clientId}/{baseId}/{instanceId}/advisory/", name="api_advisoryByInstance")
 * @Route("/gestion/ressources/{clientId}/{baseId}/{instanceId}/advisory/",name="gestion_advisoryByInstance")
 */
    public function getAdvisoryByInstance(Request $request, $clientId, $baseId, $instanceId)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        return $this->checkAccess( $request, $clientId , $baseId , $instanceId , function($clientEntity,$baseEntity,$instanceEntity){
            return new JsonResponse($this->getAdvisoryRessource($instanceEntity));
        });
    }
    /**
     * @Route("/api/ressources/{clientId}/{baseId}/{instanceId}/snapshots/{snapshotId}/", name="api_snapshotsRessource")
     * @Route("/gestion/ressources/{clientId}/{baseId}/{instanceId}/snapshots/{snapshotId}/",name="gestion_snapshotsRessource")
     */
    public function getSnapshotContentById(Request $request, $clientId, $baseId, $instanceId, $snapshotId)
    {
        //we do not write on session so dont need to lock it
        session_write_close();

        return $this->checkAccess($request,$clientId, $baseId, $instanceId,function($clientEntity, $baseEntity, $instanceEntity) use($snapshotId) {
            $em = $this->getDoctrine()->getManager();
            $snapshotEntity = $em->getRepository('AppBundle\Entity\Snapshots\Snapshot')->findOneById($snapshotId);
            /* @var $ressourceProvider AbstractRessource */
            $ressourceProvider = $this->get('ressource.snapshot');
            $ressourcesEntities = $ressourceProvider->getRessource($snapshotEntity);
            $ressources = [];
            foreach ($ressourcesEntities as $ressourceName=>$ressourceEntity){
                try{
                    $ressourceProvider = $this->get('ressource.'.$ressourceName);
                    $ressources[$ressourceName] = $ressourceProvider->getRessourceForSnapshot($snapshotEntity);
                }catch (ServiceNotFoundException $e){
                    $ressources[$ressourceName] = $ressourceEntity;
                }
            }
            return new JsonResponse($ressources);
        });
    }

    /**
     * @Route("/api/ressources/{clientId}/{baseId}/{instanceId}/snapshots/{snapshotId}/{className}/", name="api_RessourceByClass")
     * @Route("/gestion/ressources/{clientId}/{baseId}/{instanceId}/snapshots/{snapshotId}/{className}/",name="gestion_RessourceByClass")
     */
    public function getRessourceByClass(Request $request, $clientId, $baseId, $instanceId, $snapshotId, $className)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        $filter = json_decode($request->get("filter"),true);
        return $this->checkAccess($request,$clientId, $baseId, $instanceId,function($clientEntity, $baseEntity, $instanceEntity) use($snapshotId,$className,$filter) {
            $em = $this->getDoctrine()->getManager();
            $snapshotEntity = $em->getRepository('AppBundle\Entity\Snapshots\Snapshot')->findOneById($snapshotId);
            /* @var $ressourceProvider \AppBundle\RessourceProvider\AbstractClass\AbstractRessource */
            $ressourceProvider = $this->get('ressource.factory')->get($className);
            if( method_exists($ressourceProvider,"getRessourceForSnapshot")){
                $ressource = $ressourceProvider->getRessourceForSnapshot($snapshotEntity);
                $ressource = $ressourceProvider->filterRessource($filter,$ressource);
                return new JsonResponse($ressource);
            }else{
                return new JsonResponse('Ressource Not found', 404);
            }
        });
    }
    /**
     * @Route("/api/ressources/{clientId}/{baseId}/{instanceId}/snapshots/{snapshotId}/{className}/{propertyName}/", name="api_snapshotsRessourceByClassAndIndicator")
     * @Route("/gestion/ressources/{clientId}/{baseId}/{instanceId}/snapshots/{snapshotId}/{className}/{propertyName}/",name="gestion_snapshotsRessourceByClassAndIndicator")
     */
    public function getSnapshotsRessourceByClassAndIndicator(Request $request, $clientId, $baseId, $instanceId, $snapshotId, $className, $propertyName)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        $filter = json_decode($request->get("filter"),true);
        return $this->checkAccess($request,$clientId, $baseId, $instanceId,function($clientEntity, $baseEntity, $instanceEntity) use($snapshotId,$className,$filter,$propertyName) {
            $em = $this->getDoctrine()->getManager();
            $snapshotEntity = $em->getRepository('AppBundle\Entity\Snapshots\Snapshot')->findOneById($snapshotId);
            /* @var $ressourceProvider \AppBundle\RessourceProvider\AbstractClass\AbstractRessource */
            $ressourceProvider = $this->get('ressource.factory')->get($className);
            if( method_exists($ressourceProvider,"getRessourceForSnapshot")){
                $ressource = $ressourceProvider->getRessourceBySnapshotAndIndicator($snapshotEntity, $propertyName,$filter);
                $ressource = $ressourceProvider->filterRessource($filter,$ressource);
                return new JsonResponse($ressource);
            }else{
                return new JsonResponse('Ressource Not found', 404);
            }
        });
    }
    private function getAdvisoryRessource($instanceEntity){
        $advisories = [];
        /* $advisory Advisory */
        foreach ($instanceEntity->getAdvisory() as $advisory){
            $advisories[$advisory->getName()] = $advisory->getAdvisoryMap();
        }
        return $advisories;
    }
    private function getSnapshotsRessource($instanceEntity){
        $snapshotsEntities = $instanceEntity->getSnapshots();
        $snapshots = [];
        /* @var $client Client */
        foreach ($snapshotsEntities as $snapshot) {
            $snapshots[$snapshot->getId()] = $this->getAllFieldValue($snapshot);
        }
        return $snapshots;
    }
    /*
     * $baseEntity Base
     */
    private function getInstancesRessource($baseEntity){
        $instancesEntities = $baseEntity->getInstances();
        $instances = [];
        $em = $this->getDoctrine()->getManager();
        
        /* @var $instance Instance */
        foreach ($instancesEntities as $instance) {
            $qb = $em->createQueryBuilder();
            $queryResult = $qb->select("min(snapshot.startDate),max(snapshot.endDate)")
                    ->from('AppBundle\Entity\Snapshots\Snapshot',"snapshot")
                    ->where("snapshot.instance = :instance")
                    ->setParameter("instance",$instance->getId())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult()[0];
            $minDate = date_create_from_format("Y-m-d H:i:s",$queryResult[1]);
            $maxDate = date_create_from_format("Y-m-d H:i:s",$queryResult[2]);
            $queryResult = $qb->select("instanceMetadata.nbSnapshot")
                ->from('AppBundle\Entity\Metadata\InstanceMetadata',"instanceMetadata")
                ->where("instanceMetadata.instance = :instance")
                ->setParameter("instance",$instance->getId())
                ->setMaxResults(1)
                ->getQuery()
                ->getResult()[0];
            $nbSnapshot = $queryResult['nbSnapshot'];
            $instances[$instance->getId()] = array_merge($this->getAllFieldValue($instance),
                [
                    "config" => [
                        "server" => $this->getAllFieldValue($instance->getServerConfig()),
                        "sgbd" => $this->getAllFieldValue($instance->getSgbdConfig())
                    ],
                    // "nbSnapshots" => $instance->getSnapshots()->count(),
                    "nbSnapshots" => $nbSnapshot,
                    "minDate" => ($minDate !== null && $minDate !== false)?$minDate->format('d/m/Y H:i'):null,
                    "maxDate" => ($maxDate !== null && $maxDate !== false)?$maxDate->format('d/m/Y H:i'):null,
                    "version" => $baseEntity->getVersion(),
                ]
            );
        }
        return $instances;
    }

    private function getAllFieldValue($entity)
    {
        if ($entity === null) {
            return null;
        }
        try {
            $fields = [];
            $em = $this->getDoctrine()->getManager();
            $metadata = $em->getClassMetadata(get_class($entity));
            $fieldnames = $metadata->getFieldNames();
            $fieldnames = array_diff($fieldnames, ['id']);
            if (sizeof($fieldnames) === 0) {
                return null;
            }
            foreach ($fieldnames as $fieldName) {
                $fields[$fieldName] = $metadata->getFieldValue($entity, $fieldName);
                $fields['id'] = $entity->getId();
            }
            return $fields;
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * @Route("/api/users/", name="users")
     * @Route("/gestion/users/",name="gestion_users")
     */
    public function getAccounts(Request $request)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        $em = $this->getDoctrine()->getManager();
        $usersEntities = $em->getRepository('AppBundle\Entity\User')->findAll();
        $users = [];
        /* @var  $user \AppBundle\Entity\User */
        foreach ($usersEntities as $user) {
            $clients = [];
            foreach ($user->getClients() as $clientEntity) {
                $clients[$clientEntity->getId()] = $this->getAllFieldValue($clientEntity);
            }
            $users[$user->getId()] = [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'id' => $user->getId(),
                'isActive' => $user->getIsActive(),
                'clients' => $clients
            ];
        }
        return new JsonResponse($users);
    }

    /**
     * Get the all the client with an account in the app
     * @Route("/api/instanceMetadata/{instanceId}/", name="api_instanceMetadata")
     * @Route("/gestion/instanceMetadata/{instanceId}/",name="gestion_instanceMetadata")
     */
    public function getInstanceMetadata(Request $request, $instanceId)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        $em = $this->getDoctrine()->getManager();
        /* @var $instanceEntity Instance */
        $instanceEntity = $em->getRepository('AppBundle\Entity\Base\Instance')->findOneById($instanceId);
        /* @var $baseEntity Base */
        $baseEntity = $instanceEntity->getBase();
        /* @var $clientEntity Client */
        $clientEntity = $baseEntity->getClient();

        if ($this->getAuthorizedClients($request)->contains($clientEntity)) {
            // MODIF NB CPU
            $nbParamCpu = 0;
            $nbCpu = 0;
            $nbCores = 0;
            $ocpu = 0;
            $snapshotId = 0;
            $instanceId = $instanceEntity->getId();
            try{
                $snapshotSql =  "SELECT * FROM zenityService.snapshot WHERE instance_id = '$instanceId';";
                $sqlSnapshotStatement = $em->getConnection()->prepare($snapshotSql);
                $sqlSnapshotStatement->execute();
                $resultSnapshot = $sqlSnapshotStatement->fetchAll();
                if(count($resultSnapshot)){
                    $snapshotId = $resultSnapshot[0]["id"];
                }
            }catch(\Exception $e){
                $snapshotId = "No snapshot found for this instance";
            }
            if($snapshotId == 0){
                $snapshotId = "No snapshot found for this instance";
            }
            /*try{
                $cpuCountSql =  "SELECT * FROM zenityService.parameters WHERE snapshot_id = '$snapshotId' AND parameters.name='cpu_count';";
                $cpuCountSqlStatement = $em->getConnection()->prepare($cpuCountSql);
                $cpuCountSqlStatement->execute();
                $resultCpuCountSql = $cpuCountSqlStatement->fetchAll();
                if(count($resultCpuCountSql)){
                    $nbCpu = $resultCpuCountSql[0]["value"];
                }
            }catch(\Exception $e){
                $nbCpu = 15;
            }
            if($nbCpu == 0){
                $nbCpu = ($instanceEntity->getServerConfig())?$instanceEntity->getServerConfig()->getNbCpu():null;
            }*/

            // GET CPU PARAMETERS COUNT
            try{
                $cpuCountSql =  "SELECT * FROM zenityService.parameters WHERE snapshot_id = '$snapshotId' AND parameters.name='cpu_count';";
                $cpuCountSqlStatement = $em->getConnection()->prepare($cpuCountSql);
                $cpuCountSqlStatement->execute();
                $resultCpuCountSql = $cpuCountSqlStatement->fetchAll();
                if(count($resultCpuCountSql)){
                    $nbParamCpu = $resultCpuCountSql[0]["value"];
                }
            }catch(\Exception $e){
                $nbParamCpu = -15;
            }

            // GET CPU STATS COUNT
            $nbCpu = ($instanceEntity->getServerConfig())?$instanceEntity->getServerConfig()->getNbCpu():null;
            // GET CORES STATS COUNT
            $nbCores = ($instanceEntity->getServerConfig())?$instanceEntity->getServerConfig()->getNbCores():null;

            if($nbParamCpu > 0){ // if param cpu exists
                if($nbCores > 0){ // if cores exists
                    if(($nbCpu / $nbCores) == 2) { // and if the result by division is 2... hyperthreading detected
                        $ocpu = $nbParamCpu * ($nbCores / $nbCpu);
                        //$ocpu = 1;
                    }else // hyperthreading not detected
                    {
                        $ocpu = $nbCores;
                        //$ocpu = 2;
                    }
                }else{
                    if($nbCores == null || $nbCores == 0){ // if core is not defined, it means that hyperthreading is defined
                        $ocpu = $nbCpu / 2;
                        $nbCores = 0;
                        //$ocpu = 3;
                    }
                }
            }else{
                if($nbCores > 0){
                    if(($nbCpu / $nbCores) == 2) { // hyperthreading detected
                        $ocpu = $nbCores;
                        //$ocpu = 4;
                    }else // hyperthreading not detected
                    {
                        $ocpu = $nbCores;
                        //$ocpu = 5;
                    }
                }else{
                    if($nbCores == null || $nbCores == 0){ // if core is not defined, it means that hyperthreading is defined
                        $ocpu = $nbCpu / 2;
                        $nbCores = 0;
                        //$ocpu = 6;
                    }
                }               
            }

            // END MODIF CPU

            $edition;
            try{
                $edition = $this->getEdition($instanceId, $em);
            }catch(\Exception $e){
                $edition = null;
            }

            $metadata = [
                "clientName"=>  $clientEntity->getName(),
                "baseName" =>$baseEntity->getName(),
                "baseSgbd" =>$baseEntity->getSgbd(),
                "baseVersion"=>$baseEntity->getVersion(),
                "instanceName"=>$instanceEntity->getName(),
                //"fullEditionInfos"=>$instanceEntity->getFullEdition(),
                "instanceFirstSnapshot"=>$instanceEntity->getFirstSnapshotDate()->format("d/m/Y H:i"),
                "instanceLastSnapshot"=>$instanceEntity->getLastSnapshotDate()->format("d/m/Y H:i"),
                /*"instanceNbCpu"=>($instanceEntity->getServerConfig())?
                    $instanceEntity->getServerConfig()->getNbCpu():null,*/
                "instanceNbCpu"=>$nbParamCpu,
                "instanceOs"=>($instanceEntity->getServerConfig())?
                    $instanceEntity->getServerConfig()->getOs():null,
                "cpuCount"=>$nbCpu,
                "nbCores"=>$nbCores,
                "OCPU"=>$ocpu,
                "Edition"=>$edition->getName(),
                "serverName"=>$instanceEntity->getServerName(),
            ];
            return new JsonResponse($metadata);

        } else {
            return new JsonResponse('Access denied', 403);
        }
    }
    /**
     * @Route("/api/ressources/{clientId}/{baseId}/{instanceId}/snapshots/{snapshotId}/event_{className}/", name="api_eventsRessourceByClass")
     * @Route("/gestion/ressources/{clientId}/{baseId}/{instanceId}/snapshots/{snapshotId}/event_{className}/",name="gestion_eventsRessourceByClass")
     */
    public function getEventRessourceByWaitClass(Request $request, $clientId, $baseId, $instanceId, $snapshotId, $className){
        //we do not write on session so dont need to lock it
        $filter = $request->get('filter');
        return $this->checkAccess($clientId, $baseId, $instanceId,function($clientEntity, $baseEntity, $instanceEntity) use($snapshotId,$className,$filter) {
            $em = $this->getDoctrine()->getManager();
            $snapshotEntity = $em->getRepository('AppBundle\Entity\Snapshots\Snapshot')->findOneById($snapshotId);
            /* @var $ressourceProvider EventRessource */
            $ressourceProvider = $this->get('ressource.factory')->get('event');
            $ressource = $ressourceProvider->getEventsRessourceForSnapshotByClass($snapshotEntity,$className);
            if($filter && method_exists($ressourceProvider,"filterRessource" )){
                $ressource = $ressourceProvider->filterRessource($filter,$ressource);
            }
            return new JsonResponse($ressource);
        });
    }
    /**
     * @Route("/api/ressources/{clientId}/{baseId}/{instanceId}/{byol}/{licence}/cloudPricing", name="api_getCloudPricing")
     * @Route("/gestion/ressources/{clientId}/{baseId}/{instanceId}/{byol}/{licence}/cloudPricing",name="gestion_getCloudPricing")
     */
    public function getPricingForInstance(Request $request, $clientId, $baseId, $instanceId, $byol, $licence ){
        $em = $this->getDoctrine()->getManager();
        return new JsonResponse( 
            $this->get('cloud_pricing')
            ->computePricesForInstance(
                $em->getRepository('AppBundle\Entity\Base\Instance')->findOneById($instanceId), $byol, $licence
            )
        );
    }

     /**
     * @Route("/api/ressources/{clientId}/{baseId}/{instanceId}/options", name="api_getOptions")
     * @Route("/gestion/ressources/{clientId}/{baseId}/{instanceId}/options",name="gestion_getOptions")
     */
     public function getOptionsForInstance(Request $request, $clientId, $baseId, $instanceId ){
        $em = $this->getDoctrine()->getManager();
        return new JsonResponse( 
            $this->get('ressource.factory')->get('options')
            ->getRessource(
                $em->getRepository('AppBundle\Entity\Base\Instance')->findOneById($instanceId)->getOptions()
            ) 
        );
    }


    public function getEdition($instanceId, $em){
        //$instanceDetail = $em->get
        $optionCloudInstanceIdMax = 0;
        $packEdition = null;
        $instanceDetail =  "SELECT MAX(options_cloud_instance_id) FROM zenityService.instance_details WHERE instance_id = $instanceId;";
        $instanceDetailStatement = $em->getConnection()->prepare($instanceDetail);
        $instanceDetailStatement->execute();
        $resultinstanceDetail = $instanceDetailStatement->fetchAll();
        if(count($resultinstanceDetail)){
            $optionCloudInstanceIdMax = $resultinstanceDetail[0];
            $packEdition = $em->getRepository('AppBundle\Entity\CloudBase\PackEdition')->findOneById($optionCloudInstanceIdMax);
            return $packEdition;
            //return "ok";
        }else{
            return "no ok";
        }
    }

    public function getTarif($packEdition, $type, $byol, $em){
        $tarif;
        $packEditionId = $packEdition->getId();
        $tarifSql = "SELECT * FROM zenityService.tarifs WHERE pack_edition_id = '$packEditionId' AND tarifs.type_name = '$type' AND byol = '$byol' AND payg_or_flex = 'FLEX';";
        $tarifSqlStatement = $this->em->getConnection()->prepare($tarifSql);
        $tarifSqlStatement->execute();
        $resultTarifSql = $tarifSqlStatement->fetchAll();
        if(count($resultTarifSql)){
            $tarif = $resultTarifSql[0];
            return $tarif;
        }else{
            return null;
        }
    }
}