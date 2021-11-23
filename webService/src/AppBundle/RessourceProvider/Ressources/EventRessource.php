<?php
namespace AppBundle\RessourceProvider\Ressources;

use AppBundle\Entity\Base\Instance;
use AppBundle\Entity\Metadata\EventMetadata;
use AppBundle\Entity\Snapshots\Event;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Snapshots\EfficiencyIndicator;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class EventRessource extends AbstractEntityRessource
{
    /**
     * @return mixed
     */
    function getMetadata()
    {
        $classes = $this->getEventClasses();
        $metadataArray = [];
        foreach ($classes as $className){
            $metadata = [
                "title"=> "event".$className,
                "properties" => []
            ];
            $eventsName = $this->getEventNamesbyClass($className);
            foreach ($eventsName as $eventName){
                $eventProperty = [
                    [
                        $eventName.'__avgWait' => [
                            "type" => "number"
                        ],
                        $eventName.'__dbTime' => [
                            "type" => "number"
                        ],
                        $eventName.'__time' => [
                            "type" => "number"
                        ],
                        $eventName.'__waits' => [
                            "type" => "number"
                        ],
                    ]
                ];
                $metadata["properties"] = array_merge($metadata["properties"],$eventProperty);
            }
            array_push($metadataArray,$metadata);

        }/**/
        return [
            "title" => "event",
            "properties" => [
                'avgWait' => [
                    "type" => "number"
                ],
                'dbTime' => [
                    "type" => "number"
                ],
                "class" => [
                    "type" => "string",
                    "hints" => $this->getEventClasses()
                ],
                "name" => [
                    "type" => "string",
                    "hints" => $this->getEventNames()
                ],
                'time' => [
                    "type" => "number"
                ],
                'waits' => [
                    "type" => "number"
                ],
            ]
        ];
    }
    /**
     * @param $entity Event
     * @return array
     */
    public function getRessource($entity)
    {
        if(!$entity) return null;
        $ressource = [
            'name' => ($entity->getName() !== null )?$entity->getName():null,
            'avgWait' => ($entity->getAvgWait() !== null )?$entity->getAvgWait():0,
            'dbTime' => ($entity->getDbTime() !== null )?$entity->getDbTime():null,
            'time' => ($entity->getTime() !== null )?$entity->getTime():null,
            'waits' => ($entity->getWaits() !== null )?$entity->getWaits():null
        ];
        if ($entity->getEventMetadata()) {
            $ressource['_name'] = $entity->getEventMetadata()->getName();
            $ressource['waitClass'] = $entity->getEventMetadata()->getWaitClass();
            $ressource['description'] = $entity->getEventMetadata()->getDescription();
        } else {
            $ressource['_name'] = null;
            $ressource['waitClass'] = null;
            $ressource['description'] = null;
        }
        return $ressource;
    }

    /**
     * @param $entity Event
     * transform a valid json into $this
     * @param $json String
     */
    public function updateFromJson($entity, $json)
    {
        if (!$entity) {
            $entity = new Event();
        }
        $jsonDecoded = json_decode($json, true);
        $entity->setName(
            (isset($jsonDecoded['name'])) ? $jsonDecoded['name'] : $entity->getName());
        $entity->setWaits(
            (isset($jsonDecoded['waits'])) ? $jsonDecoded['waits'] : $entity->getWaits());
        $entity->setTime(
            (isset($jsonDecoded['time'])) ? $jsonDecoded['time'] : $entity->getTime());
        $entity->setAvgWait(
            (isset($jsonDecoded['avgWait'])) ? $jsonDecoded['avgWait'] : $entity->getAvgWait());
        $entity->setDbTime(
            (isset($jsonDecoded['dbTime'])) ? $jsonDecoded['dbTime'] : $entity->getDbTime());
        /** @var EventMetadata $eventMetadata */
        $eventMetadata =
            $this->em->getRepository('AppBundle\Entity\Metadata\EventMetadata')->createQueryBuilder("eventMetadata")
                ->where("eventMetadata.name LIKE :name")
                ->setParameter("name",$jsonDecoded['name'].'%')
                ->getQuery()
                ->getResult();
        if(isset($eventMetadata[0])){
            $entity->setEventMetadata($eventMetadata[0]);
        }
        return $entity;
    }

    /**
     * @param $snapshot Snapshot
     * @return array
     */
    public function getRessourceForSnapshot($snapshot)
    {
        $events = $snapshot->getEvents();
        $ressources = [];
        /** @var Event $event */
        foreach ($events as $event) {
            if($event->getEventMetadata()){
                $name = $event->getEventMetadata()->getName();

            }else{
                $name = $event->getName();
            }
            if($event->getEventMetadata() === null || $event->getEventMetadata()->getWaitClass() !== "Idle"){
                $ressources[$name] = $this->getRessource($event);
            }
        }
        return $ressources;
    }

    public function getEventNamesbyClass($className)
    {
        return array_map('current',
            $this->em->createQueryBuilder()
            ->select('EventMetadata.name')
            ->from('AppBundle\Entity\Metadata\EventMetadata', "EventMetadata")
            ->where("EventMetadata.wait_class = :className")
            ->setParameter('className', $className)
            ->getQuery()
            ->getArrayResult()
        );
    }
    /**
     * @param $snapshot Snapshot
     */
    public function getEventsRessourceForSnapshotByClass($snapshot,$className){
        $events = $snapshot->getEvents();
        $ressources = [];
        /** @var Event $event */
        foreach ($events as $event) {
            if($event->getEventMetadata()){
                $name = $event->getEventMetadata()->getName();
                if(strtolower($event->getEventMetadata()->getWaitClass()) === strtolower($className)){
                    $ressources[$name] = $this->getRessource($event);
                }
            }
        }
        return $ressources;
    }
    /**
     * @param $snapshot Snapshot
     */
    public function getEventsForSnapshotByClass($snapshot,$className){
        return  $this->em->getRepository('AppBundle\Entity\Snapshots\Event')->createQueryBuilder("event")
            ->select("event")
            ->join('AppBundle\Entity\Metadata\EventMetadata',"eventMetadata")
            ->where("event.snapshot = :snapshotId")
            ->andWhere("eventMetadata.wait_class = :className")
            ->setParameter('className',$className)
            ->setParameter('snapshotId',$snapshot)
            ->getQuery()
            ->getResult();
    }
    private function getEventClasses()
    {
        $data = [];
        $events =  array_map('current',
            $this->em->createQueryBuilder()
            ->select('EventMetadata.wait_class')
            ->from('AppBundle\Entity\Metadata\EventMetadata', "EventMetadata")
            ->distinct()
            ->getQuery()
            ->getScalarResult()
        );
        foreach ($events as $className){
            $data[] = $className;
        }
        return  $data;
    }
    private function getEventNames()
    {
        $data = [];
        $events =  array_map('current',
        $this->em->createQueryBuilder()
                ->select('EventMetadata.name')
                ->from('AppBundle\Entity\Metadata\EventMetadata', "EventMetadata")
                ->getQuery()
                ->getScalarResult()
            );
        foreach ($events as $eventName){
            $data[] = [$eventName];
        }
        return $data;
    }


}