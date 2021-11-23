<?php

namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\Base\Advisory;
use AppBundle\Entity\Base\Instance;
use AppBundle\Entity\Base\ServerConfig;
use AppBundle\Entity\Base\SgbdConfig;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class InstanceRessource extends AbstractEntityRessource
{
    /**
     * @param $entity Instance
     * @return mixed
     */
    public function getRessource($entity)
    {   
        $qb =  $this->em->createQueryBuilder();
        $instanceMetadata = $qb->select("InstanceMetadata")
        ->from('AppBundle\Entity\Metadata\InstanceMetadata',"InstanceMetadata")
        ->where("InstanceMetadata.instance = :instanceId ")
        ->setParameter("instanceId",$entity->getId())
        ->getQuery()
        ->getFirstResult();
        $nbSapshot = $instanceMetadata[0][1];
       return [
           'name'=> $entity->getName(),
           'nbSnapshots'=>$nbSapshot
       ];
    }
    /**
     * @param $entity Instance
     * @param String $json
     * @return mixed
     */
    public function updateFromJson($entity, $json)
    {
        $jsonDecoded = json_decode($json,true);
        $entity->setname ((isset($jsonDecoded['name']))?$jsonDecoded['name']:$entity->getname());
        $entity->setserverName((isset($jsonDecoded['serverName']))?$jsonDecoded['serverName']:$entity->getserverName());
        if (isset($jsonDecoded['ServerConfig'])) {
            if($entity->getServerConfig() === null){
                $entity->setServerConfig(New ServerConfig());
            }
            $entity->getserverConfig()->updateFromJson(json_encode($jsonDecoded['ServerConfig']));
        }
        if (isset($jsonDecoded['SgbdConfig'])) {
            if($entity->getSgbdConfig() === null){
                $entity->setSgbdConfig(New SgbdConfig());
            }
            $entity->getsgbdConfig()->updateFromJson(json_encode($jsonDecoded['SgbdConfig']));
        }
        if (isset($jsonDecoded['Advisory'])) {
            foreach ($jsonDecoded['Advisory'] as $jsonAdvisory ) {
                /* @var $advisory Advisory */
                $advisory = $this->em->createQueryBuilder()
                    ->select('Advisory')
                    ->from('AppBundle\Entity\Base\Advisory', "Advisory")
                    ->where('Advisory.name = :name')
                    ->andWhere('Advisory.instance = :instance')
                    ->setParameter('name',$jsonAdvisory['name'])
                    ->setParameter('instance',$entity->getId())
                    ->getQuery()
                    ->getOneOrNullResult();
                if($advisory){
                    $advisory->updateFromJson(json_encode($jsonAdvisory));
                }else{
                    $advisory =  new Advisory();
                    $advisory->updateFromJson(json_encode($jsonAdvisory));
                    $entity->addAdvisory($advisory);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return [
            "title"=>"instanceState",
            "properties"=>[
                "nbSession"=>[
                    "name"=>"string"
                ]
            ]
        ];
    }

    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {
        return $this->getRessource($snapshot->getInstance());
    }

}