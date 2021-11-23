<?php


namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\ComplementaryFlow\AlertLog;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class AlertLogRessource extends AbstractEntityRessource
{

    /**
     * @param $entity AlertLog
     * @param String $json
     * @return mixed
     */
    public function updateFromJson($entity, $json)
    {
        if (!$entity) {
            $entity = new AlertLog();
        }
        $jsonDecoded = json_decode($json, true);
        $entity->setCode(
            (isset($jsonDecoded['code'])) ? $jsonDecoded['code'] : $entity->getCode());
        $entity->setDate(
            (isset($jsonDecoded['date'])) ? new \DateTime($jsonDecoded['date']) : $entity->getDate());
        $entity->setText(
            (isset($jsonDecoded['text'])) ? $jsonDecoded['text'] : $entity->getText());
        return $entity;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
    }

    /**
     * @param $entity AlertLog
     * @return mixed
     */
    public function getRessource($entity)
    {
        return [
            'date'=>$entity->getDate(),
            'code'=>$entity->getCode(),
            'text'=>$entity->getText()
        ];
    }

    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {
        return $this->getRessource($snapshot->getInstance()->getPerfStatInfo());
    }
}