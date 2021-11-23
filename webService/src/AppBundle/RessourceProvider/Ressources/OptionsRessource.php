<?php

namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\ComplementaryFlow\Options;

use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class OptionsRessource extends AbstractEntityRessource
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
            $entity = new Options();
        }
        if(array_key_exists("PRODUCT_USAGE",$jsonDecoded))$entity->setProductUsage($jsonDecoded["PRODUCT_USAGE"]);
        if(array_key_exists("INSTANCE_PARAMETER",$jsonDecoded))$entity->setInstanceParameters($jsonDecoded["INSTANCE_PARAMETER"]);
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
        if (!$entity) return [];
        return [
            "productUsage"=>$entity->getProductUsage(),
            "instanceParameters"=>$entity->getInstanceParameters()
        ];
    }
    /**
     * @param $snapshot
     * @return mixed
     */ 
    public function getRessourceForSnapshot($snapshot)
    {
        return $this->getRessource($snapshot->getInstance()->getOptions());
    }
    
}