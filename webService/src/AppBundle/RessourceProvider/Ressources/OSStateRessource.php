<?php
use AppBundle\Entity\Base\Instance;
use AppBundle\Entity\Snapshots\Snapshot;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Snapshots\EfficiencyIndicator;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\Snapshots\OSState;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class OSStateRessource extends AbstractEntityRessource
{
    /**
     * @return mixed
     */
    function getMetadata()
    {
        return [
            "title"=>"oSState",
            "properties"=>[
                  'memoryUsage'=>[
                      "type"=>"number"
                  ],
                  'userTime'=>[
                      "type"=>"number"
                  ],
                  'idleTime'=>[
                      "type"=>"number"
                  ],
                  'busyTime'=>[
                      "type"=>"number"
                  ],
                  'load'=>[
                      "type"=>"number"
                  ],
                  'sysTime'=>[
                      "type"=>"number"
                  ],
              ]
        ];
    }

    /**
     * @param $entity OSState
     * @param String $json
     * @return OSState
     */
    public function updateFromJson($entity,$json){
        $jsonDecoded = json_decode($json,true);
        if(!$entity){
            $entity = new OSState();
        }
        $entity->setMemoryUsage(
            (isset($jsonDecoded['memoryUsage']))?$jsonDecoded['memoryUsage']:null);
        $entity->setUserTime(
            (isset($jsonDecoded['userTime']))?$jsonDecoded['userTime']:null);
        $entity->setIdleTime(
            (isset($jsonDecoded['idleTime']))?$jsonDecoded['idleTime']:null);
        $entity->setBusyTime(
            (isset($jsonDecoded['busyTime']))?$jsonDecoded['busyTime']:null);
        $entity->setLoad(
            (isset($jsonDecoded['load']))?$jsonDecoded['load']:null);
        $entity->setSysTime(
            (isset($jsonDecoded['sysTime']))?$jsonDecoded['sysTime']:null);
        return $entity;
    }

    /**
     * @param $entity OSState
     * @return array
     */
    public function getRessource($entity){
        if ($entity){
            return [
                'memoryUsage'=>$entity->getMemoryUsage(),
                'userTime'=>$entity->getUserTime(),
                'idleTime'=>$entity->getIdleTime(),
                'busyTime'=>$entity->getBusyTime(),
                'load'=>$entity->getLoad(),
                'sysTime'=>$entity->getSysTime(),
                'usage'=>$entity->getBusyTime()/($entity->getIdleTime()+$entity->getBusyTime())
            ];
        }
        else{
            return null;
        }
    }
    /**
     * @param $snapshot Snapshot
     * @return array
     */
    public function getRessourceForSnapshot($snapshot){
        return [$this->getRessource($snapshot->getOsState())];
    }
}
