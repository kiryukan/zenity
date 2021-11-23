<?php
// src/AppBundle/Entity/Snapshots/Ressources/Event.php
namespace AppBundle\Entity\Snapshots;
use AppBundle\Entity\IJsonUpdatable;
use AppBundle\Entity\INamable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 * Un event important en ressources sur un @link Ressources
 */
class Event implements INamable
{

    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /** nécessaire pour un OneToMany dans @link Ressources
     * @ORM\ManyToOne(targetEntity="Snapshot", inversedBy="events",cascade={"persist","remove"}))
     */
    private $snapshot;
    /**
     * @ORM\Column(type="string",length=30,nullable =true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Metadata\EventMetadata")
     */
    private $eventMetadata;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $waits;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $time;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $avgWait;


    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $dbTime;

    /** 
     * @ORM\OneToOne(targetEntity="Histo",cascade={"persist","remove"})
     */
    private $histogram;
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
     * Set name
     *
     * @param string $name
     *
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set waits
     *
     * @param integer $wait
     *
     * @return Event
     */
    public function setWaits($waits)
    {
        $this->waits = $waits;

        return $this;
    }

    /**
     * Get waits
     *
     * @return integer
     */
    public function getWaits()
    {
        return $this->waits;
    }

    /**
     * Set time
     *
     * @param integer $time
     *
     * @return Event
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set avgWait
     *
     * @param integer $avgWait
     *
     * @return Event
     */
    public function setAvgWait($avgWait)
    {
        $this->avgWait = $avgWait;

        return $this;
    }

    /**
     * Get avgWait
     *
     * @return integer
     */
    public function getAvgWait()
    {
        return $this->avgWait;
    }


    /**
     * Set snapshot
     *
     * @param \AppBundle\Entity\Snapshots\Snapshot $snapshot
     *
     * @return Event
     */
    public function setSnapshot(\AppBundle\Entity\Snapshots\Snapshot $snapshot = null)
    {
        $this->snapshot = $snapshot;

        return $this;
    }

    /**
     * Get snapshot
     *
     * @return \AppBundle\Entity\Snapshots\Snapshot
     */
    public function getSnapshot()
    {
        return $this->snapshot;
    }

    /**
     * Set histogram
     *
     * @param \AppBundle\Entity\Snapshots\Histo $histogram
     *
     * @return Event
     */
    public function setHisto(\AppBundle\Entity\Snapshots\Histo $histogram = null)
    {

        $this->histogram = $histogram;

        return $this;
    }

    /**
     * Get histogram
     *
     * @return \AppBundle\Entity\Snapshots\Histo
     */
    public function getHisto()
    {
        return $this->histogram;
    }

    /**
     * Set dbTime
     *
     * @param float $dbTime
     *
     * @return Event
     */
    public function setDbTime($dbTime)
    {
        $this->dbTime = $dbTime;

        return $this;
    }

    /**
     * Get dbTime
     *
     * @return float
     */
    public function getDbTime()
    {
        return $this->dbTime;
    }

    /**
     * Set histogram
     *
     * @param \AppBundle\Entity\Snapshots\Histo $histogram
     *
     * @return Event
     */
    public function setHistogram(\AppBundle\Entity\Snapshots\Histo $histogram = null)
    {
        $this->histogram = $histogram;

        return $this;
    }

    /**
     * Get histogram
     *
     * @return \AppBundle\Entity\Snapshots\Histo
     */
    public function getHistogram()
    {
        return $this->histogram;
    }

    /**
     * Set eventMetadata
     *
     * @param \AppBundle\Entity\Metadata\EventMetadata $eventMetadata
     *
     * @return Event
     */
    public function setEventMetadata(\AppBundle\Entity\Metadata\EventMetadata $eventMetadata = null)
    {
        $this->eventMetadata = $eventMetadata;

        return $this;
    }

    /**
     * Get eventMetadata
     *
     * @return \AppBundle\Entity\Metadata\EventMetadata
     */
    public function getEventMetadata()
    {
        return $this->eventMetadata;
    }
}
