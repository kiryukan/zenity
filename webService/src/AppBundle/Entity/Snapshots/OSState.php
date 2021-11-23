<?php
// src/AppBundle/Entity/Snapshots/OSState.php
namespace AppBundle\Entity\Snapshots;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="OSState")
 * l'état de l'OS (prend en compte toutes les applications du serveur et pas juste la BDD)
 */
class OSState
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $memoryUsage;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $userTime;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $idleTime;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $busyTime;
    /**
     * @ORM\Column(name="_load",type="float",nullable=true)
     */
    private $load;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $sysTime;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set memoryUsage
     *
     * @param integer $memoryUsage
     *
     * @return OSState
     */
    public function setMemoryUsage($memoryUsage)
    {
        $this->memoryUsage = $memoryUsage;
        return $this;
    }

    /**
     * Get memoryUsage
     *
     * @return integer
     */
    public function getMemoryUsage()
    {
        return $this->memoryUsage;
    }

    /**
     * Set userTime
     *
     * @param integer $userTime
     *
     * @return OSState
     */
    public function setUserTime($userTime)
    {
        $this->userTime = $userTime;

        return $this;
    }

    /**
     * Get userTime
     *
     * @return integer
     */
    public function getUserTime()
    {
        return $this->userTime;
    }

    /**
     * Set idleTime
     *
     * @param integer $idleTime
     *
     * @return OSState
     */
    public function setIdleTime($idleTime)
    {
        $this->idleTime = $idleTime;

        return $this;
    }

    /**
     * Get idleTime
     *
     * @return integer
     */
    public function getIdleTime()
    {
        return $this->idleTime;
    }

    /**
     * Set busyTime
     *
     * @param integer $busyTime
     *
     * @return OSState
     */
    public function setBusyTime($busyTime)
    {
        $this->busyTime = $busyTime;

        return $this;
    }

    /**
     * Get busyTime
     *
     * @return integer
     */
    public function getBusyTime()
    {
        return $this->busyTime;
    }

    /**
     * Set load
     *
     * @param float $load
     *
     * @return OSState
     */
    public function setLoad($load)
    {
        $this->load = $load;

        return $this;
    }

    /**
     * Get load
     *
     * @return float
     */
    public function getLoad()
    {
        return $this->load;
    }

    /**
     * Set sysTime
     *
     * @param float $sysTime
     *
     * @return OSState
     */
    public function setSysTime($sysTime)
    {
        $this->sysTime = $sysTime;

        return $this;
    }

    /**
     * Get sysTime
     *
     * @return float
     */
    public function getSysTime()
    {
        return $this->sysTime;
    }
}
