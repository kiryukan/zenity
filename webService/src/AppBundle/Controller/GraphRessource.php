<?php
/**
 * Dear maintainer, i wrote this code as a young student
 * sorry for that, i sincerelly apologize and good luck.
 * When i wrote this code only I and god knew what it was,
 * Now, only god know
 * so if you hope to modify that code and try to optimize it increment the following counter:
 * total_hours_wasted_here = 66
 * ps: dont hesitate one second to ditch all of this SHIT 
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Base\Base;
use AppBundle\Entity\Base\Instance;
use AppBundle\Entity\Client;
use AppBundle\Entity\Snapshots\Note;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\Services\AuditEngine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\PersistentCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Get a set of JSON data via URL
 * Type of graph:
 *      Data over time  // @link dataOverTime(Request $request)
 *      Note over time  // @link noteOverTime(Request $request)
 *
 * Class GraphRessource
 * @package AppBundle\Controller
 *
 */
class GraphRessource extends Controller
{
    /**
     * @Route("/api/graph/advisory",name="advisory_graph")
     */
    public function advisory(Request $request)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        $em = $this->getDoctrine()->getManager();
        $instanceId = $request->get('instanceId');
        $instanceEntity = $em->getRepository('AppBundle\Entity\Base\Instance')->findOneById($instanceId);
        $advisories = [];
        /* $advisory Advisory */
        $currentMemoryMap = [];

        foreach ($instanceEntity->getAdvisory() as $advisory){
            $advisoryMap = $advisory->getAdvisoryMap();
            foreach ($advisoryMap as $key=>$value){
                $advisories[$advisory->getName()][$value['size']] = $value['gainFctr'];
                if($value['sizeFctr'] === 1){
                    $currentMemoryMap[$advisory->getName()] = $value['size'];
                }
            }
        }
        ksort($advisories);
        $advisories['memoryMap'] = $currentMemoryMap;
        return new JsonResponse($advisories);
    }

    /**
     * @Route("/api/graph/advisory/{name}",name="namedadvisory_graph")
     */
    public function namedAdvisory(Request $request,$name)
    {
        //we do not write on session so dont need to lock it
        session_write_close();
        $em = $this->getDoctrine()->getManager();
        $instanceId = $request->get('instanceId');
        $instanceEntity = $em->getRepository('AppBundle\Entity\Base\Instance')->findOneById($instanceId);
        $advisory = $em->getRepository('AppBundle\Entity\Base\Advisory')->findOneBy(['instance'=>$instanceId,'name'=>$name]);
        /* $advisory Advisory */
        $currentMemoryMap = [];
        if($advisory === null)return new JsonResponse();
        $advisoryMap = $advisory->getAdvisoryMap();
        $advisories = [];
        foreach ($advisoryMap as $key=>$value){
            $advisories[$advisory->getName()][$value['size']] = $value['gainFctr'];
            if($value['sizeFctr'] === 1){
                $currentMemoryMap[$advisory->getName()] = $value['size'];
            }
        }
        $advisories['memoryMap'] = $currentMemoryMap;

        return new JsonResponse($advisories);
    }
    /** This method take an object (graph) in the HttpRequest who contain all element to gather graph (see param doc)
     *  and return a JsonReponse containing all the data gathered for the graph
     *
     *
     * @param $request [
     *          'instanceId'=>in,            // contain the id of the instance where the graph is selected
     *          'from'=>String(optional),               // (Y/m/d H\h:i) the beggining of selection  (if not set: year 0)
     *          'to'=>String(optional),                 // (Y/m/d H\h:i) the end of selection (if not set : now )
     *          graphLayout:{
     *              limit:$limitValue                          // optional set the number of point to gather (overTime case)
     *              data:{
     *                  "collectStyle1":[
     *                      "className":{
     *                          "indicators":["i1",i2","i3],
     *                          "filter":["i4::value"]          // optional
     *                      }
     *                  ],
     *                  "collectStyle2":[
     *                      "className":{
     *                          "indicators":["i1",i2","i3],
     *                          "filter":["i4::value"]          // optional
     *                      ]
     *                  }
     *              }
     *          }
     * ]
     *
     * @return JsonResponse graph :
     * {
     *  x:[t1,t2,...]//optionnal
     *  {
     *      collectStyle1: results,
     *      collectStyle2: results...} // the style of the graph depend on collectStyle we pick
     *  }
     * }
     * possible collectStyle:
     * overTime:
     *      "data":                                                                         //data_overTime
     *          "x":[t1,t2,t3...]
     *          ["e1"]["i1"]:[v1,v2,v3...]
     *          ["e1"]["i2"]:[v1,v2,v3...]
     *          ["e2"]["i1"]:[v1,v2,v3...]
     *          ["e2"]["i2"]:[v1,v2,v3...]
     *
     *          "sumOfElement":                                                             //sumOfElement_OverTime
     *              "i1":[sum("e1.i1"[v1]+"e2.i1"[v1]),sum("e1.i1"[v2]+"e2.i1"[v2])...]
     *              "i2":[sum("e1.i2"[v1]+"e2.i2"[v1]),sum("e1.i2"[v2]+"e2.i2"[v2])...]
     *
     *          "sumOfIndicator":                                                           //sumOfIndicator_OverTime
     *              "e1":[sum("e1.i1"[v1]+"e1.i2"[v1]),sum("e1.i1"[v2]+"e1.i2"[v2])...]
     *              "e2":[sum("e2.i1"[v1]+"e2.i2"[v1]),sum("e2.i1"[v2]+"e2.i2"[v2])...]
     *          "avgOfElement":                                                             //avgOfElement_OverTime
     *              "i1":[avg("e1.i1"[v1]+"e2.i1"[v1]),avg("e1.i1"[v2]+"e2.i1"[v2])...]
     *              "i2":[avg("e1.i2"[v1]+"e2.i2"[v1]),avg("e1.i2"[v2]+"e2.i2"[v2])...]
     *
     *          "avgOfIndicator":                                                           //avgOfIndicator_OverTime
     *              "e1":[avg("e1.i1"[v1]+"e1.i2"[v1]),avg("e1.i1"[v2]+"e1.i2"[v2])...]
     *              "e2":[avg("e2.i1"[v1]+"e2.i2"[v1]),avg("e2.i1"[v2]+"e2.i2"[v2])...]
     *
     * total:                                                                              //subStyleName_total
     *      Same style name as overTime but for each style we do :
     *      Sum of all element in array
     *      No x axis
     *
     * avg:                                                                                //subStyleName_avg
     *      Same style name as overTime but for each style we do :
     *      Avg of all element in array
     *      No x axis
     * other:
     *    "firstValueOfElement":                                                           //firstValueOfElement_other
     *          i1:"v1"
     *          i2:"v2"
     * @Route("/api/graph",name="graph")
     */
    public function graph(Request $request)
    {
        //we do not write on session so dont need to lock it
        session_write_close();

        // this process can be very long
        set_time_limit(3000);

        $snapshots = $this->extractSnapshots($request);
        $nbSnapshot = sizeof($snapshots);
        $graphLayout = json_decode($request->get('layout'), true);
        //--------------------------------------------------------------------------------------------------------------
        $sampleValue = 1;
        if (isset($graphLayout['parameters']['samplingValue'])) {
            $sampleValue = $graphLayout['parameters']['samplingValue'];
        }
        $graph = $this->initializeGraph($snapshots, null, $graphLayout);
        if(empty($graph['x'])){
            $response = new JsonResponse([
                'code'=>101,
                'msg'=>'no snapshot on this period'
            ]);
            return $response->setStatusCode(400);
        }
        //--------------------------------------------------------------------------------------------------------------
        $rawData = $this->extractRawDataFromGraphLayout($graphLayout, $snapshots, $nbSnapshot);

        $emptyReturn = true;
        foreach ($graphLayout['data'] as $collectStyle => $classes) {
            $graph[$collectStyle] = [];

            foreach ($classes as $className => $class) {
                $indicator = $class['indicators'];
                if ($indicator === "*"){
                    $indicator = null;
                }
                $filter = (isset($class['name_filter'])) ? $class['name_filter'] : null;
                if(isset($rawData[$className])){
                    $data = $rawData[$className];
                    $graph[$collectStyle][$className] =
                        $this->extractDataFromCollectStyle($collectStyle, $data, $indicator, $filter, $nbSnapshot / $sampleValue);
                }
            }
            if(empty($graph[$collectStyle])){
                unset($graph[$collectStyle]);
            }else{
                $emptyReturn = false;
            }
        }
        if($emptyReturn){
            $response = new JsonResponse([
                'code'=>102,
                'msg'=>'Indicator dont exist'
            ]);
            return $response->setStatusCode(400);
        }
        return new JsonResponse($graph);
    }
    /** Return raw data formated like :
     *              "className":[
     *                  "filterId/None":[
     *                     "indicator":["element":[values]]
     *              ]
     * return all
     * @param $graphLayout String (JSON) @see graph
     */
    private function extractRawDataFromGraphLayout($graphLayout, $snapshots, $nb_snapshots)
    {
        $globalFilter = null;
        if(isset($graphLayout['parameters']['global_filter'])){
            $globalFilter = $graphLayout['parameters']['global_filter'];
        }

        $nameFilter = null;
        if(isset($graphLayout['parameters']['name_filter'])){
            $nameFilter = $graphLayout['parameters']['name_filter'];
        }
        $sampleValue = 1;
        if (isset($graphLayout['parameters']['samplingValue'])) {
            $sampleValue = $graphLayout['parameters']['samplingValue'];
        }
        $collectStyles = $graphLayout['data'];
        $classesToGather = [];
        foreach ($collectStyles as $collectStyle) {
            foreach ($collectStyle as $className => $classContent) {
                if (array_key_exists($className, $classesToGather) === false) {
                    $classesToGather[$className] = [];

                }
                $classesToGather[$className] = (array_merge_recursive($classesToGather[$className], $classContent));
               // array_push($classesToGather[$className]['indicators'], $classContent['indicators']);

            }
        }
        $data = [];
        foreach ($classesToGather as $className => $class) {
            $data[$className] = [];
            $indicators = $class['indicators'];
            if ($indicators === "*"){
                $indicators = null;
            }
            $i = 0;
            $fillByNull = true;
            $values = [];
            foreach ($snapshots as $snapshot) {
                $filter = null;
                if (isset($globalFilter[$className])){
                    $filter = $globalFilter[$className];
                }
                $values = $this->getValues($snapshot, $className, $indicators, $filter);
                    foreach ($values as $indicatorName => $indicatorValue) {
                        if(isset($data[$className][$indicatorName]) === false){
                            $data[$className][$indicatorName] = [];
                        }
                        if ($nameFilter != null){
                            if(isset($indicatorValue[$nameFilter])){
                                $value = $indicatorValue[$nameFilter];
                                if($value !== null){
                                    $fillByNull = false;
                                }
                                if (!isset($data[$className][$indicatorName][$nameFilter])) {
                                    $data[$className][$indicatorName][$nameFilter] = array_fill(0, $nb_snapshots, null);
                                }
                                $data[$className][$indicatorName][$nameFilter][$i] = $value;
                            }
                        }else{
                            foreach ($indicatorValue as $elementName=>$value){
                                if($value !== null){
                                    $fillByNull = false;
                                }
                                if (!isset($data[$className][$indicatorName][$elementName])) {
                                    $data[$className][$indicatorName][$elementName] = array_fill(0, $nb_snapshots, null);
                                }
                                $data[$className][$indicatorName][$elementName][$i] = $value;
                            }
                        }
                    }
                $i++;
            }
           // var_dump($values);
            if($fillByNull){
                $data[$className] = null;
            }
        }
        $data = $this->sample($data, $sampleValue, $nb_snapshots);

        return $data;
    }

    /**
     * Gather only few point
     * @param $data
     * @param $sampleValue
     */
    private function sample($data, $sampleValue, $nbSnapshot)
    {
        if ($sampleValue === 1) return $data;
        $dataToReturn = [];
        foreach ($data as $className => $classContent) {
            foreach ($classContent as $indicatorName => $indicatorContent) {
                foreach ($indicatorContent as $elementName => $elementContent) {
                    $value = 0;
                    $j = 0;
                    for ($i = 0; $i < $nbSnapshot; $i++) {
                        $value += $elementContent[$i];
                        if ($i % $sampleValue === $sampleValue - 1) {
                            $dataToReturn[$className][$indicatorName][$elementName][$j] = ($value !== 0) ? $value / $sampleValue : 0;
                            $j++;
                            $value = 0;
                        }
                    }
                }
            }
        }
        return $dataToReturn;
    }
    /**
     *
     * @param $snapshot Snapshot
     * @param $className String
     * @param null $fields String[]
     * @param null $filters String[]
     * @return array
     */
    private function getValues($snapshot, $className, $fields = null, $filters = null)
    {
        $filters = ($filters === 'None') ? Null : $filters;
        $filters = (is_array($filters) ? $filters :
            array('0' => $filters));

        $values = [];
        if($fields === null){
            $values  = $this->get('ressource.factory')
                ->get($className)
                ->getRessourceForSnapshot($snapshot)[0];
            foreach ($values as $indicatorName=>$value){
                $values[$indicatorName] = [$value];
            }
        }else{
            $fields = (is_array($fields) ? $fields :
                array('0' => $fields));
            foreach ($fields as $field){
                $value = $this->get('ressource.factory')
                    ->get($className)
                    ->getRessourceBySnapshotAndIndicator($snapshot,$field,$filters);
                $values[$field]  = $value;
                // $values[$field] = $this->get('accessor')->getPropertyFromSnapshot($snapshot,$className,$field,$filters);
            }
        }
        return $values;
    }
    //-------------------------------------------------------Private functions--------------------------------------------------------------------------------//

    /**
     * @param $snapshots ArrayCollection
     * @param $fields String[]
     * @return []
     */
    private function initializeGraph($snapshots, $fields = null, $samplingValue = 1)
    {

        $fields = (is_array($fields) ? $fields :
            array('0' => $fields));

        $graph = [];
        $graph['x'] = [];

        foreach ($fields as $field) {
            if ($field != null) {
                $graph[$field] = [];
            }
        }

        /* Initialise empty set($graph['fieldName'] for each field */
        $i = 0;
        /* @var $snapshot Snapshot */
        foreach ($snapshots as $snapshot) {
            if ($i % $samplingValue === 0) {
                $graph['x'][] = $snapshot->getStartDate()->format('Y-m-d H:i:s');
            }
            $i++;
        }
        return $graph;
    }

    /**
     * @param $instance Instance
     * @param $from String          (Y/m/d H\h:i)
     * @param $to String          (Y/m/d H\h:i)
     * @return ArrayCollection
     */
    private function extractSnapshots(Request $request)
    {
        /* Select the instance by url criteria or instance ID, return an empty page if not found*/
        if ($instanceId = ($request->get('instanceId'))) {
            $instance = $this->getInstanceById($instanceId);
        } else {
            return null;
        }
        $from = urldecode($request->get('from'));
        $to = urldecode($request->get('to'));
        $dateFrom = ($dateFrom = \DateTime::createFromFormat('d/m/Y H:i', $from)) ? $dateFrom :
            \DateTime::createFromFormat('m-j-y H:i', "01-01-70 00:00");                 // Year 0
        $dateTo = ($dateTo = \DateTime::createFromFormat('d/m/Y H:i', $to)) ? $dateTo :
            new \DateTime();                                                 // Now
        return $instance->getSnapshotsBetween($dateFrom, $dateTo);
    }
    /**
     * Get an instance with her Id
     * @param $instanceId int
     * @return Instance
     */
    private function getInstanceById($instanceId)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $instance Instance */
        $instance = $em->getRepository('AppBundle\Entity\Base\Instance')
            ->findOneBy(['id' => $instanceId]);
        if ($instance === null) {
            throw new \Exception("l'instance n'existe pas ");
        }
        return $instance;
    }


    //--------------------------------------

    /**
     * @param $collectStyle
     * @param $data
     * @param $indicators
     * @param $filters
     * @param $nbSnapshot
     * @return array
     */
    private function extractDataFromCollectStyle($collectStyle, $data, $indicators, $filters, $nbSnapshot){
        $preciseStyle = explode('_', $collectStyle)[0];
        $overallStyle = explode('_', $collectStyle)[1];
        $dataToReturn = [];
        if($indicators == null){
            $indicators = array_keys($data);
        }
        if ($filters !== null) {
            foreach ($indicators as $indicator) {
                $tmpData = [];
                foreach ($filters as $element) {
                    $tmpData[$element] = $data[$indicator][$element];
                }
                $data[$indicator] = $tmpData;
            }
        } else {
            $tmpData = [];
            foreach ($indicators as $indicator) {
                $tmpData[$indicator] = $data[$indicator];
            }
            $data = $tmpData;
        }
        switch ($preciseStyle) {
            case "data":
                    $dataToReturn = $this->extractFromCollectStyle_data($data,$nbSnapshot);
                break;
            case "sumOfElement":
                    $dataToReturn = $this->extractFromCollectStyle_sumOfElement($data,$nbSnapshot);
                break;
            case "avgOfElement":
                    $dataToReturn = $this->extractFromCollectStyle_avgOfElement($data,$nbSnapshot);
                break;
            case "sumOfIndicator":
                // TODO
                break;
            case "avgOfIndicator":
                // TODO
                break;
        }
        switch ($overallStyle) {
            case "overTime":
                //pass
                break;
            case "total":
                $dataToReturn = $this->extractFromCollectStyle_total($dataToReturn,$nbSnapshot);
                break;
            case "avg":
                $dataToReturn = $this->extractFromCollectStyle_avg($dataToReturn,$nbSnapshot);
                break;
            case "one":
                $dataToReturn = $this->extractFromCollectStyle_one($dataToReturn,$nbSnapshot);
                break;
        }
        return $dataToReturn;
    }

    /**
     * @param $data
     * @param $indicators
     * @param $filters
     * @param $nbSnapshot
     * @return mixed
     */
    private function extractFromCollectStyle_data($data, $nbSnapshot){
        return $data;
    }
    /**
     * @param $data
     * @param $indicators
     * @param $filters
     * @param $nbSnapshot
     * @return array
     */
    private function extractFromCollectStyle_one($data, $nbSnapshot){
        $dataToReturn = [];

        foreach ($data as $indicatorName => $indicatorContent) {
            foreach ($indicatorContent as $elementName => $elementContent) {

                if(is_array($elementContent)) {
                        for ($i = 0 ; $i <= $nbSnapshot; $i++){
                            if($elementContent[$i] !== null){
                                $dataToReturn[$indicatorName][$elementName][0] = $elementContent[$i];
                                break;
                            }
                        }
                }else{
                    $dataToReturn[$indicatorName][$elementName][0] = $elementContent;
                }
            }
        };

        return $dataToReturn;
    }
    /**
     * @param $data
     * @param $indicators
     * @param $filters
     * @param $nbSnapshot
     * @return array
     */
    private function extractFromCollectStyle_sumOfElement($data, $nbSnapshot){
        $dataToReturn = [];
        foreach ($data as $indicatorName => $indicatorContent) {
            $dataToReturn[$indicatorName]['all'] = [];
            foreach ($indicatorContent as $elementName => $elementContent) {
                if(is_array($elementContent)) {
                    for ($i = 0; $i < $nbSnapshot; $i++) {
                        $value = $elementContent[$i];
                        if(is_numeric($value)){
                            if (!isset($dataToReturn[$indicatorName]['all'][$i])) {
                                $dataToReturn[$indicatorName]['all'][$i] = 0;
                            }
                            $dataToReturn[$indicatorName]['all'][$i] += $value;
                        }else{
                            if (!isset($dataToReturn[$indicatorName]['all'][$i])) {
                                $dataToReturn[$indicatorName]['all'][$i] = $value;
                            }
                        }
                    }
                }else{
                    $dataToReturn[$indicatorName]['all'] = "None";
                }
            }
        }
        return $dataToReturn;
    }
    /**
     * @param $data
     * @param $indicators
     * @param $filters
     * @param $nbSnapshot
     */
    private function extractFromCollectStyle_avgOfElement($data, $nbSnapshot)
    {
        $dataToReturn = [];
        foreach ($data as $indicatorName => $indicatorContent) {
            $dataToReturn[$indicatorName]['all'] = [];
            foreach ($indicatorContent as $elementName => $elementContent) {
                if (is_array($elementContent)) {
                    for ($i = 0; $i < $nbSnapshot; $i++) {
                        $value = $elementContent[$i];

                        if (!isset($dataToReturn[$indicatorName]['all'][$i])) {
                            $dataToReturn[$indicatorName]['all'][$i] = 0;
                        }
                        $dataToReturn[$indicatorName]['all'][$i] += $value;
                    }
                } else {
                    $dataToReturn[$indicatorName]['all'] = "None";
                }
            }
            for ($i = 0; $i < $nbSnapshot; $i++) {
                if (is_array($dataToReturn[$indicatorName]['all'])
                    && is_numeric($dataToReturn[$indicatorName]['all'][$i])) {
                        $dataToReturn[$indicatorName]['all'][$i] /= sizeof($indicatorContent);
                }
            };/**/
        }
        return $dataToReturn;
    }
    private function extractFromCollectStyle_total($data,$nbSnapshot){
        foreach ($data as $indicatorName => $indicatorContent) {
            $data[$indicatorName] = (array) $data[$indicatorName];
            foreach ($indicatorContent as $elementName => $elementContent) {
                if(is_array($elementContent)){
                    $value = array_sum($elementContent);
                    if($value !== 0){
                        $data[$indicatorName][$elementName] = array_sum($elementContent);
                    }else{ //all element is 0 or non numeric, so we take the last one
                        $data[$indicatorName][$elementName] = $elementContent[sizeof($elementContent)-1];
                    }

                }
            }
        }
        return $data;
    }

    private function extractFromCollectStyle_avg($data,$nbSnapshot){
        foreach ($data as $indicatorName => $indicatorContent) {
            foreach ($indicatorContent as $elementName => $elementContent) {
                if(is_array($elementContent)){
                    $value = array_sum($elementContent);
                    if($value !== 0){
                        $data[$indicatorName][$elementName] = array_sum($elementContent) / sizeof($elementContent);
                    }else{ //all element is 0 or non numeric, so we take the last one

                        $data[$indicatorName][$elementName] = $elementContent[sizeof($elementContent)-1];
                    }
                }
            }
        }
        return $data;
    }
}
