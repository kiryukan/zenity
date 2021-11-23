<?php


namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\ComplementaryFlow\Segment;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class SegmentRessource extends AbstractEntityRessource
{
    /**
     * @param $entity Segment
     * @param String $json
     * @return mixed
     */
    public function updateFromJson($entity, $json)
    {

        $jsonDecoded = json_decode($json,true);
        if(!$entity){
            $entity = new Segment();
        }
        $entity->setName(
            (isset($jsonDecoded['SEGMENT_NAME']))?$jsonDecoded['SEGMENT_NAME']:null);
        $entity->setType(
            (isset($jsonDecoded['SEGMENT_TYPE']))?$jsonDecoded['SEGMENT_TYPE']:null);
        $entity->setBytes(
            (isset($jsonDecoded['BYTES']))?$jsonDecoded['BYTES']:null);
        $entity->setOwner(
            (isset($jsonDecoded['OWNER']))?$jsonDecoded['OWNER']:null);

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
     * @param $entity Segment
     * @return mixed
     */
    public function getRessource($entity)
    {
        return [
            'type'=>$entity->getName(),
            'name'=>$entity->getType(),
            'bytes'=>$entity->getBytes(),
            'owner'=>$entity->getOwner()

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