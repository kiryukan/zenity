<?php


namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\ComplementaryFlow\Lock;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class LockRessource extends AbstractEntityRessource
{
    /**
     * @param $entity Lock
     * @param String $json
     * @return mixed
     */
    public function updateFromJson($entity, $json)
    {
        if (!$entity) {
            $entity = new Lock();
        }
        $jsonDecoded = json_decode($json, true  );

        $entity->setTimestamp(
            isset($jsonDecoded['TIMESTAMP'])?date_create_from_format("Y-m-d H:i",$jsonDecoded['TIMESTAMP']):null
        );
        $entity->setUsername(
            isset($jsonDecoded['USERNAME'])?$jsonDecoded['USERNAME']:null
        );
        $entity->setModule(
            isset($jsonDecoded['MODULE'])?$jsonDecoded['MODULE']:null
        );
        $entity->setMachine(
            isset($jsonDecoded['MACHINE'])?$jsonDecoded['MACHINE']:null
        );
        $entity->setTerminal(
            isset($jsonDecoded['TERMINAL'])?$jsonDecoded['TERMINAL']:null
        );
        $entity->setProgram(
            isset($jsonDecoded['PROGRAM'])?$jsonDecoded['PROGRAM']:null
        );
        $entity->setSqlId(
            isset($jsonDecoded['SQL_ID'])?$jsonDecoded['SQL_ID']:null
        );
        $entity->setSqlChildNumber(
            isset($jsonDecoded['SQL_CHILD_NUMBER'])?$jsonDecoded['SQL_CHILD_NUMBER']:null
        );
        $entity->setDuree(
            isset($jsonDecoded['DUREE'])?$jsonDecoded['DUREE']:null
        );
        $entity->setWaitClass(
            isset($jsonDecoded['WAIT_CLASS'])?$jsonDecoded['WAIT_CLASS']:null
        );
        $entity->setEvent(
            isset($jsonDecoded['EVENT'])?$jsonDecoded['EVENT']:null
        );
        $entity->setObjectOwner(
            isset($jsonDecoded['OBJECT_OWNER'])?$jsonDecoded['OBJECT_OWNER']:null
        );
        $entity->setObjectName(
            isset($jsonDecoded['OBJECT_NAME'])?$jsonDecoded['OBJECT_NAME']:null
        );
        $entity->setObjectType(
            isset($jsonDecoded['OBJECT_TYPE'])?$jsonDecoded['OBJECT_TYPE']:null
        );
        $entity->setOsuser(
            isset($jsonDecoded['OSUSER'])?$jsonDecoded['OSUSER']:null
        );
        $entity->setStatus(
            isset($jsonDecoded['STATUS'])?$jsonDecoded['STATUS']:null
        );
        $entity->setModeHeld(
            isset($jsonDecoded['MODE_HELD'])?$jsonDecoded['MODE_HELD']:null
        );
        $entity->setLockId(
            isset($jsonDecoded['LOCK_ID'])?$jsonDecoded['LOCK_ID']:null
        );
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
     * @param $entity Lock
     * @return mixed
     */
    public function getRessource($entity)
    {
        return [
            'timestamp'=>$entity->getTimestamp(),
            'username'=>$entity->getUsername(),
            'module'=>$entity->getModule(),
            'machine'=>$entity->getMachine(),
            'terminal'=>$entity->getTerminal(),
            'program'=>$entity->getProgram(),
            'sql_id'=>$entity->getSqlId(),
            'sql_child_number'=>$entity->getSqlChildNumber(),
            'duree'=>$entity->getDuree(),
            'wait_class'=>$entity->getWaitClass(),
            'event'=>$entity->getEvent(),
            'object_owner'=>$entity->getObjectOwner(),
            'object_name'=>$entity->getObjectName(),
            'object_type'=>$entity->getObjectType(),
            'osuser'=>$entity->getOsuser(),
            'status'=>$entity->getStatus(),
            'mode_held'=>$entity->getModeHeld(),
            'lock_id'=>$entity->getLockId()
        ];
    }

    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {
        $ressource = [];
        /* @var $lock Lock*/
        foreach($snapshot->getLocks() as $lock){
            $ressource[$lock->getLockId()] =  $this->getRessource($lock);
        }
        return $ressource;
    }

}