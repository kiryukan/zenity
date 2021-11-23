<?php


namespace AppBundle\RessourceProvider\Traits;


use AppBundle\RessourceProvider\MetadataProviderTrait;
use AppBundle\RessourceProvider\RessourceMetadataTrait;
use Doctrine\DBAL\Query\QueryException;
use Doctrine\ORM\EntityManager;

trait EntityStatisticsMetadataTrait
{
    /** @var  EntityManager */
    protected $em;
    private $className;
    public function getMetadataStats(){
        $stats = [];
        $metadata = $this->getMetadata();
        $className = "AppBundle\\Entity\\Snapshots\\".ucfirst($metadata['title']);

        foreach (array_keys($metadata['properties']) as $indicatorName){
            try{
                $stats[$indicatorName] = [
                    "min"=>$this->getMin($className,$indicatorName),
                    "max"=>$this->getMax($className,$indicatorName),
                    "avg"=>$this->getAvg($className,$indicatorName)
                ];
            }catch(QueryException $e){
                //
            }

        }
        return $stats;
    }
    private function getMin($className,$indicatorName){

         return $this->em->createQueryBuilder()
            ->select('max(obj.'.$indicatorName)
            ->from($className,"obj")
            ->getQuery()
            ->getFirstResult();
    }
    private function getMax($className,$indicatorName){
        return $this->em->createQueryBuilder()
            ->select('MAX(:indicator)')
            ->from($className,"obj")
            ->setParameter('indicator',$indicatorName)
            ->getQuery()
            ->getFirstResult();

    }
    private function getAvg($className,$indicatorName){
        return $this->em->createQueryBuilder()
            ->select('indicator')
            ->from($className,"obj")
            ->setParameter('indicator',$indicatorName)
            ->getQuery()
            ->getFirstResult();
    }
}