<?php
use AppBundle\Entity\AuditEngine\NoteEngine;

namespace AppBundle\RessourceProvider\Ressources;

use AppBundle\AppBundle;
use AppBundle\Entity\AuditEngine\NoteEngine;
use AppBundle\Entity\Base\Instance;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;
use AppBundle\RessourceProvider\RessourceFactory;
use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Snapshots\EfficiencyIndicator;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use AppBundle\Entity\Snapshots\Note;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\Services\AuditEngine;

class NoteRessource extends AbstractEntityRessource
{
    private $auditEngine;
    public function __construct(RessourceFactory $ressourceFactory,EntityManager $em,AuditEngine $auditEngine) {
        parent::__construct($ressourceFactory,$em);
        $this->auditEngine = $auditEngine;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        $notesProperty = [];
        foreach ($this->getNotesName() as $noteName){
            $notesProperty[$noteName] = ["type"=>"number"];
        }
        return[
            "title"=>"note",
            "properties"=> $notesProperty
        ];
    }
    private function getNotesName(){
        return array_map('current',
        $this->em->createQueryBuilder()
            ->select("noteEngine.name")
            ->from("AppBundle\Entity\AuditEngine\NoteEngine","noteEngine")
            ->distinct()
            ->getQuery()
            ->getScalarResult()
        );
    }
    /**
     * @param $entity Note
     * @param String $json
     * @return Note
     */


    public function updateFromJson($entity,$json){
        $jsonDecoded = json_decode($json,true);
        if(!$entity){
            $entity = new Note();
        }
        $entity->setValue(
            (isset($jsonDecoded['value']))?$jsonDecoded['value']:$entity->getValue());
        $noteEngine = $this->em->getRepository('AppBundle\Entity\AuditEngine\NoteEngine')
            ->findOneBy(['name'=>$jsonDecoded['name']]);
        $entity->setNoteEngine($noteEngine);
        return $entity;
    }


    /**
     * @param $entity Note
     * @return array
     */
    public function getRessource($entity){
        $value = ($entity->getValue() !== null)?$entity->getValue():$this->auditEngine->getNoteValue($entity->getNoteEngine(),$entity->getSnapshot());
        $entity->setValue($value);
        if($entity->getNoteEngine()->getIsStored()){
            $this->em->persist($entity);
            $this->em->flush();
        }
        return [$entity->getName()=>$value];
    }

    /**
     * @param $snapshot Snapshot
     * @return array
     */
    public function getRessourceForSnapshot($snapshot){
        $notesEngine = $this->em->getRepository('AppBundle\Entity\AuditEngine\NoteEngine')->findAll();
        $ressources = [];
        /* @var $noteEngine NoteEngine */
        foreach ($notesEngine as $noteEngine){

            $note = null;
            if($noteEngine->getIsStored()){
                /** @var Note $note */
                $note = $this->findNoteBySnapshotAndNoteEngine($snapshot,$noteEngine);

                if( !$note ){
                    $note = new Note();
                    $note->setNoteEngine($noteEngine);
                    $snapshot->addNote($note);
                    $this->em->persist($note);
                    $this->em->persist($snapshot);
                    $this->em->flush();
                }
            }
            else{
               $note = new Note();
               $note->setNoteEngine($noteEngine);
               $note->setSnapshot($snapshot);
            }
            $noteName = $note->getNoteEngine()->getName();
            $ressources[$noteName] = $this->getRessource($note)[$noteName];
        }
        return [$ressources];
    }
    /**
     * @param $snapshot Snapshot
     * @param $noteEngine NoteEngine
     */
    private function findNoteBySnapshotAndNoteEngine($snapshot,$noteEngine){
        return $this->em->getRepository('AppBundle\Entity\Snapshots\Note')
            ->findOneBy(['snapshot'=>$snapshot,'noteEngine'=>$noteEngine]);
    }
    /**
     * @param $snapshot Snapshot
     * @param $indicator
     * @return array
     */
    public function getRessourceBySnapshotAndIndicator($snapshot,$indicator,$filterArray=null){
        $noteEngine = $this->em->getRepository('AppBundle\Entity\AuditEngine\NoteEngine')->findOneBy(['name'=>$indicator]);
        /* @var $noteEngine NoteEngine */
        if($noteEngine->getIsStored()){
            $note = $this->findNoteBySnapshotAndNoteEngine($snapshot,$noteEngine);
            if(!$note){
                $note = new Note();
                $note->setNoteEngine($noteEngine);
                $snapshot->addNote($note);
                $this->em->persist($note);
                $this->em->persist($snapshot);
                $this->em->flush();
            }
        }
            else{
                $note = new Note();
                $note->setNoteEngine($noteEngine);
                $note->setSnapshot($snapshot);
            }
           $ressource = $this->getRessource($note);
        return [$ressource[$indicator]];
    }

    /**
     *
     */
    public function whipeNoteForNoteEngine($noteEngine){
        $this->em->createQueryBuilder()
            ->from('AppBundle\Entity\Snapshots\Note',"note")
            ->delete()
            ->where(':noteEngine = note.noteEngine')
            ->setParameter('noteEngine',$noteEngine)
            ->getQuery()
            ->execute();
        //$this->em->flush();
    }

    /**
     * @param $noteEngine NoteEngine
     * @param $scheduledDate \DateTime
     * @param $recalculateFrom \DateTime
     * @param $recalculateTo \DateTime
     */
    public function scheduleNoteUpdate($noteEngine,$scheduledDate,$recalculateFrom,$recalculateTo){
        /*$this->recalculateNote($noteEngine,
            $recalculateFrom,
            ($recalculateTo !== null)?$recalculateTo:new \DateTime()
        );*/
        /*$this->scheduler->call(function($noteEngine,$recalculateFrom,$recalculateTo)
        {
            $this->recalculateNote($noteEngine,
                $recalculateFrom,
                ($recalculateTo !== null)?$recalculateTo:new \DateTime()
            );
        })->at($scheduledDate->format('i H d m * Y'));*/
        $job = new Job('app:note:recalculate'
        ,[
            'from'=>$recalculateFrom->format("Y-m-d H:i:s"),
            'to'=>$recalculateTo->format("Y-m-d H:i:s"),
            'noteEngineId'=>$noteEngine->getId()
        ]);
        $job->setExecuteAfter($scheduledDate);
        $this->em->persist($job);
        $this->em->flush();
    }

    /**
     * @param $noteEngine NoteEngine
     * @param $from \DateTime
     * @param $to \DateTime
     */
    public function recalculateNote($noteEngineId,$from,$to){
        $notesToUpdate = $this->em->createQueryBuilder()
            ->select('note')
            ->from('AppBundle\Entity\Snapshots\Note',"note")
            ->join('AppBundle\Entity\Snapshots\Snapshot',"snapshot","with","note.snapshot = snapshot")
            ->where(':noteEngineId = note.noteEngine')
            ->andWhere('snapshot.startDate between :startDate and :endDate')
            ->setParameter('startDate',$from/*->format("Y-m-d H:i:s")*/)
            ->setParameter('endDate',$to/*->format("Y-m-d H:i:s")*/)
            ->setParameter('noteEngineId',$noteEngineId)
            ->getQuery()
            ->getResult();
        /* @var $noteEngine NoteEngine */
        $noteEngine = $this->em->getRepository('AppBundle\Entity\AuditEngine\NoteEngine')->findOneBy(["id"=>$noteEngineId]);
        /* @var $note Note */
        foreach ($notesToUpdate as $note){
            $noteValue = $this->auditEngine->getNoteValue($noteEngine,$note->getSnapshot());
            $note->setValue($noteValue);
            $this->em->merge($note);
        }
        $this->em->flush();
    }
}   