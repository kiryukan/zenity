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


use AppBundle\Entity\Snapshots\AdvisorySnapshot;
use AppBundle\Entity\Snapshots\EfficiencyIndicator;
use AppBundle\Entity\Snapshots\Event;
use AppBundle\Entity\Snapshots\InstanceState;
use AppBundle\Entity\Snapshots\LoadProfile;
use AppBundle\Entity\Snapshots\Note;
use AppBundle\Entity\Snapshots\OSState;
use AppBundle\Entity\Snapshots\Parameters;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\Entity\Snapshots\SqlInfo;
use AppBundle\Entity\Snapshots\Stat;
use AppBundle\Entity\Snapshots\Tablespace;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class SnapshotRessource extends AbstractEntityRessource
{
    /**
     * transform a valid json into $this
     * @param $entity Snapshot
     * @param $json String
     */
    public function updateFromJson($entity, $json)
    {
        $jsonDecoded = json_decode($json, true);
        $entity->setVersion(
            (isset($jsonDecoded['version'])) ? $jsonDecoded['version'] : $entity->getVersion());
        $entity->setFileName(
            (isset($jsonDecoded['fileName'])) ? $jsonDecoded['fileName'] : $entity->getFileName());
        $entity->setStartDate(
            (isset($jsonDecoded['startDate'])) ? \DateTime::createFromFormat('j-M-y H:i:s', $jsonDecoded['startDate']) : $entity->getStartDate());
        $entity->setEndDate(
            (isset($jsonDecoded['endDate'])) ? \DateTime::createFromFormat('j-M-y H:i:s', $jsonDecoded['endDate']) : $entity->getEndDate());
        if (isset($jsonDecoded['TableSpace'])) {
            foreach ($jsonDecoded['TableSpace'] as $jsonDecodedTableSpace) {
                $tableSpace = new Tablespace();
                $this->ressourceFactory->get('tablespace')->updateFromJson($tableSpace, json_encode($jsonDecodedTableSpace));
                $entity->addTablespace($tableSpace);
            }
        }
        if (isset($jsonDecoded['Note'])) {
            foreach ($jsonDecoded['Note'] as $jsonDecodedNote) {
                $note = new Note();
                $this->ressourceFactory->get('note')->updateFromJson($note, json_encode($jsonDecodedNote));
                $entity->addNote($note);
            }
        }
        if (isset($jsonDecoded['Event'])) {
            foreach ($jsonDecoded['Event'] as $jsonDecodedEvent) {
                $event = new Event();
                $event = $this->ressourceFactory->get('event')->updateFromJson($event, json_encode($jsonDecodedEvent));
                $entity->addEvent($event);
            }
        }
        if (isset($jsonDecoded['Parameters'])) {
            foreach ($jsonDecoded['Parameters'] as $jsonDecodedParameters) {
                $parameter = new Parameters();
                $parameter = $this->ressourceFactory->get('parameters')->updateFromJson($parameter, json_encode($jsonDecodedParameters));
                $entity->addParameters($parameter);
            }
        }
        if (isset($jsonDecoded['Stats'])) {
            foreach ($jsonDecoded['Stats'] as $jsonDecodedEvent) {
                $stat = new Stat();
                $stat = $this->ressourceFactory->get('stat')->updateFromJson($stat, json_encode($jsonDecodedEvent));
                $entity->addStat($stat);
            }
        }
        if (isset($jsonDecoded['InstanceState'])) {
            $instanceState = (!$entity->getInstanceState()) ? new InstanceState() : $entity->getInstanceState();
            $this->ressourceFactory->get('instanceState')->updateFromJson($instanceState, json_encode($jsonDecoded['InstanceState']));
            $entity->setInstanceState($instanceState);

        }
        if (isset($jsonDecoded['LoadProfile'])) {
            $loadProfile = (!$entity->getLoadProfile()) ? new LoadProfile() : $entity->getLoadProfile();
            $this->ressourceFactory->get('loadProfile')->updateFromJson($loadProfile, json_encode($jsonDecoded['LoadProfile']));
            $entity->setLoadProfile($loadProfile);
        }

        if (isset($jsonDecoded['EfficiencyIndicator'])) {
            $efficiencyIndicator = (!$entity->getEfficiencyIndicator()) ? new EfficiencyIndicator() : $entity->getEfficiencyIndicator();
            $this->ressourceFactory->get('efficiencyIndicator')->updateFromJson($efficiencyIndicator, json_encode($jsonDecoded['EfficiencyIndicator']));
            $entity->setEfficiencyIndicator($efficiencyIndicator);

        }
        if (isset($jsonDecoded['SqlInfo'])) {

            $sqlInfo = (!$entity->getSqlInfo()) ? new SqlInfo() : $entity->getSqlInfo();
            $this->ressourceFactory->get('sqlInfo')->updateFromJson($sqlInfo, json_encode($jsonDecoded['SqlInfo']));
            $entity->setSqlInfo($sqlInfo);

        }
        if (isset($jsonDecoded['OSState'])) {
            $oSState = (!$entity->getOsState()) ? new OSState() : $entity->getOsState();
            $this->ressourceFactory->get('osState')->updateFromJson($oSState, json_encode($jsonDecoded['OSState']));
            $entity->setOsState($oSState);
        }
        if (isset($jsonDecoded['Advisory'])) {
            foreach($jsonDecoded['Advisory'] as $advisory){
                $advisoryEntity = new AdvisorySnapshot();
                $this->ressourceFactory->get('advisory')->updateFromJson($advisoryEntity, json_encode($advisory));
                $entity->addAdvisory($advisoryEntity);
            }
        }
    }

    /**
     * @param $entity Snapshot
     */
    public function getRessource($entity)
    {
        $ressource = [];
        $ressource['startDate'] = $entity->getStartDate();
        $ressource['endDate'] = $entity->getEndDate();
        $ressource['version'] = $entity->getVersion();
        $ressource['oSState'] = $entity->getOsState();
        $ressource['event'] = $entity->getEvents();
        $ressource['efficiencyIndicator'] = $entity->getEfficiencyIndicator();
        $ressource['tablespace'] = $entity->getTablespaces();
        $ressource['loadProfile'] = $entity->getLoadProfile();
        $ressource['stat'] = $entity->getStats();
        $ressource['instanceState'] = $entity->getInstanceState();
        $ressource['request'] = $entity->getSqlInfo()->getRequests();

        //$ressource['note'] = $entity->getNotes();
        return $ressource;
    }
    public function getMetadata()
    {
        return[
            //TODO
        ];
    }
    public function getRessourceForSnapshot($snapshot){
        return $this->getRessource($snapshot);
    }
}