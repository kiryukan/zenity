<?php

namespace AppBundle\RessourceProvider\Ressources;

use AppBundle\Entity\Base\Instance;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Snapshots\EfficiencyIndicator;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use AppBundle\Entity\Snapshots\InstanceState;

class InstanceStateRessource extends AbstractEntityRessource
{
    /**
     * @return mixed
     */
    function getMetadata()
    {
        return [
            "title"=>"instanceState",
            "properties"=>[
                'nbSession'=>[
                    "type"=>"number"
                ],
                'redoLogSpaceRequests'=>[
                    "type"=>"number"
                ],
                'redoLogSpaceWaitTime'=>[
                    "type"=>"number"
                ],
                'sqlAreaEvicted'=>[
                    "type"=>"number"
                ],
                'sqlAreaPurged'=>[
                    "type"=>"number"
                ],
                'userCall'=>[
                    "type"=>"number"
                ],
                'userCommit'=>[
                    "type"=>"number"
                ],
                'userIOWaitTime'=>[
                    "type"=>"number"
                ],
            ]
        ];
    }
    /**
     * @param $entity InstanceState
     * @return array
     */
    public function getRessource($entity){
        return [
            'redoLogSpaceRequests'=>$entity->getRedoLogSpaceRequests(),
            'redoLogSpaceWaitTime'=>$entity->getRedoLogSpaceWaitTime(),
            'sqlAreaEvicted'=>$entity->getSqlAreaEvicted(),
            'sqlAreaPurged'=>$entity->getSqlAreaPurged(),
            'userCall'=>$entity->getUserCall(),
            'userCommit'=>$entity->getUserCommit(),
            'nbSession'=>$entity->getNbSession(),
            'userIOWaitTime'=>$entity->getUserIOWaitTime(),
        ];
    }

    /**
     * @param $json
     * @param $entity InstanceState
     */
    public function updateFromJson($entity,$json)
    {
        $jsonDecoded = json_decode($json,true);
        if(!$entity){
            $entity = new InstanceState();
        }
        $entity->setRedoLogSpaceRequests(
            (isset($jsonDecoded['redoLogSpaceRequests']))?$jsonDecoded['redoLogSpaceRequests'] : null );
        $entity->setRedoLogSpaceWaitTime(
            (isset($jsonDecoded['redoLogSpaceWaitTime']))?$jsonDecoded['redoLogSpaceWaitTime'] : null );
        $entity->setSqlAreaEvicted(
            (isset($jsonDecoded['sqlAreaEvicted']))?$jsonDecoded['sqlAreaEvicted'] : null );
        $entity->setSqlAreaPurged(
            (isset($jsonDecoded['sqlAreaPurged']))?$jsonDecoded['sqlAreaPurged'] : null );
        $entity->setUserCall(
            (isset($jsonDecoded['userCall']))?$jsonDecoded['userCall'] : null );
        $entity->setUserCommit(
            (isset($jsonDecoded['userCommit']))?$jsonDecoded['userCommit'] : null );
        $entity->setNbSession(
            (isset($jsonDecoded['nbSession']))?$jsonDecoded['nbSession'] : null );
        $entity->setUserIOWaitTime(
            (isset($jsonDecoded['userIOWaitTime']))?$jsonDecoded['userIOWaitTime'] : null );
        return $entity;
    }

    /**
     * @param $snapshot Snapshot
     * @return array
     */
    public function getRessourceForSnapshot($snapshot){
        return [$this->getRessource($snapshot->getInstanceState())];
    }
}