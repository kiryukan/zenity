<?php


namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

class PerformanceBenchRessource extends AbstractEntityRessource
{
    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return [
        ];
    }

    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {
      return [$this->getRessource($snapshot->getPerformanceBench())];
    }
    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    public function getRessourceForInstance($instance,$startDate=null,$endDate=null){
      return $this->getRessource(
          $this->em->getRepository('AppBundle\Entity\ComplementaryFlow\PerformanceBench')
              ->createQueryBuilder("performanceBench")
              ->where("performanceBench.timestamp between :startDate and :endDate")
              ->limit(5)
              ->orderBy("performanceBench.cpu")
              ->setParameter("startDate",$startDate)
              ->setParameter("endDate",$endDate)
              ->getQuery()
              ->getResult()[5]
            );
    }
    public function compareInstance($instance1,$instance2,$startDate=null,$endDate=null){
      $r1 = $this->getRessourceForInstance($instance1,$startDate,$endDate);
      $r2 = $this->getRessourceForInstance($instance2,$startDate,$endDate);
      $result = [];
      foreach ($r1 as $key=>$r1Value){
        $r2Value = $r2[$key];
        if(is_numeric($r2Value) && is_numeric($r1Value) )
          $result[$key] = $r1Value/$r2Value;
      }
      return $result;
    }
    public function sortCloudInstance($intance,$provider){
      $cloudInstances = $this->em->getQueryBuilder()
      ->select('cloudInstance')
      ->from('cloudInstance','AppBundle\Entity\Cloud\Instance')
      ->getQuery()
      ->getResult();
      $candidates = [];
      foreach($cloudInstances as $cloudInstance){
        $instanceCompare = $this->compareInstance($instance,$cloudInstance->getPerformanceBench());
        $valueOverOneCount = 0;
        foreach($instanceCompare as $key=>$value){
          if($value > 1) $valueOverOneCount ++ ;
        }
        if($valueOverOneCount > (sizeOf($cloudInstance)/2) +1) $candidates[] = $cloudInstance;
      }
      $min_price = 0;
      $best_candidate = null;
      return usort($candidates,function($candidateA,$candidateB){
        return $candidateA->getValue() - $candidateB->getValue();
      });
    }
    private function chronoToSec($str){
      $str = str_replace('Ecoulé :', '', $str);
      $str = str_replace(' ', '', $str);
      $parsedDate = date_parse($str);
      return $parsedDate['second']+$parsedDate['minute']*60+$parsedDate['hour']*3600+$parsedDate['fraction'];
    }
    /**
     * @param $entity PerfStatInfo
     * @return mixed
     */
    public function getRessource($entity)
    {
        if ($entity)
          return [
            'insert' =>$entity->getInsert(),
            'cpu' =>$entity->getCpu(),
            'update' =>$entity->getUpdate(),
            'sqlplus' =>$entity->getSqlPlus(),
            'timestamp' =>date_format($entity->getTimestamp(),'Y-m-d H:i:s')
          ];
        else return [
          'insert' =>null,
          'cpu' =>null,
          'update' =>null,
          'sqlplus' =>null,
          'timestamp' =>null
        ];
    }
    public function updateFromJson($entity,$json)
    {
        $jsonDecoded = json_decode($json,true);
        if(array_key_exists('INSERT',$jsonDecoded))
          $entity->setInsert($this->chronoToSec($jsonDecoded['INSERT']));
        if(array_key_exists('CPU',$jsonDecoded))
          $entity->setCpu($this->chronoToSec($jsonDecoded['CPU']));
        if(array_key_exists('UPDATE',$jsonDecoded))
          $entity->setUpdate($this->chronoToSec($jsonDecoded['UPDATE']));
        if(array_key_exists('SQLPLUS',$jsonDecoded))
          $entity->setSqlPlus($this->chronoToSec($jsonDecoded['SQLPLUS']));
        if(array_key_exists('TIMESTAMP',$jsonDecoded))
          $entity->setTimestamp(date_create_from_format('Y-m-d H:i:s',$jsonDecoded['TIMESTAMP']));
        return $entity;
    }
    public function updateFromArray($entity,$array)
    {
        if(array_key_exists('INSERT',$array))
          $entity->setInsert($array['INSERT']);
        if(array_key_exists('CPU',$array))
          $entity->setCpu($array['CPU']);
        if(array_key_exists('UPDATE',$array))
          $entity->setUpdate($array['UPDATE']);
        if(array_key_exists('SQLPLUS',$array))
          $entity->setSqlPlus($array['SQLPLUS']);
        if(array_key_exists('TIMESTAMP',$array))
          $entity->setTimestamp(date_create_from_format('Y-m-d H:i:s',$array['TIMESTAMP']));
        return $entity;
    }
}
