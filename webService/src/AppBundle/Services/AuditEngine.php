<?php
namespace AppBundle\Services;

use AppBundle\Entity\AuditEngine\Filter;
use AppBundle\Entity\Snapshots\Note;
use AppBundle\RessourceProvider\RessourceFactory;
use AppBundle\Entity\AuditEngine\Advice;
use AppBundle\Entity\AuditEngine\Indicator;
use AppBundle\Entity\AuditEngine\NoteEngine;
use AppBundle\Entity\Snapshots\Snapshot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;

class AuditEngine
{
    /**
     * @var $em EntityManager
     */
    private $em;
    private $logger;
    private $ressourceFactory;
    private $NOTE_TOTAL = 20;
    public function __construct(EntityManager $entityManager,RessourceFactory $ressourceFactory ,Logger $logger)
    {
        $this->ressourceFactory = $ressourceFactory;
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @param $noteEngine NoteEngine
     * @param $snapshot Snapshot
     * @return float|int
     */
    public function getNoteValue($noteEngine, $snapshot)
    {
        try{
            return ($noteEngine->getIsAvg())?
                $this->calculateNoteAvg($noteEngine,$snapshot):
                $this->calculateNote($noteEngine,$snapshot);
        }catch (Exception $e){
            return $this->NOTE_TOTAL/2;
        }
    }

    /**
     * @param $noteEngine NoteEngine
     * @param $snapshot Snapshot
     * @return float|int
     */
    private function calculateNote($noteEngine,$snapshot){
        if(!$noteEngine) return $this->NOTE_TOTAL/2;
        $totalCoeff = 0;
        $noteValue = 0;
        $indicators = $noteEngine->getIndicators() ;
        $logInfo = [
            "start_date"=>$snapshot->getStartDate(),
            "client"=>$snapshot->getInstance()->getBase()->getClient()->getName(),
            "base"=>$snapshot->getInstance()->getBase()->getName(),
            "version"=> $snapshot->getInstance()->getBase()->getVersion(),
            "fileName"=>$snapshot->getFileName(),
            "calcul"=>[]
        ];
        $logInfo["noteName"] = $noteEngine->getName();
        /** @var  $indicator Indicator */
        foreach ($indicators as $indicator) {
            if($indicator->getClass() === null){
                $this->em->remove($indicator);
                $this->em->flush($indicator);
                break;
            }
            $filterArray = (!$indicator->getFilter()->isEmpty())?
            $this->createFilterMap($indicator->getFilter()):null;
            $propertyValues = $this->ressourceFactory->get($indicator->getClass())
                ->getRessourceBySnapshotAndIndicator($snapshot,$indicator->getField(),$filterArray);
               // $this->accessor->getPropertyFromSnapshot($snapshot,$indicator->getClass(), $indicator->getField(),$filterArray);
            if (!is_array($propertyValues) && $propertyValues != null){
                $propertyValue = $propertyValues;
            }else{
                $i=0;
                $propertyValue = 0;
                $fillWithNull = True;
                foreach ($propertyValues as $val){
                    if($val == null ) {
                        $val = 0;
                    }else{
                        $fillWithNull = False;
                    }
                    $propertyValue+=$val;
                    $i++;
                }
                if($i != 0){
                    $propertyValue /= $i;
                }else if ($fillWithNull === true){
                    $propertyValue = null;
                }
            }
            $indicatorName = $indicator->getClass().".".$indicator->getField().'('.$indicator->getId().')';
            $filter = $indicator->getFilter()[0];
            if($filter !== null){
                $indicatorName .= '['.$filter->getValue().']';
            }
            $logInfo["calcul"][$indicatorName] =
                $indicator->getMinValue()."<= "
                    .(($propertyValue !== null)?$propertyValue:"NULL")
                    ." <=".$indicator->getMaxValue()
                    ." (".$indicator->getCoeff().")";
            if($propertyValue !== null){
                $totalCoeff += $indicator->getCoeff();
                if ($this->isTriggered($indicator, $propertyValue) === true) {
                    $noteValue += $indicator->getCoeff();
                    $logInfo["calcul"][$indicatorName] .= "(+)";
                }else{
                    $logInfo["calcul"][$indicatorName] .= "(-)";
                }
            }else{
                $logInfo["calcul"][$indicatorName] .= "(=)";
            }
        }
        if($totalCoeff != 0){
            $result = ($noteValue / $totalCoeff) * $this->NOTE_TOTAL;
            $logInfo["noteValue"] = $result;
            $this->logger->info("Détail du calcul de la note",$logInfo);
            return $result;
        }
        else{
            $result = $this->NOTE_TOTAL/2;
            $logInfo["noteValue"] = $result;
            $this->logger->warn("Détail du calcul de la note: aucun indicateur trouvé",$logInfo);
            return $this->NOTE_TOTAL/2;
        }
    }

    /**
     * @param $noteEngine NoteEngine
     * @param $snapshot
     * @return float|int
     */
    private function calculateNoteAvg($noteEngine,$snapshot)
    {
        //$noteEngine = $this->getNoteEngine($noteName,$snapshot->getVersion());
        if (!$noteEngine) return $this->NOTE_TOTAL;
        $totalCoeff = 0;
        $noteValue = 0;
        $indicators = $noteEngine->getIndicators();
        /** @var  $indicator Indicator */
        foreach ($indicators as $indicator) {
            /** @var Note $note */
            $note = $this->ressourceFactory->get($indicator->getClass())->getRessourceBySnapshotAndIndicator($snapshot, $indicator->getField(), $filterArray = null)[0];
            //$note = $this->accessor->getPropertyFromSnapshot($snapshot,$indicator->getClass(),$indicator->getField())[0];
            $totalCoeff += $indicator->getCoeff();
            $noteValue += $indicator->getCoeff() * $note;
        }
        if ($totalCoeff !== 0) {
            return ($noteValue / $totalCoeff);
        } else {
            return $this->NOTE_TOTAL;
        }
    }


    private function createFilterMap($filters){
        $filterArray = [];
        /** @var Filter $filter */
        foreach ($filters as $filter){
            $filterArray[$filter->getField()] = $filter->getValue();
        }
        return $filterArray;
    }

    private function isTriggered(Indicator $indicator, $value)
    {
        if (!$indicator) return false;
        if ($indicator->getMaxValue() !== null && $indicator->getMinValue() !== null ) {
            if ($indicator->getMinValue() <= $value && $value <= $indicator->getMaxValue())  {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $noteName
     * @return mixed
     */
    public function isStored($noteName){
        return $this->em->getRepository('AppBundle\Entity\AuditEngine\NoteEngine')->findOneByName($noteName)->isStored();
    }
}
