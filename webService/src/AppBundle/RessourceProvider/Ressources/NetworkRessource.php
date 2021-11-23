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


use AppBundle\Entity\Snapshots\Event;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractVirtualRessource;
use Doctrine\Common\Collections\Criteria;

class NetworkRessource extends AbstractVirtualRessource
{
    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {

        $data =[];
        $events = $this->getEventsForSnapshot($snapshot);
        if($events){
            /** @var $event Event*/
            foreach ($events as $event){
                if($event !== null){
                    if($event->getEventMetadata()){
                        if($event->getEventMetadata()->getWaitClass() == "Network"){
                            $name = $event->getEventMetadata()->getName();
                            $data[$name."__avgWait"] = ($event->getAvgWait())?$event->getAvgWait():null;
                            $data[$name."__waits"] = ($event->getWaits())?$event->getWaits():null;
                        }
                    }
                }
            }
        }
        return [$data];
    }
    /**
     * @return mixed
     */
    public function getMetadata()
    {
        $eventMetadataProperties = [];
        foreach ($this->getEventsMetadataName() as $eventName){
            $eventMetadataProperties[$eventName."__avgWait"] = ["type"=>"number"];
            $eventMetadataProperties[$eventName."__waits"] = ["type"=>"number"];
        }
        return[
            "title"=>"network",
            "properties"=>
                $eventMetadataProperties
        ];
    }

    private function getEventsForSnapshot($snapshot){
        return $this->ressourceFactory->get('event')->getEventsForSnapshotByClass($snapshot,'Network');
    }
    private function getEventsMetadataName()
    {
        return $this->ressourceFactory->get('event')->getEventNamesbyClass('Network');
    }

}