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
/**
 * Created by PhpStorm.
 * User: simonvivier
 * Date: 25/04/17
 * Time: 09:22
 */
class EfficiencyIndicatorRessource extends AbstractEntityRessource
{

    /**
     * @return mixed
     */
    function getMetadata()
    {
        return[
            "title"=>"efficiencyIndicator",
            "properties"=>[
                'bufferHit'=>[
                    "type"=>"number"
                ],
                'bufferNoWait'=>[
                    "type"=>"number"
                ],
                'cpuToParse'=>[
                    "type"=>"number"
                ],
                'execToParse'=>[
                    "type"=>"number"
                ],
                'inMemorySort'=>[
                    "type"=>"number"
                ],
                'latchHit'=>[
                    "type"=>"number"
                ],
                'libraryHit'=>[
                    "type"=>"number"
                ],
                'nonParseCpu'=>[
                    "type"=>"number"
                ],
                'optimalWA'=>[
                    "type"=>"number"
                ],
                'pgaCacheHit'=>[
                    "type"=>"number"
                ],
                'redoNoWait'=>[
                    "type"=>"number"
                ],
                'softParse'=>[
                    "type"=>"number"
                ]
            ]
        ];
    }
    /**
     * transform a valid json into $this
     * @param $entity EfficiencyIndicator
     * @param $json String
     */
    public function updateFromJson($entity,$json){
        $jsonDecoded = json_decode($json,true);
        if(!$entity){
            $entity = new EfficiencyIndicator();
        }
        $entity->setBufferNoWait(
            (isset($jsonDecoded['bufferNoWait']))?$jsonDecoded['bufferNoWait']:$entity->getBufferNoWait());
        $entity->setRedoNoWait(
            (isset($jsonDecoded['redoNoWait']))?$jsonDecoded['redoNoWait']:$entity->getRedoNoWait());
        $entity->setInMemorySort(
            (isset($jsonDecoded['inMemorySort']))?$jsonDecoded['inMemorySort']:$entity->getInMemorySort());
        $entity->setOptimalWA(
            (isset($jsonDecoded['optimalWA']))?$jsonDecoded['optimalWA']:$entity->getOptimalWA());
        $entity->setBufferHit(
            (isset($jsonDecoded['bufferHit']))?$jsonDecoded['bufferHit']:$entity->getBufferHit());
        $entity->setLibraryHit(
            (isset($jsonDecoded['libraryHit']))?$jsonDecoded['libraryHit']:$entity->getLibraryHit());
        $entity->setSoftParse(
            (isset($jsonDecoded['softParse']))?$jsonDecoded['softParse']:$entity->getSoftParse());
        $entity->setExecToParse(
            (isset($jsonDecoded['execToParse']))?$jsonDecoded['execToParse']:$entity->getExecToParse());
        $entity->setLatchHit(
            (isset($jsonDecoded['latchHit']))?$jsonDecoded['latchHit']:$entity->getLatchHit());
        $entity->setCpuToParse(
            (isset($jsonDecoded['cpuToParse']))?$jsonDecoded['cpuToParse']:$entity->getCpuToParse());
        $entity->setNonParseCpu(
            (isset($jsonDecoded['nonParseCpu']))?$jsonDecoded['nonParseCpu']:$entity->getNonParseCpu());
        /*$entity->setPgaCacheHit(
            (isset($jsonDecoded['pgaCacheHit']))?$jsonDecoded['pgaCacheHit']:$entity->getPgaCacheHit());*/
        return $entity;
    }
    /**
     * @param $entity EfficiencyIndicator
     * @return array
     */

    public function getRessource($entity){
        return [
            'bufferHit'=>$entity->getBufferHit(),
            'bufferNoWait'=>$entity->getBufferNoWait(),
            'cpuToParse'=>$entity->getCpuToParse(),
            'execToParse'=>$entity->getExecToParse(),
            'inMemorySort'=>$entity->getInMemorySort(),
            'optimalWa'=>$entity->getOptimalWA(),
            'latchHit'=>$entity->getLatchHit(),
            'libraryHit'=>$entity->getLibraryHit(),
            'nonParseCpu'=>$entity->getNonParseCpu(),
            /*'pgaCacheHit'=>$entity->getPgaCacheHit(),*/
            'redoNoWait'=>$entity->getRedoNoWait(),
            'softParse'=>$entity->getSoftParse()
        ];
    }

    /**
     * @param $snapshot Snapshot
     * @return array
     */
    public function getRessourceForSnapshot($snapshot){
        return [$this->getRessource($snapshot->getEfficiencyIndicator())];
    }
}