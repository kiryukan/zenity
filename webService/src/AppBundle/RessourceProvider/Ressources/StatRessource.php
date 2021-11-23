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


use AppBundle\Entity\Metadata\StatMetadata;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\Entity\Snapshots\Stat;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class StatRessource extends AbstractEntityRessource
{

    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {
        $data = [];
        $stats = $snapshot->getStats();
        foreach ($stats as $stat){
            $data[] = $this->getRessource($stat);
        }
        return $data;
    }

    /**
     * @param $entity Stat
     * @param String $json
     * @return Stat
     */
    public function updateFromJson($entity, $json)
    {
        $jsonDecoded = json_decode($json, true);
        if (!$entity) {
            $entity = new Stat();
        }
        $entity->setName(
            (isset($jsonDecoded['name'])) ? strtolower($jsonDecoded['name']) : null );
        $entity->setTotal(
            (isset($jsonDecoded['total'])) ? $jsonDecoded['total'] : null );
        $entity->setPerSec(
            (isset($jsonDecoded['perSec'])) ? $jsonDecoded['perSec'] : null );
        $entity->setPerTrans(
            (isset($jsonDecoded['perTrans'])) ? $jsonDecoded['perTrans'] : null );
        $entity->setPerHour(
            (isset($jsonDecoded['perHour'])) ? $jsonDecoded['perHour'] : null );
        /** @var StatMetadata $statMetadata */
        $statMetadata =
            $this->em->getRepository('AppBundle\Entity\Metadata\StatMetadata')->createQueryBuilder("eventMetadata")
                ->where("eventMetadata.name LIKE :name")
                ->setParameter("name",$jsonDecoded['name'].'%')
                ->getQuery()
                ->getResult();
        if(isset($statMetadata[0])){
            $entity->setStatMetadata($statMetadata[0]);
        }
        return $entity;
    }

    /**
     * @param $entity Stat
     * @return array
     */
    public function getRessource($entity)
    {
        $ressource = [
            'name' => $entity->getName(),
            'total' => $entity->getTotal(),
            'perSec' => $entity->getPerSec(),
            'perTrans' => $entity->getPerTrans(),
            'perHour' => $entity->getPerHour(),

        ];
        if ($entity->getStatMetadata()) {
            $ressource['_name'] = $entity->getStatMetadata()->getName();
            $ressource['waitClass'] = $entity->getStatMetadata()->getClassName();
        } else {
            $ressource['_name'] = null;
            $ressource['waitClass'] = null;
        }
        return $ressource;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return [
            "title" => "stat",
            "properties" => [
                'name' => [
                    "type" => "string",
                ],
                'waitClass' =>[
                    "type" => "string",
                ],
                'total' => [
                    "type" => "integer"
                ],
                'perSec' => [
                    "type" => "integer"
                ],
                'perTrans' => [
                    "type" => "integer"
                ],
                'perHour' => [
                    "type" => "integer"
                ],
            ]
        ];

    }
    public function renameStat($statName){
        return str_replace(" ","_",$statName);
    }
}
