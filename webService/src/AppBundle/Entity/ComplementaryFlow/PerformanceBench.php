<?php
// src/AppBundle/Entity/ComplementaryFlow/PerformanceBench.php
namespace AppBundle\Entity\ComplementaryFlow;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="performanceBench")
 */
class PerformanceBench
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(name="insert_perf",type="float")
     */
    private $insert = 0;
    /**
     * @ORM\Column(name="cpu_perf",type="float")
     */
    private $cpu = 0;
    /**
     * @ORM\Column(name="update_perf",type="float")
     */
    private $update = 0;
    /**
     * @ORM\Column(name="sqlplus_perf",type="float")
     */
    private $sqlPlus = 0;
    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $timestamp;
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
     * Set insert
     *
     * @param float $insert
     *
     * @return PerformanceBench
     */
    public function setInsert($insert)
    {
        $this->insert = $insert;

        return $this;
    }

    /**
     * Get insert
     *
     * @return float
     */
    public function getInsert()
    {
        return $this->insert;
    }

    /**
     * Set cpu
     *
     * @param float $cpu
     *
     * @return PerformanceBench
     */
    public function setCpu($cpu)
    {
        $this->cpu = $cpu;

        return $this;
    }

    /**
     * Get cpu
     *
     * @return float
     */
    public function getCpu()
    {
        return $this->cpu;
    }

    /**
     * Set update
     *
     * @param float $update
     *
     * @return PerformanceBench
     */
    public function setUpdate($update)
    {
        $this->update = $update;

        return $this;
    }

    /**
     * Get update
     *
     * @return float
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * Set sqlPlus
     *
     * @param float $sqlPlus
     *
     * @return PerformanceBench
     */
    public function setSqlPlus($sqlPlus)
    {
        $this->sqlPlus = $sqlPlus;

        return $this;
    }

    /**
     * Get sqlPlus
     *
     * @return float
     */
    public function getSqlPlus()
    {
        return $this->sqlPlus;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return PerformanceBench
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

}
