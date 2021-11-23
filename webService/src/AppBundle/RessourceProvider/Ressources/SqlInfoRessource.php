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
use AppBundle\Entity\Snapshots\SqlInfo;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class SqlInfoRessource extends AbstractEntityRessource
{
    //Use EntityMetadataStatistics;
    /**
     * @return mixed
     */
    function getMetadata()
    {
        return [
            "title" => "sqlInfo",
            "properties" => [
                'repeatedRequestMemory' => [
                    "type" => "number"
                ],
                'repeatedRequestPercent' => [
                    "type" => "number"
                ],
                'nbExec' => [
                    "type" => "integer"
                ],
                "%RequestWithTotalCpuOver25" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalCpuOver50" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalCpuOver75" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalGetsOver25" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalGetsOver50" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalGetsOver75" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalReadsOver25" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalReadsOver50" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalReadsOver75" => [
                    "type" => "integer"
                ],
                "%RequestWithRowPerExecUnder500" => [
                    "type" => "integer"
                ],
                "%RequestWithRowPerExecUnder1000" => [
                    "type" => "integer"
                ],
                "nNbRequestWithTotalParseOver25" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalParseOver50" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalParseOver75" => [
                    "type" => "integer"
                ],
                "%RequestWithVersionCountOver25" => [
                    "type" => "integer"
                ],
                "%RequestWithVersionCountOver50" => [
                    "type" => "integer"
                ],
                "%RequestWithTotalElapTimeOver25" => [
                    "type" => "number"
                ],
                "%RequestWithTotalElapTimeOver50" => [
                    "type" => "number"
                ],
                "%RequestWithTotalElapTimeOver75" => [
                    "type" => "number"
                ],
            ]
        ];
    }
        public function updateFromJson($entity, $json)
    {
        $jsonDecoded = json_decode($json, true);
        if (!$entity) {
            $entity = new SqlInfo();
        }
        $entity->setRepeatedRequestMemory(
            (isset($jsonDecoded['repeatedRequestMemory'])) ? $jsonDecoded['repeatedRequestMemory'] : null );
        $entity->setRepeatedRequestPercent(
            (isset($jsonDecoded['repeatedRequestPercent'])) ? $jsonDecoded['repeatedRequestPercent'] : null );
        $entity->setNbExec(
            (isset($jsonDecoded['nbExec'])) ? $jsonDecoded['nbExec'] : null );
        if (isset($jsonDecoded['Request'])) {
            foreach ($jsonDecoded['Request'] as $jsonDecodedRequests) {
                $request = new Request();
                $this->ressourceFactory->get('request')->updateFromJson($request, json_encode($jsonDecodedRequests));
                $this->em->persist($request);
                $entity->addRequest($request);
            }
        }
    }

    /**
     * @param $entity SqlInfo
     * @return array
     */
    public function getRessource($entity)
    {

        return [
            'repeatedRequestMemory' => $entity->getRepeatedRequestMemory(),
            'repeatedRequestPercent' => $entity->getRepeatedRequestPercent(),
            'nbExec' => $entity->getNbExec(),
            'requests' => $this->getRequestsRessource($entity)
        ];
    }

    /**
     * @param $entity SqlInfo
     * @return array
     */
    private function getRequestsRessource($entity){
        $requests = [];
        foreach ($entity->getRequests() as $requestEntity) {
            $requests[] = $this->ressourceFactory->get('request')->getRessource($requestEntity);
        }
        return $requests;
    }
    public function getRessourceForSnapshot($snapshot)
    {
        return [$this->getRessource($snapshot->getSqlInfo())];
    }

    /**/
    /**
     * @param $entity SqlInfo
     * @param $indicatorName
     * @return mixed|null
     */
    public function getRessourceByIndicator($entity, $indicatorName)
    {

        if (isset($this->getRessource($entity)[$indicatorName])) {
            return $this->getRessource($entity)[$indicatorName];
        } elseif (preg_match('/%RequestWith(?<indicator>[A-Z].*)(?<comparator>Over|Under|Equal)(?<value>[0-9]{1,}(?:_[0-9]+)?)/', $indicatorName, $matches)) {
            $value = floatval(str_replace('_', '.', $matches['value']));
            $indicator = lcfirst($matches['indicator']);
            $nbRequest = sizeof($entity->getRequests());
            if ($matches['comparator'] === 'Over') {
                return $this->getNbRequestWithIndicatorOver($entity,$indicator, $value)/$nbRequest;
            } else if ($matches['comparator'] === 'Under') {
                return $this->getNbRequestWithIndicatorUnder($entity,$indicator, $value)/$nbRequest;
            } else if ($matches['comparator'] === 'Equal') {
                return $this->getNbRequestWithIndicatorEqual($entity,$indicator, $value)/$nbRequest;
            }
        }elseif (preg_match('/nbRequestWith(?<indicator>[A-Z].*)(?<comparator>Over|Under|Equal)(?<value>[0-9]{1,}(?:_[0-9]+)?)/', $indicatorName, $matches)) {
            $value = floatval(str_replace('_', '.', $matches['value']));
            $indicator = lcfirst($matches['indicator']);
            $nbRequest = sizeof($entity->getRequests());
            if ($matches['comparator'] === 'Over') {
                return $this->getNbRequestWithIndicatorOver($entity,$indicator, $value);
            } else if ($matches['comparator'] === 'Under') {
                return $this->getNbRequestWithIndicatorUnder($entity,$indicator, $value);
            } else if ($matches['comparator'] === 'Equal') {
                return $this->getNbRequestWithIndicatorEqual($entity,$indicator, $value);
            }
        }
        return null;
    }

    /**
     * @param $snapshot Snapshot
     * @param $indicatorName
     * @param null $filterArray
     *
     * @return mixed|null
     */
    public function getRessourceBySnapshot($snapshot){
        return [$this->getRessource($snapshot->getSqlInfo())];
    }
    /**
     * @param $snapshot Snapshot
     * @param $indicatorName
     * @param null $filterArray
     * @return mixed|null
     */
    public function getRessourceBySnapshotAndIndicator($snapshot,$indicatorName,$filterArray=null){
        return [$this->getRessourceByIndicator($snapshot->getSqlInfo(), $indicatorName)];
    }

    /**
     * @param $entity SqlInfo
     * @param $indicatorName
     * @param $hitValue
     * @return int
     */
    private function getNbRequestWithIndicatorOver($entity,$indicatorName, $hitValue)
    {
        $nb = 0;
        /* @var $request Request */

        foreach ($this->getRequestsRessource($entity) as $request) {
            $value = (isset($request[$indicatorName]))?$request[$indicatorName]:null;
            if ($value > $hitValue ) {
                $nb++;
            }
        }
        return $nb;
    }

    /**
     * @param $entity SqlInfo
     * @param $indicatorName
     * @param $hitValue
     * @return int
     */
    private function getNbRequestWithIndicatorUnder($entity,$indicatorName, $hitValue)
    {
        $nb = 0;
        /* @var $request Request */
        foreach ($this->getRequestsRessource($entity) as $request) {

            $value = (isset($request[$indicatorName]))?$request[$indicatorName]:null;
            if ($value < $hitValue ) {
                $nb++;
            }
        }
        return $nb;
    }

    /**
     * @param $entity SqlInfo
     * @param $indicatorName
     * @param $hitValue
     * @return int
     */
    private function getNbRequestWithIndicatorEqual($entity,$indicatorName, $hitValue)
    {
        $nb = 0;
        /* @var $request Request */
        foreach ($this->getRequestsRessource($entity) as $request) {
            $value = (isset($request[$indicatorName]))?$request[$indicatorName]:null;
            if ($hitValue == $value) {
                $nb++;
            }
        }
        return $nb;
    }

}