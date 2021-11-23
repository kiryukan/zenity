<?php

namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\ComplementaryFlow\TablespaceSegInfo;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class TablespaceSegInfoRessource extends AbstractEntityRessource
{
    /**
     * @param $entity TablespaceSegInfo
     * @param String $json
     * @return mixed
     */
    public function updateFromJson($entity, $json)
    {
        $jsonDecoded = json_decode($json,true);
        if(!$entity){
            $entity = new TablespaceSegInfo();
        }
        $entity->setName(
            (isset($jsonDecoded['TABLESPACE_NAME']))?strtolower($jsonDecoded['TABLESPACE_NAME']):null);
        $entity->setTimestamp(
            isset($jsonDecoded['TIMESTAMP'])?date_create_from_format("Y-m-d H:i:s",$jsonDecoded['TIMESTAMP']):null
        );
        $entity->setTotalBytes(
            isset($jsonDecoded['TOTAL_BYTES'])?$jsonDecoded['TOTAL_BYTES']:null
        );
        if(isset($jsonDecoded["SEGMENTS"]) && count($jsonDecoded["SEGMENTS"]) > 0){
            foreach ($jsonDecoded["SEGMENTS"] as $segment){
                $segment = $this->ressourceFactory->get("segment")->updateFromJson(null,json_encode($segment));
                $entity->addSegment($segment);
            }
        }
        return $entity;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        // TODO: Implement getMetadata() method.
    }

    /**
     * @param $entity TablespaceSegInfo
     * @return mixed
     */
    public function getRessource($entity)
    {
        return [
            'timestamp'=>$entity->getTimestamp(),
            'name'=>$entity->getName(),
            'totalBytes'=>$entity->getTotalBytes()
        ];
    }
    /**
     * @param $snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {
        // TODO: Implement getRessourceForSnapshot() method.
    }

}