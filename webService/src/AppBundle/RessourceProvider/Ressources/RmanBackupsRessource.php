<?php

namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\ComplementaryFlow\Rman;
use AppBundle\Entity\ComplementaryFlow\RmanBackups;
use AppBundle\Entity\ComplementaryFlow\RmanParameters;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class RmanBackupsRessource extends AbstractEntityRessource
{
    /**
     * @param $entity Rman
     * @param String $json
     * @return mixed
     */
    public function updateFromJson($entity, $json)
    {
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
            "startDate"=>$entity->getStartDate(),
            "endDate"=>$entity->getEndDate(),
            "type"=>$entity->getType(),
            "outputDeviceType"=>$entity->getOutputDevice(),
            "inputSize"=>$entity->getInputSize(),
            "outputSize"=>$entity->getOutputSize(),
        ];
    }
    /**
     * @param $snapshot
     * @return mixed
     */ 
    public function getRessourceForSnapshot($snapshot)
    {
        $this->getRessource($snapshot->getInstance()->getRmanbyDate($snapshot->getStartDate()));
    }

}