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


use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\Entity\Snapshots\Tablespace;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class TableSpaceRessource extends AbstractEntityRessource
{

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return [
            "title"=>"Tablespace",
            "properties"=>[
                'name' =>[
                    "type"=>"string"
                ],
                'readNb' =>[
                    "type"=>"number"
                ],
                'avReadS' =>[
                    "type"=>"number"
                ],
                'avRead' =>[
                    "type"=>"number"
                ],
                'avBlkReads' =>[
                    "type"=>"number"
                ],
                'avWritesS' =>[
                    "type"=>"number"
                ],
                'bufferWaits' =>[
                    "type"=>"number"
                ],
                'avBufferWaits' =>[
                    "type"=>"number"
                ],
                'writesNb' =>[
                    "type"=>"number"
                ],
            ]
        ];
    }

    /**
     * @param $entity Tablespace
     * @param String $json
     * @return Tablespace
     */
    public function updateFromJson($entity,$json){
        $jsonDecoded = json_decode($json,true);
        if(!$entity){
            $entity = new Tablespace();
        }
        $entity->setName(
            (isset($jsonDecoded['name']))?strtolower($jsonDecoded['name']):null);
        $entity->setWritesNb(
            (isset($jsonDecoded['writesNb']))?$jsonDecoded['writesNb']:null);
        $entity->setReadNb(
            (isset($jsonDecoded['readNb']))?$jsonDecoded['readNb']:null);
        $entity->setAvReadS(
            (isset($jsonDecoded['avReadS']))?$jsonDecoded['avReadS']:null);
        $entity->setAvRead(
            (isset($jsonDecoded['avRead']) && isset($jsonDecoded['readNb']) && $jsonDecoded['readNb'] !== 0)?$jsonDecoded['avRead']:null);
        $entity->setAvBlkReads(
            (isset($jsonDecoded['avBlkReads']))?$jsonDecoded['avBlkReads']:null);
        $entity->setAvWritesS(
            (isset($jsonDecoded['avWritesS']))?$jsonDecoded['avWritesS']:null);
        $entity->setBufferWaits(
            (isset($jsonDecoded['bufferWaits']))?$jsonDecoded['bufferWaits']:null);
        $entity->setAvBufferWaits(
            (isset($jsonDecoded['avBufferWaits']))?$jsonDecoded['avBufferWaits']:null);
        return $entity;
    }

    /**
     * @param $entity Tablespace
     * @return array
     */
    public function getRessource($entity)
    {
        if(!$entity){
            return null;
        }
        return [
            'name' => $entity->getName(),
            'readNb' => $entity->getReadNb(),
            'avReadS' => $entity->getAvReadS(),
            'avRead' => $entity->getAvRead(),
            'avBlkReads' => $entity->getAvBlkReads(),
            'avWritesS' => $entity->getAvWritesS(),
            'bufferWaits' => $entity->getBufferWaits(),
            'avBufferWaits' => $entity->getAvBufferWaits(),
            'writesNb' => $entity->getWritesNb(),
        ];
    }
    /**
     * @param $snapshot Snapshot
     * @return array
     */
    public function getRessourceForSnapshot($snapshot){
        $tableSpaces = $snapshot->getTablespaces();
        $ressources = [];
        foreach ($tableSpaces as $tableSpace){
            $ressources[strtolower($tableSpace->getName())] = $this->getRessource($tableSpace);
        }
        return $ressources;
    }
    
}