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


use AppBundle\Entity\Snapshots\Request;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;


class RequestRessource extends AbstractEntityRessource
{
    /**
     * @return mixed
     */
    function getMetadata()
    {
        return [
            "title"=>"request",
            "properties"=>[
                'sqlId'=>[
                    "type"=>"string"
                ],
                'hash'=>[
                    "type"=>"number"
                ],
                'sqlCode'=>[
                    "type"=>"number"
                ],
                'elapPerExec'=>[
                    "type"=>"number"
                ],
                'elapTime'=>[
                    "type"=>"number"
                ],
                'cpuTime'=>[
                    "type"=>"number"
                ],
                'cpu'=>[
                    "type"=>"number"
                ],
                'io'=>[
                    "type"=>"number"
                ],
                'getPerExec'=>[
                    "type"=>"number"
                ],
                'bufferGet'=>[
                    "type"=>"number"
                ],
                'parseCall'=>[
                    "type"=>"number"
                ],
                'totalParse'=>[
                    "type"=>"number"
                ],
                'maxVersionCount'=>[
                    "type"=>"number"
                ],
                'totalCpu'=>[
                    "type"=>"number"
                ],
                'totalGets'=>[
                    "type"=>"number"
                ],
                'totalReads'=>[
                    "type"=>"number"
                ],
                'totalElapTime'=>[
                    "type"=>"number"
                ],
                'exec'=>[
                    "type"=>"number"
                ],
                'cpuPerExec'=>[
                    "type"=>"number"
                ],
                'rowProcessed'=>[
                    "type"=>"number"
                ],
                'rowPerExec'=>[
                    "type"=>"number"
                ],
                'sharableMemory'=>[
                    "type"=>"number"
                ],
            ]
        ];
    }
    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {
        $data = [];
        /** @var Request $request */
        foreach ($snapshot->getSqlInfo()->getRequests() as $request){
            $name = ($request->getSqlId())?$request->getSqlId():$request->getHash()."-hash";
            $data[$name] = $this->getRessource($request);
        };
        return $data;
    }

    /**
     * @param $entity Request
     * @param String $json
     */
    public function updateFromJson($entity,$json){
        $jsonDecoded = json_decode($json,true);
        /* @var $snapshot Snapshot */
        if(!$entity){
            $entity = new Request();
        }
        $entity->setSqlInfo(
            (isset($jsonDecoded['sqlInfo']))?$jsonDecoded['sqlInfo']:null);
        $entity->setSqlId(
            (isset($jsonDecoded['sqlId']))?$jsonDecoded['sqlId']:null);
        $entity->setHash(
            (isset($jsonDecoded['hash']))?$jsonDecoded['hash']:null);
        $entity->setSqlCode(
            (isset($jsonDecoded['sqlCode']))?$jsonDecoded['sqlCode']:null);
        $entity->setModule(
            (isset($jsonDecoded['module']))?$jsonDecoded['module']:null);
        $entity->setElapPerExec(
            (isset($jsonDecoded['elapPerExec']))?$jsonDecoded['elapPerExec']:null);
        $entity->setElapTime(
            (isset($jsonDecoded['elapTime']))?$jsonDecoded['elapTime']:null);
        $entity->setCpuTime(
            (isset($jsonDecoded['cpuTime']))?$jsonDecoded['cpuTime']:null);
        $entity->setCpu(
            (isset($jsonDecoded['cpu']))?$jsonDecoded['cpu']:null);
        $entity->setIo(
            (isset($jsonDecoded['io']))?$jsonDecoded['io']:null);
        $entity->setGetPerExec(
            (isset($jsonDecoded['getPerExec']))?$jsonDecoded['getPerExec']:null);
        $entity->setBufferGet(
            (isset($jsonDecoded['bufferGet']))?$jsonDecoded['bufferGet']:null);
        $entity->setParseCall(
            (isset($jsonDecoded['parseCall']))?$jsonDecoded['parseCall']:null);
        $entity->setTotalParse(
            (isset($jsonDecoded['totalParse']))?$jsonDecoded['totalParse']:null);
        $entity->setMaxVersionCount(
            (isset($jsonDecoded['maxVersionCount']))?$jsonDecoded['maxVersionCount']:null);
        $entity->setExec(
            (isset($jsonDecoded['exec']))?$jsonDecoded['exec']:null);
        $entity->setCpuPerExec(
            (isset($jsonDecoded['cpuPerExec']))?$jsonDecoded['cpuPerExec']:null);
        $entity->setRowProcessed(
            (isset($jsonDecoded['rowProcessed']))?$jsonDecoded['rowProcessed']:null);
        $entity->setRowPerExec(
            (isset($jsonDecoded['rowPerExec']))?$jsonDecoded['rowPerExec']:null);
        $entity->setSharableMemory(
            (isset($jsonDecoded['sharableMemory']))?$jsonDecoded['sharableMemory']:null);
        $entity->setTotalCpu(
            (isset($jsonDecoded['totalCpu']))?$jsonDecoded['totalCpu']:null);
        $entity->setTotalGets(
            (isset($jsonDecoded['totalGets']))?$jsonDecoded['totalGets']:null);
        $entity->setTotalReads(
            (isset($jsonDecoded['totalReads']))?$jsonDecoded['totalReads']:null);
        $entity->setTotalElapTime(
            (isset($jsonDecoded['totalElapTime']))?$jsonDecoded['totalElapTime']:null);
    }

    /**
     * @param $entity Request
     * @return array
     */
    public function getRessource($entity){
        $snapshot = $this->em->getRepository('AppBundle\Entity\Snapshots\Snapshot')->findOneBy(['sqlInfo'=>$entity->getSqlInfo()]);
        $nbCpu = 1;
        if($snapshot->getInstance()->getServerConfig() !== null){
            $nbCpu = $snapshot->getInstance()->getServerConfig()->getNbCpu();
            if($nbCpu === 0 || $nbCpu === null) {
                $nbCpu = 1;
            }
        }
        return [
            'sqlId'=>$entity->getSqlId(),
            'hash'=>$entity->getHash(),
            'name'=>($entity->getSqlId())?$entity->getSqlId():$entity->getHash().'-hash',
            'module'=>($entity->getModule())?$entity->getModule():'',
            'sqlCode'=>($entity->getSqlCode())?$entity->getSqlCode():null,
            'elapPerExec'=>($entity->getElapPerExec())?$entity->getElapPerExec():null,
            'elapTime'=>($entity->getElapTime())?$entity->getElapTime():null,
            'cpuTime'=>($entity->getCpuTime())?$entity->getCpuTime():null,
            'cpu'=>($entity->getCpu())?$entity->getCpu():null,
            'io'=>($entity->getIo())?$entity->getIo():null,
            'getPerExec'=>($entity->getGetPerExec())?$entity->getGetPerExec():null,
            'bufferGet'=>($entity->getBufferGet())?$entity->getBufferGet():null,
            'parseCall'=>($entity->getParseCall())?$entity->getParseCall():null,
            'totalParse'=>($entity->getTotalParse())?$entity->getTotalParse():null,
            'maxVersionCount'=>($entity->getMaxVersionCount())?$entity->getMaxVersionCount():null,
            'exec'=>($entity->getExec())?$entity->getExec():null,
            'cpuPerExec'=>($entity->getCpuPerExec())?$entity->getCpuPerExec():null,
            'rowProcessed'=>($entity->getRowProcessed())?$entity->getRowProcessed():null,
            'rowPerExec'=>($entity->getRowPerExec())?$entity->getRowPerExec():null,
            'sharableMemory'=>($entity->getSharableMemory())?$entity->getSharableMemory():null,
            'totalCpu'=>($entity->getTotalCpu())?$entity->getTotalCpu()/$nbCpu:null,
            'totalGets'=>($entity->getTotalGets())?$entity->getTotalGets()/$nbCpu:null,
            'totalReads'=>($entity->getTotalReads())?$entity->getTotalReads()/$nbCpu:null,
            'totalElapTime'=>($entity->getTotalElapTime())?$entity->getTotalElapTime()/$nbCpu:null,
        ];
    }
}