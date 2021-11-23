<?php
namespace AppBundle\Controller\Gestion;

    use AppBundle\AppBundle;
    use AppBundle\Entity\AuditEngine\Advice;
    use AppBundle\Entity\AuditEngine\Filter;
    use AppBundle\Entity\AuditEngine\Indicator;
    use AppBundle\Entity\AuditEngine\NoteEngine;
    use AppBundle\Entity\Snapshots;
    use AppBundle\Entity\Snapshots\Tablespace;
    use AppBundle\Services\SnapshotReading;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\DBAL\Types\TextType;
    use JMS\Serializer\SerializerBuilder;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Form\Form;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
class AuditEngineController extends Controller
{

    /**
     * @Route("/gestion/notes", name="noteManager")
     */
    public function noteAction(Request $request)
    {
        return $this->render('gestion/manageNote.html.twig', [

        ]);
    }
    /**
     * @Route("/gestion/AuditEngine/newIndicator", name="newIndicator")
     */
    public function newIndicator(Request $request){
        $em = $this->getDoctrine()->getManager();
        /* @var $client Indicator */
        $indicatorEntity = new Indicator();
        $indicatorEntity->setCoeff(1);
        if($_GET['parentType'] === 'note'){
            /* @var $note NoteEngine */
            $note = $em->getRepository('AppBundle\Entity\AuditEngine\NoteEngine')
                ->findOneBy(['id' => $_GET['parentId']]);
            $note->addIndicator($indicatorEntity);
            $em->merge($note);
        }elseif ($_GET['parentType'] === 'advice'){
            /* @var $advice Advice */
            $advice = $em->getRepository('AppBundle\Entity\AuditEngine\Advice')
                ->findOneBy(['id' => $_GET['parentId']]);
            $advice->addIndicator($indicatorEntity);
            $em->merge($advice);
        }

        $em->persist($indicatorEntity);
        $em->flush();
        return new JsonResponse($indicatorEntity->getId());
    }
    /**
     * @Route("/gestion/AuditEngine/newNote", name="newNote")
     */
    public function newNoteEngine(Request $request){
        $em = $this->getDoctrine()->getManager();
        /* @var $client NoteEngine */
        $noteEntity = new NoteEngine();
        $noteEntity->setName('none');
        $em->persist($noteEntity);
        $em->flush();
        return new JsonResponse($noteEntity->getId());
    }
    /**
     * @Route("/gestion/AuditEngine/deleteIndicator", name="deleteIndicator")
     */
    public function deleteIndicator(Request $request){
        $em = $this->getDoctrine()->getManager();
        $indicator = $em->getRepository('AppBundle\Entity\AuditEngine\Indicator')
            ->findOneBy(['id' => $_POST['indicatorId']]);
        $em->remove($indicator);
        $em->flush();
        return new Response();
    }
    /**
     * @Route("/gestion/AuditEngine/deleteNote", name="deleteNote")
     */
    public function deleteNote(Request $request){
        $noteId = $_POST['noteId'];
        $em = $this->getDoctrine()->getManager();
        $note = $em->getRepository('AppBundle\Entity\AuditEngine\NoteEngine')
            ->findOneById($noteId);
        if($note){
            $em->remove($note);
            $em->flush();

        }

        return new Response();
    }

    /**
     * Return a JSON response with all the note generator from the AuditEngine
     * @Route("/gestion/AuditEngine/ressources/Notes", name="getAllNotes")
     */
    // public function getAllNotes(Request $request)
    //{
    //    $em = $this->getDoctrine()->getManager();
    //    /* @var $notes ArrayCollection */
    //    $notes = $em->getRepository('AppBundle\Entity\AuditEngine\NoteEngine')->findAll();
    //    /* @var $allNotes string[] (json[]) */
    //    $allNotes = [];
    //    /* @var $note NoteEngine */
    //    foreach ($notes as $note) {
    //        $serializer = SerializerBuilder::create()->build();//TODO injection
    //        $allNotes[] = $serializer->serialize($note, 'json');
    //    }
    //    return new JsonResponse($allNotes);
   // }

    /**
     * @Route("/config/AuditEngine/updateNote", name="updateNote")
     */
    public function updateNoteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($_POST['jsondata']);
        $modifiedNoteEngineArray = [];
        //$data = json_decode('{"indicatorArray":{"170":{"class":"class1","field":"class1.property1","filter":{"field":"class1.property1"},"minValue":"10","maxValue":"25","value":"ttr"}}}');
        if(property_exists($data,'indicatorArray')){
            $indicatorArray = $data->indicatorArray;
            foreach ($indicatorArray as $indicatorId => $indicator){
                /* @var $indicatorEntity Indicator*/
                $indicatorEntity = $em->getRepository('AppBundle\Entity\AuditEngine\Indicator')
                    ->findOneById($indicatorId);

                if(property_exists($indicator,'coeff')){
                    $indicatorEntity->setCoeff($indicator->coeff);
                }
                if(property_exists($indicator,'class')){
                    $indicatorEntity->setClass($indicator->class);
                }
                if(property_exists($indicator,'field')){
                    $indicatorEntity->setField($indicator->field);
                }
                if(property_exists($indicator,'minValue')){
                    $indicatorEntity->setMinValue($indicator->minValue);
                }
                if(property_exists($indicator,'maxValue')){
                    $indicatorEntity->setMaxValue($indicator->maxValue);
                }
                if(property_exists($indicator,'filter')){
                    $filter = $indicator->filter;
                    /* @var $filterEntity Filter */
                    $filterEntity = null;
                    if($indicatorEntity->getFilter()->isEmpty()){
                        $filterEntity = new Filter();
                        $indicatorEntity->addFilter($filterEntity);
                        $em->persist($filterEntity);
                    }else{
                        $filterEntity = $indicatorEntity->getFilter()->first();
                    }
                    if(property_exists($filter,'value')){
                            $filterEntity->setValue($filter->value);
                    }
                    if(property_exists($filter,'field')){
                        $filterEntity->setField($filter->field);
                    }
                    /*if(property_exists($indicator,'minValue')){
                        $filterEntity->setMinValue($filter->minValue);
                    }
                    if(property_exists($indicator,'maxValue')){
                        $filterEntity->setMaxValue($filter->maxValue);

                    }*/
                    //$em->merge($filterEntity);
                }
                if(!in_array($indicatorEntity->getNote(),$modifiedNoteEngineArray)){
                    $modifiedNoteEngineArray[] = $indicatorEntity->getNote();
                }
                $em->merge($indicatorEntity);
                $em->flush();

            }
        }
        /* @var $modifiedNoteEngine NoteEngine */
        foreach ($modifiedNoteEngineArray as $modifiedNoteEngine){
            $scheduledDate = date_create_from_format('d/m/Y H:i',$_POST['scheduledDate']);
            $recalculationPeriod = \DateInterval::createFromDateString($_POST['recalculationPeriod']);
            $now = new \DateTime() ;
            $lastSnapshotToRecalculate = date_sub(new \DateTime(),$recalculationPeriod);
            $this->get('ressource.factory')->get('note')->scheduleNoteUpdate(
                $modifiedNoteEngine,
                $scheduledDate,
                $lastSnapshotToRecalculate,
                $now);
        }
        if(property_exists($data,'noteArray')){
            $noteArray = $data->noteArray;
            foreach ($noteArray as $noteId => $note){
                /* @var $noteEntity NoteEngine*/
                $noteEntity = $indicatorEntity = $em->getRepository('AppBundle\Entity\AuditEngine\NoteEngine')
                    ->findOneById($noteId);
                if(property_exists($note,'name')){
                    $noteEntity->setName($note->name);
                    $em->merge($noteEntity);
                }
                if(property_exists($note,'isAvg')){
                    $noteEntity->setIsAvg($note->isAvg);
                }
            }
        }
        $em->flush();
        return new JsonResponse();
    }

    /**
     * @Route("/gestion/AuditEngine/noteData", name="noteData")
     */
    public function getNoteMetaData(Request $request){
        $em = $this->getDoctrine()->getManager();
        $noteEntities = $em->getRepository('AppBundle\Entity\AuditEngine\NoteEngine')->findAll(["name"=>'ASC']);
        $notes = [];
        /* @var $noteEntity NoteEngine */
        foreach ($noteEntities as $noteEntity){
            $note = [];
            $note['id'] = $noteEntity->getId();
            $note['name'] = $noteEntity->getName();
            $note['isAvg'] = $noteEntity->getIsAvg();

            $note['sgbd'] = $noteEntity->getSgbd();
            $note['versionPattern'] = $noteEntity->getVersionPattern();

            $indicatorEntities = $noteEntity->getIndicators();
            /* @var $indicatorEntity Indicator */
            $indicators = [];
            foreach ($indicatorEntities as $indicatorEntity){
                $indicator = [];
                $indicator['id'] = $indicatorEntity->getId();
                $indicator['class'] = $indicatorEntity->getClass();
                $indicator['coeff'] = $indicatorEntity->getCoeff();
                $indicator['field'] = $indicatorEntity->getField();
                //$indicator['fieldExactValue'] = $indicatorEntity->getField();
                $indicator['maxValue'] = $indicatorEntity->getMaxValue();
                $indicator['minValue'] = $indicatorEntity->getMinValue();
                $indicator['filter'] = [];
                if($indicatorEntity->getFilter()->first())
                {
                    $indicator['filter']['field'] = $indicatorEntity->getFilter()->first()->getField();
                    $indicator['filter']['value'] = $indicatorEntity->getFilter()->first()->getValue();
                }
                $indicators [] = $indicator;
            }
            $note['indicator'] = $indicators;
            $notes[] = $note;
        }

        $response['notes'] = $notes;
        return new JsonResponse($response);
    }
}
