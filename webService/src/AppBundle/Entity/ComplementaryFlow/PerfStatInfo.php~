<?php
/**
 * Created by PhpStorm.
 * User: simonvivier
 * Date: 17/01/18
 * Time: 10:40
 */

namespace AppBundle\Entity\ComplementaryFlow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="perfStatInfo")
 */
class PerfStatInfo
{

    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="datetime",nullable=true)
     * la date d'inscription du client
     */
    private $lastSnapTime;
    /**
     * @ORM\Column(type="boolean")
     *
     */
    private $snapshotJobStatus;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    private $purgeJobStatus;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    private $sequenceCacheSize;
    /**
     * @ORM\Column(type="integer")
     *
     */
    private $idleEventCount;
    /**
     * @return mixed
     */
    public function getLastSnapTime()
    {
        return $this->lastSnapTime;
    }

    /**
     * @param mixed $lastSnapTime
     * @return PerfStatInfo
     */
    public function setLastSnapTime($lastSnapTime)
    {
        $this->lastSnapTime = $lastSnapTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSnapshotJobStatus()
    {
        return $this->snapshotJobStatus;
    }

    /**
     * @param mixed $snapshotJobStatus
     * @return PerfStatInfo
     */
    public function setSnapshotJobStatus($snapshotJobStatus)
    {
        $this->snapshotJobStatus = $snapshotJobStatus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPurgeJobStatus()
    {
        return $this->purgeJobStatus;
    }

    /**
     * @param mixed $purgeJobStatus
     * @return PerfStatInfo
     */
    public function setPurgeJobStatus($purgeJobStatus)
    {
        $this->purgeJobStatus = $purgeJobStatus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSequenceCacheSize()
    {
        return $this->sequenceCacheSize;
    }

    /**
     * @param mixed $sequenceCacheSize
     * @return PerfStatInfo
     */
    public function setSequenceCacheSize($sequenceCacheSize)
    {
        $this->sequenceCacheSize = $sequenceCacheSize;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdleEventCount()
    {
        return $this->idleEventCount;
    }

    /**
     * @param mixed $idleEventCount
     * @return PerfStatInfo
     */
    public function setIdleEventCount($idleEventCount)
    {
        $this->idleEventCount = $idleEventCount;
        return $this;
    }



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
