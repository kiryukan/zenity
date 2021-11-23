<?php

namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractVirtualRessource;

class LocksMetadataRessource extends AbstractVirtualRessource
{
    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return [
            "title"=>"locksMetadata",
            "properties"=>[
                'lockCountAvg'=>[
                    "type"=>"number"
                ],
                'maxSimultaneousLock'=>[
                    "type"=>"number"
                ],
                'lockDurationAvg'=>[
                    "type"=>"number"
                ]
            ]
        ];
    }

    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb
            ->select($qb->expr()->count('instance_lock.id').'AS lockCount','SUM(instance_lock.duree)'.'AS lockDuration')
            ->from('AppBundle\Entity\ComplementaryFlow\Lock','instance_lock')
            ->where('instance_lock.snapshot = :snap_id')
            ->groupBy('instance_lock.timestamp')
            ->setParameter('snap_id',$snapshot->getId())
            ->getQuery()
            ->execute();
            $lockCountArray  = array_map(function($item) {
                ?><script>console.log("Locks count Array: "+<?php echo $lockCountArray ?> );</script><?php
                return (int)$item['lockCount'];
            },$result);
            $lockDurationArray  = array_map(function($item) {
                return (int)$item['lockDuration'];
            },$result);
            $segmentCount = 12;
            $maxSimultaneousLock = max($lockCountArray);

            if(count($result) !== 0){
                $lockCountAvg = array_sum($lockCountArray) / $segmentCount;
                $lockDureeAvg = array_sum($lockDurationArray) / $segmentCount;
            }else{
                $lockCountAvg = 0;
                $lockDureeAvg = 0;
            }
            return [[
                'lockCountAvg'=>$lockCountAvg,
                'maxSimultaneousLock'=>$maxSimultaneousLock,
                'lockDurationAvg'=>$lockDureeAvg,
            ]];
    }

}