<?php
namespace AppBundle\Services;

use AppBundle\Entity\AuditEngine\Filter;
use AppBundle\Entity\Snapshots\Note;
use AppBundle\RessourceProvider\RessourceFactory;
use AppBundle\Entity\AuditEngine\Advice;
use AppBundle\Entity\AuditEngine\Indicator;
use AppBundle\Entity\AuditEngine\NoteEngine;
use AppBundle\Entity\Snapshots\Snapshot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;

class CloudPricing
{
    /**
     * @var $em EntityManager
     */
    private $em;
    private $logger;
    private $ressourceFactory;
    public function __construct(EntityManager $entityManager,RessourceFactory $ressourceFactory ,Logger $logger)
    {
        $this->ressourceFactory = $ressourceFactory;
        $this->em = $entityManager;
        $this->logger = $logger;
    }
    private function calculate_string( $mathString,$n ){
        $mathString = str_replace("n",$n,$mathString);
        $mathString = trim($mathString);     // trim white spaces
        $mathString = preg_replace('[^0-9\+-\*\/\(\) ]', '', $mathString); 
        $compute = create_function("", "return (" . $mathString . ");" );
        return 0 + $compute();
    }
    private function isValidCpuCount($cpuCount,$cpuCountRule,$minCpu=1,$maxCpu=512){
        if($cpuCount < $minCpu ) $cpuCount = $minCpu;
        if(!$cpuCountRule || $cpuCountRule == "") $cpuCountRule = "1";
        while(! $this->calculate_string($cpuCountRule,$cpuCount) && $cpuCount < $maxCpu){
            $cpuCount ++ ;
        }
        if($this->calculate_string($cpuCountRule,$cpuCount) && $cpuCount < $maxCpu){
            return [
                "isValid"=>true,
                "nbCpu"=>$cpuCount
            ];
        }else{
            return [
                "isValid"=>false,
                "nbCpu"=>$cpuCount
            ];
        }
    }
    public function computePricesForInstance($instance, $byol, $type)
    {
        $cloudInstances = $this->em->getRepository('AppBundle\Entity\CloudBase\CloudInstance')->findAll();
        // ---------------------------
        // ---- MODIF CPU COUNT ------
        // ---------------------------
        /* @var $instanceEntity Instance */
        $instanceEntity = $this->em->getRepository('AppBundle\Entity\Base\Instance')->findOneById($instance->getId());
        /* @var $baseEntity Base */
        $baseEntity = $instanceEntity->getBase();
        /* @var $clientEntity Client */
        $clientEntity = $baseEntity->getClient();
            $nbParamCpu = 0;
            $nbCpu = 0;
            $nbCores = 0;
            $ocpu = 0;
            $snapshotId = 0;
            $instanceId = $instance->getId();
            try{
                $snapshotSql =  "SELECT * FROM zenityService.snapshot WHERE instance_id = '$instanceId';";
                $sqlSnapshotStatement = $this->em->getConnection()->prepare($snapshotSql);
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
            try{
                $cpuCountSql =  "SELECT * FROM zenityService.parameters WHERE snapshot_id = '$snapshotId' AND parameters.name='cpu_count';";
                $cpuCountSqlStatement = $this->em->getConnection()->prepare($cpuCountSql);
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
        // -------------------------------
        // ---- END MODIF CPU COUNT ------
        // -------------------------------
        $edition;
        try{
            $edition = $this->getEdition($instanceId, $this->em);
        }catch(\Exception $e){}

        //$tarif = $this->getTarif($edition->getId(), $type, $byol, $this->em);
        $tarif = $this->getTarif($edition->getId(), $type, $byol, $this->em);

        //$instanceStorageCapacity = $instance->getServerConfig()->getInstanceStorageCapacity();
        $price = 0.4667;
        $instanceStorageCapacity = 150;
        foreach($cloudInstances as $cloudInstance){
            $costPerCpu = $cloudInstance->getCostPerCpu();
            $licences = [];
            /*foreach($cloudInstance->getLicencePrices() as $licence){
                $licences[] = [
                    "name"=>$licence->getName(),
                    "value"=>$licence->getValue(),
                ];
            }
            $selectedLicence = null;
            foreach ($licences as $key=>$licence){
                // A changer par la license à sélectionner
                if($licence["name"] == "SE"){
                    $selectedLicence = $licence;
                    $licences[$key]["selected"] = true;
                }
            }*/
            $baseCost = $cloudInstance->getBaseCost();
            $storageToPay = $instanceStorageCapacity - $cloudInstance->getBaseStorageCapacity();
            $costPerGo = $cloudInstance->getCostPerGo();
            $storageCost = $storageToPay * $costPerGo;
            //$licenceCost = $selectedLicence?$selectedLicence["value"]:0;
            $licenceCost = 0;
            $validCpuCount = $this->isValidCpuCount($nbCpu,$cloudInstance->getCpuCountRule(),$cloudInstance->getMinCpuCount(),$cloudInstance->getMaxCpuCount());
            $isValidCpuCount = $validCpuCount["isValid"];
            // TEST -- $nbCpu = $validCpuCount["nbCpu"];
            if($isValidCpuCount){
                $pricing[$cloudInstance->getName()] = [
                    "providerName"=>$cloudInstance->getProvider()->getName(), // ok
                    "technicalInfos"=>$cloudInstance->getTechnicalInfos(), // ok
                    "requiredStorage"=>$storageToPay , // ok
                    //"availableLicences"=>$licences,
                    //"selectedLicence"=>$selectedLicence,
                    "costPerCpu"=>$costPerCpu, // ok
                    "baseCost"=>$baseCost, // ok?
                    "storageCost"=>$storageCost, // ok?
                    "cpuCountRule"=>$cloudInstance->getCpuCountRule(), // ok
                    //"Edition"=>$edition->getName(),
                    //"cpuCount"=>$nbCpu,
                    //"total"=>round($baseCost + $licenceCost + $nbCpu * $costPerCpu + $storageCost,2)
                    "total"=>round($ocpu * $price * 24 * 31,2)
                ];
            }

        }
        $minimumKey = key($pricing);
        $minimum = $pricing[$minimumKey]['total'];
        foreach ($pricing as $key=>$cloudInstance){
            if($cloudInstance["total"] < $minimum){
                $minimumKey = $key;
                $minimum = $cloudInstance["total"];
            }
        }
        if($minimumKey)$pricing[$minimumKey]['minimum'] = true;
        return $pricing;
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
