<?php

namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\ComplementaryFlow\PerfStatInfo;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;
use AppBundle\RessourceProvider\AbstractClass\AbstractRessource;

class PerfStatInfoRessource extends AbstractEntityRessource
{
    /**
     * @param $entity PerfStatInfo
     * @param String $json
     * @return mixed
     */
    public function updateFromJson($entity, $json)
    {
        if (!$entity) {
            $entity = new PerfStatInfo();
        }
        $jsonDecoded = json_decode($json, true);
        $entity->setIdleEventCount(
            (isset($jsonDecoded['IDLE_EVENT_COUNT'])) ? $jsonDecoded['IDLE_EVENT_COUNT'] : $entity->getIdleEventCount());
        $entity->setLastSnapTime(
            (isset($jsonDecoded['LAST_SNAP_TIME'])) ? new \DateTime($jsonDecoded['LAST_SNAP_TIME']) : $entity->getLastSnapTime());
        $entity->setPurgeJobStatus(
            (isset($jsonDecoded['PURGE_JOB_STATUS'])) ? $jsonDecoded['PURGE_JOB_STATUS'] : false);
        $entity->setSequenceCacheSize(
            (isset($jsonDecoded['SEQUENCE_CACHE_SIZE('])) ? $jsonDecoded['SEQUENCE_CACHE_SIZE'] : $entity->getSequenceCacheSize());
        $entity->setSnapshotJobStatus(
            (isset($jsonDecoded['SNAPSHOT_JOB_STATUS'])) ? $jsonDecoded['SNAPSHOT_JOBS_TATUS'] : false);
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
     * @param $entity PerfStatInfo
     * @return mixed
     */
    public function getRessource($entity)
    {
        $isOk = false;
        if($entity->getIdleEventCount() === 166
        && $entity->getLastSnapTime()->dif(new \DateTime()) <= 2
        && $entity->getPurgeJobStatus() === true
        && $entity->getSnapshotJobStatus() === true
        && $entity->getSequenceCacheSize() === 0 )
            $isOk = true;
        return [
            'isOk'=>$isOk,
            'idleEventCount'=>$entity->getIdleEventCount(),
            'lastSnapTime'=>$entity->getLastSnapTime(),
            'purgeJobStatus'=>$entity->getPurgeJobStatus(),
            'sequenceCacheSize'=>$entity->getSequenceCacheSize(),
            'snapshotJobStatus'=>$entity->getSnapshotJobStatus()
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