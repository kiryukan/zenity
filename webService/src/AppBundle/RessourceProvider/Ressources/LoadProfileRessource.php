<?php
use AppBundle\Entity\Base\Instance;
use AppBundle\Entity\Snapshots\Snapshot;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Snapshots\EfficiencyIndicator;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\Snapshots\LoadProfile;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class LoadProfileRessource extends AbstractEntityRessource
{
    /**
     * @return mixed
     */
    function getMetadata()
    {
        return [
            'title'=>"loadProfile",
            "properties"=>[


                'blockChange'=>[
                    "type"=>"number"
                ],
                'dbTime'=>[
                    "type"=>"number"
                ],
                'execute'=>[
                    "type"=>"number"
                ],
                'hardParses'=>[
                    "type"=>"number"
                ],
                'logons'=>[
                    "type"=>"number"
                ],
                'parses'=>[
                    "type"=>"number"
                ],
                'physicalRead'=>[
                    "type"=>"number"
                ],
                'physicalWrite'=>[
                    "type"=>"number"
                ],
                'transactions'=>[
                    "type"=>"number"
                ],
                'userCalls'=>[
                    "type"=>"number"
                ],
                'rollbacks'=>[
                    "type"=>"number"
                ],
                'redoSize'=>[
                    "type"=>"number"
                ],

            ]

        ];
    }

    /**
     * @param $entity LoadProfile
     * @param String $json
     * @return LoadProfile
     */
    public function updateFromJson($entity,$json){
        $jsonDecoded = json_decode($json,true);
        $jsonDecoded = json_decode($json,true);
        if(!$entity){
            $entity = new LoadProfile();
        }
        $entity->setBlockChange(
            (isset($jsonDecoded['blockChange']))?$jsonDecoded['blockChange']: null);
        $entity->setExecute(
            (isset($jsonDecoded['execute']))?$jsonDecoded['execute']: null);
        $entity->setTransactions(
            (isset($jsonDecoded['transactions']))?$jsonDecoded['transactions']: null);
        $entity->setUserCalls(
            (isset($jsonDecoded['userCalls']))?$jsonDecoded['userCalls']: null);
        $entity->setParses(
            (isset($jsonDecoded['parses']))?$jsonDecoded['parses']: null);
        $entity->setPhysicalRead(
            (isset($jsonDecoded['physicalRead']))?$jsonDecoded['physicalRead']: null);
        $entity->setRollbacks(
            (isset($jsonDecoded['rollbacks']))?$jsonDecoded['rollbacks']: null);
        $entity->setPhysicalWrite(
            (isset($jsonDecoded['physicalWrite']))?$jsonDecoded['physicalWrite']: null);
        $entity->setRedoSize(
            (isset($jsonDecoded['redoSize']))?$jsonDecoded['redoSize']: null);
        $entity->setHardParses(
            (isset($jsonDecoded['hardParses']))?$jsonDecoded['hardParses']: null);
        $entity->setLogons(
            (isset($jsonDecoded['logons']))?$jsonDecoded['logons']: null);
        $entity->setDbTime(
            (isset($jsonDecoded['dbTime']))?$jsonDecoded['dbTime']: null);
        return $entity;
    }

    /**
     * @param $entity LoadProfile
     * @return array
     */
    public function getRessource($entity){
        return [
            'blockChange'=>$entity->getBlockChange(),
            'execute'=>$entity->getExecute(),
            'transactions'=>$entity->getTransactions(),
            'userCalls'=>$entity->getUserCalls(),
            'parses'=>$entity->getParses(),
            'physicalRead'=>$entity->getPhysicalRead(),
            'rollbacks'=>$entity->getRollbacks(),
            'physicalWrite'=>$entity->getPhysicalWrite(),
            'redoSize'=>$entity->getRedoSize(),
            'hardParses'=>$entity->getHardParses(),
            'logons'=>$entity->getLogons(),
            'dbTime'=>$entity->getDbTime(),
        ];
    }

    /**
     * @param $snapshot Snapshot
     * @return array
     */
    public function getRessourceForSnapshot($snapshot){
        return [$this->getRessource($snapshot->getLoadProfile())];
    }
}