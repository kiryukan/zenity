<?php

namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\ComplementaryFlow\Rman;
use AppBundle\Entity\ComplementaryFlow\RmanBackups;
use AppBundle\Entity\ComplementaryFlow\RmanParameters;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class RmanRessource extends AbstractEntityRessource
{
    /**
     * @param $entity Rman
     * @param String $json
     * @return mixed
     */
    public function updateFromJson($entity, $json)
    {
        $jsonDecoded = json_decode($json,true);
        if(!$entity){
            $entity = new Rman();
        }
        foreach($jsonDecoded["LAST_24H_RMAN_BACKUPS"] as $backup){
            $backupEntity = new RmanBackups();
            $backupEntity->setStartDate($backup["START_TIME"]);
            $backupEntity->setEndDate($backup["END_TIME"]);
            $backupEntity->setType($backup["BACKUP_TYPE"]);
            $backupEntity->setOutputDevice($backup["OUTPUT_DEVICE_TYPE"]);
            $backupEntity->setInputSize($backup["INPUT_BYTES"]);
             $backupEntity->setOutputSize($backup["OUTPUT_BYTES"]);
            $entity->addBackup($backupEntity);
        }
        foreach($jsonDecoded["RMAN_CONFIGURATION"] as $conf){
            $rmanParameters = new RmanParameters();
            $rmanParameters->setName($backup["NAME"]);
            $rmanParameters->setValue($backup["VALUE"]);
            $entity->addParameter($rmanParameters);
        }
        return $entity;
    }
    /**
     * @return mixed
     */
    public function getMetadata(){}

    /**
     * @param $entity TablespaceSegInfo
     * @return mixed
     */
    public function getRessource($entity)
    {
        return [
            "parameters"=>$entity->getParameters(),
        ];
    }
    /**
     * @param $snapshot
     * @return mixed
     */ 
    public function getRessourceForSnapshot($snapshot)
    {
        
    }

}