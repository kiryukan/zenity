<?php

namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\AbstractClass\AbstractVirtualRessource;

class LongSessionsMetadataRessource extends AbstractVirtualRessource
{
    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return [
            "title"=>"locksMetadata",
            "properties"=>[
                'longSessionCountAvg'=>[
                    "type"=>"number"
                ],
                'maxSimultaneousLongSession'=>[
                    "type"=>"number"
                ],
                'longSessionDurationAvg'=>[
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
            ->select($qb->expr()->count('long_session.id').'AS longSessionCount','SUM(long_session.duree)'.'AS longSessionDuration')
            ->from('AppBundle\Entity\ComplementaryFlow\LongSession','long_session')
            ->where('long_session.snapshot = :snap_id')
            ->groupBy('long_session.timestamp')
            ->setParameter('snap_id',$snapshot->getId())
            ->getQuery()
            ->execute();
            $longSessionCountArray  = array_map(function($item) {
                return (int)$item['longSessionCount'];
            },$result);
            $longSessionDurationArray  = array_map(function($item) {
                return (int)$item['longSessionDuration'];
            },$result);
            $segmentCount = 12;
            $maxSimultaneousLongSession = max($longSessionCountArray);
            if(count($result) !== 0){
                $longSessionCountAvg = array_sum($longSessionCountArray) / $segmentCount;
                $longSessionDureeAvg = array_sum($longSessionDurationArray) / $segmentCount;
            }else{
                $longSessionCountAvg = 0;
                $longSessionDureeAvg = 0;
            }
            return [[
                'longSessionCountAvg'=>$longSessionCountAvg,
                'maxSimultaneousLongSession'=>$maxSimultaneousLongSession,
                'longSessionDurationAvg'=>$longSessionDureeAvg,
            ]];
    }

}