<?php


namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\Entity\Snapshots\Stat;
use AppBundle\RessourceProvider\AbstractClass\AbstractVirtualRessource;

class StatisticsRessource extends AbstractVirtualRessource
{
    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return [
            "title"=>"statistics",
            "properties"=>[
                "PhysicalWritesCache"=>[
                    "type"=>"number"
                ],
                "PhysicalWritesPerSec"=>[
                    "type"=>"number"
                ],
                "PhysicalReadsCache"=>[
                    "type"=>"number"
                ],
                "DbBlockGetsCache"=>[
                    "type"=>"number"
                ],
                "ConsistentGetsCache"=>[
                    "type"=>"number"
                ],
                "DbBlockChangeOverGets"=>[
                    "type"=>"number"
                ],
                "ConsistentChangeOverGets"=>[
                    "type"=>"number"
                ],
                "logSwitchesPerHour"=>[
                    "type"=>"number"
                ],

            ]
        ];
    }

    /**
     * @param $snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {
        return
        [[
            "PhysicalWritesPerSec" => $this->getPhysicalWritesPerSec($snapshot),
            "PhysicalWritesCache"=>$this->getPhysicalWritesCache($snapshot),
            "PhysicalReadsCache"=>$this->getPhysicalReadsCache($snapshot),
            "DbBlockGetsCache"=>$this->getDbBlockGetsCache($snapshot),
            "ConsistentGetsCache"=>$this->getConsistentGetsCache($snapshot),
            "DbBlockChangeOverGets"=>$this->getDbBlockChangeOverGets($snapshot),
            "ConsistentChangeOverGets"=>$this->getConsistentChangeOverGets($snapshot),
            "logSwitchesPerHour" => $this->getLogSwitches($snapshot),
        ]];

    }/***********************************************************************/
    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    private function getPhysicalWritesPerSec($snapshot){
        $stat = $snapshot->getStatByName('physical reads');
        return ($stat)?$stat->getPerSec():0;
    }
    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    private function getLogSwitches($snapshot){
        $stat = $snapshot->getStatByName('log switches (derived)');
        return ($stat)?$stat->getPerHour():0;
    }
    /**
     * @param $snapshot Snapshot
     * @return float|int
     */
    private function getPhysicalWritesCache($snapshot){
        $physicalWritesPerSec =($snapshot->getStatByName('physical writes') !== null)? $snapshot->getStatByName('physical writes')->getTotal():0;
        $physicalWritesDirectPerSec = ($snapshot->getStatByName('physical writes direct') !== null)?$snapshot->getStatByName('physical writes direct')->getTotal():0;
        if ($physicalWritesPerSec == 0)
            return null;
        return (($physicalWritesPerSec-$physicalWritesDirectPerSec) / $physicalWritesPerSec )*100;
    }
    /**
     * @param $snapshot Snapshot
     * @return float|int
     */
    private function getPhysicalReadsCache($snapshot){
        $physicalReadsPerSec = ($snapshot->getStatByName('physical reads'))?$snapshot->getStatByName('physical reads')->getTotal():0;
        $physicalReadsDirectPerSec = ($snapshot->getStatByName('physical reads direct'))?$snapshot->getStatByName('physical reads direct')->getTotal():0;
        if ($physicalReadsPerSec == 0)
            return null;
        return (($physicalReadsPerSec-$physicalReadsDirectPerSec) / $physicalReadsPerSec)*100 ;
    }

    /**
     * @param $snapshot Snapshot
     * @return float|int
     */
    private function getDbBlockGetsCache($snapshot){
        $dbBlockGetsPerSec = ($snapshot->getStatByName('db block gets'))?$snapshot->getStatByName('db block gets')->getTotal():0;
        $dbBlockGetsDirectPerSec = ($snapshot->getStatByName('db block gets direct'))?$snapshot->getStatByName('db block gets direct')->getTotal():0;
        if ($dbBlockGetsPerSec == 0)
            return null;
        return (($dbBlockGetsPerSec-$dbBlockGetsDirectPerSec) / $dbBlockGetsPerSec )*100;
    }

    /**
     * @param $snapshot Snapshot
     * @return float|int
     */
    private function getConsistentGetsCache($snapshot){
        $consistentGetsPerSec = ($snapshot->getStatByName('consistent gets'))?$snapshot->getStatByName('consistent gets')->getTotal():0;
        $consistentGetsDirectPerSec = ($snapshot->getStatByName('consistent gets direct'))?$snapshot->getStatByName('consistent gets direct')->getTotal():0;
        if ($consistentGetsPerSec == 0)
            return null;
        return (($consistentGetsPerSec-$consistentGetsDirectPerSec) / $consistentGetsPerSec )*100;
    }

    /**
     * @param $snapshot Snapshot
     * @return float|int
     */
    private function getDbBlockChangeOverGets($snapshot){
        $dbBlockChange = ($snapshot->getStatByName('db block changes'))?$snapshot->getStatByName('db block changes')->getTotal():0;
        $dbBlockGetsDirectPerSec = ($snapshot->getStatByName('db block gets direct'))?$snapshot->getStatByName('db block gets direct')->getTotal():0;
        if($dbBlockGetsDirectPerSec == 0)
            return null;
        return ($dbBlockChange / $dbBlockGetsDirectPerSec )*100;
    }

    /**
     * @param $snapshot Snapshot
     * @return float|int
     */
    private function getConsistentChangeOverGets($snapshot){
        $consistentChanges = ($snapshot->getStatByName('consistent changes'))?$snapshot->getStatByName('consistent changes')->getTotal():0;
        $consistentGetsPerSec = ($snapshot->getStatByName('consistent gets'))?$snapshot->getStatByName('consistent gets')->getTotal():0;
        if ($consistentGetsPerSec == 0)
            return null;
        return ($consistentChanges / $consistentGetsPerSec)*100 ;
    }/**/
}