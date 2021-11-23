<?php


namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\Snapshots\AdvisorySnapshot;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class AdvisoryRessource extends AbstractEntityRessource
{
    /**
     * @return mixed
     */
    function getMetadata()
    {
        return [
            "title"=>"instanceState",
            "properties"=>[
                'sga memory_0.9_dbTimeFctr'=>[
                    "type"=>"number"
                ],
                'sga memory_1.1_dbTimeFctr'=>[
                    "type"=>"number"
                ],
                'sga memory_1.2_dbTimeFctr'=>[
                    "type"=>"number"
                ],
                'sga memory_1.3_dbTimeFctr'=>[
                    "type"=>"number"
                ],
                'pga memory_0.9_cacheHit%'=>[
                    "type"=>"number"
                ],
                'pga memory_1.1_cacheHit%'=>[
                    "type"=>"number"
                ],
                'pga memory_1.2_cacheHit%'=>[
                    "type"=>"number"
                ],
                'pga memory_1.3_cacheHit%'=>[
                    "type"=>"number"
                ],
                'buffer pool_0.9_phReadFctr'=>[
                    "type"=>"number"
                ],
                'buffer pool_1.1_phReadFctr'=>[
                    "type"=>"number"
                ],
                'buffer pool_1.2_phReadFctr'=>[
                    "type"=>"number"
                ],
                'buffer pool_1.3_phReadFctr'=>[
                    "type"=>"number"
                ],
                'shared pool_0.9_timeSavedFctr'=>[
                    "type"=>"number"
                ],
                'shared pool_1.1_timeSavedFctr'=>[
                    "type"=>"number"
                ],
                'shared pool_1.2_timeSavedFctr'=>[
                    "type"=>"number"
                ],
                'shared pool_1.3_timeSavedFctr'=>[
                    "type"=>"number"
                ]

            ]
        ];
    }
    /**
     * transform a valid json into $this
     * @param $entity AdvisorySnapshot
     * @param $json String
     */
    public function updateFromJson($entity,$json){
        $jsonDecoded = json_decode($json,true);
        if(!$entity){
            $entity = new AdvisorySnapshot();
        }
        $entity->setName(
            (isset($jsonDecoded['name']))?$jsonDecoded['name']:$entity->getName());
        $entity->setAdvisoryMap(
            (isset($jsonDecoded['advisoryMap']))?$jsonDecoded['advisoryMap']:$entity->getAdvisoryMap());
        return $entity;
    }

    /**
     * @param $entity AdvisorySnapshot
     * @return array
     */
    public function getRessource($entity){
        $advisoryMap =  $entity->getAdvisoryMap();
        return [
            'name'=>$entity->getName(),
            '0.9'=>(key_exists('0.9', $advisoryMap) && $advisoryMap['0.9']['gainFctr'] != 0)?$advisoryMap['0.9']['gainFctr']:null,
            '1.1'=>(key_exists('1.1', $advisoryMap) && $advisoryMap['1.1']['gainFctr'] != 0)?$advisoryMap['1.1']['gainFctr']:null,
            '1.2'=>(key_exists('1.2', $advisoryMap) && $advisoryMap['1.2']['gainFctr'] != 0)?$advisoryMap['1.2']['gainFctr']:null,
            '1.3'=>(key_exists('1.3', $advisoryMap) && $advisoryMap['1.3']['gainFctr'] != 0)?$advisoryMap['1.3']['gainFctr']:null,
        ];
    }
    /**
     * @param $snapshot Snapshot
     * @return array
     */
    public function getRessourceForSnapshot($snapshot){
        $ressource = [];
        foreach ($snapshot->getAdvisory()  as $advisory){
            $ressource[$advisory->getName()] = $this->getRessource($advisory);
        }
        return $ressource;
    }
    public function getRessourceBySnapshotAndIndicator($snapshot,$indicatorName,$filterArray=null){
        $ressources = $this->getRessourceForSnapshot($snapshot);
        if($filterArray){
            $ressources = $this->filterRessource($filterArray,$ressources);
        }
        $areaName = explode('_',$indicatorName)[0];
        $sizeFctr = explode('_',$indicatorName)[1];

        return (isset($ressources[$areaName][$sizeFctr]))?[$ressources[$areaName][$sizeFctr]]:[null];
    }
}