<?php
// src/AppBundle/Entity/Snapshots/Ressources.php
namespace AppBundle\Entity\Snapshots;

use AppBundle\Entity\ComplementaryFlow\Lock;
use AppBundle\Entity\ComplementaryFlow\LongSession;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\ORM\Mapping\Index;
use AppBundle\Entity\ComplementaryFlow\PerformanceBench;
/**
 * @ORM\Entity
 * @ORM\Table(name="snapshot",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="unique",columns={"start_date", "end_date","instance_id"})},
 *     indexes={@Index(name="search_idx_1", columns={"start_date"})})
 *     indexes={@Index(name="search_idx_2", columns={"end_date"})})
 *     indexes={@Index(name="search_idx_3", columns={"instance_id"})})

 * Contient l'image d'une instance a un instant T
 */
class Snapshot
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;
    /** N° de version du SGBD
     * @ORM\Column(type="string",length=30)
     */
    private $version;
    /**
     * @ORM\Column(type="string")
     */
    private $fileName;
    /**
     * @ORM\Column(name = "start_date",type="datetime")
     * la date de modification du paramètre
     */
    private $startDate;
    /** Les locks de l'instance
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\ComplementaryFlow\Lock",mappedBy="snapshot",cascade={"persist","remove"})
     * @ORM\OrderBy({"timestamp" = "ASC"})
     */
    private $locks;
    /** Le statut de perfStat sur cette instance
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\ComplementaryFlow\LongSession",mappedBy="snapshot",cascade={"persist","remove"})
     * @ORM\OrderBy({"timestamp" = "ASC"})
     */
    private $longSessions;
    /**
     * @ORM\Column(name = "end_date",type="datetime")
     * la date de modification du paramètre
     */
    private $endDate;

    /**
     * les IO sur les tablespaces
     * @ORM\OneToMany(targetEntity="Tablespace", mappedBy="snapshot",cascade={"persist","remove"})
     */
    private $tablespaces;
    /**
     * @ORM\OneToMany(targetEntity="Note", mappedBy="snapshot",cascade={"persist","remove"})
     */
    private $notes;

    /**
     * Les events les plus important pour l'instance de la BDD
     * @ORM\OneToMany(targetEntity="Event", mappedBy="snapshot",cascade={"persist","remove"})
     */
    private $events;
    /**
     * Les conseils d'oracle sur les zones mémoire
     * @ORM\OneToMany(targetEntity="AdvisorySnapshot", mappedBy="snapshot",cascade={"persist","remove"})
     */
    private $advisory;
    /** L'état global de l'instance
     * @ORM\OneToMany(targetEntity="Stat",mappedBy="snapshot",cascade={"persist","remove"})
     */
    private $stats;
    /** L'état global de l'instance
     * @ORM\OneToOne(targetEntity="InstanceState",cascade={"persist","remove"})
     */
    private $instanceState;
    /** L'état global de l'instance
     * @ORM\OneToOne(targetEntity="LoadProfile",cascade={"persist","remove"})
     */
    private $loadProfile;
    /** Des indicateurs de performances
     * @ORM\OneToOne(targetEntity="EfficiencyIndicator",cascade={"persist","remove"})
     */
    private $efficiencyIndicator;

    /**
     * @ORM\OneToMany(targetEntity="Parameters",mappedBy="snapshot",cascade={"persist","remove"})
     */
    private $parameters;

    /** Les infos génerale sur des requetes SQL, et les requètes les plus importantes
     * @ORM\OneToOne(targetEntity="SqlInfo",cascade={"persist","remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $sqlInfo;

    /** L'état de l'OS
     * @ORM\OneToOne(targetEntity="OSState",cascade={"persist","remove"})
     */
    private $osState;

    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Base\Instance", inversedBy="snapshots",cascade={"persist"})
     */
    private $instance;
    /**
    * @ORM\OneToOne(targetEntity="\AppBundle\Entity\ComplementaryFlow\PerformanceBench",cascade={"persist","remove"})
    */
    private $performancebench;

    /**
     * get Event By Name
     * @param $eventName
     * @return Event
     */
    public function getEventByName($eventName)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->contains('name', $eventName));
        return $this->events->matching($criteria)[0];
    }

    /**
     * get Note By Name
     * @param $eventName
     * @return Event
     */
    public function getNoteByName($noteName)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->contains('name', $noteName));
        return $this->notes->matching($criteria)[0];
    }

    /**
     * get Stat By Name
     * @param $statName
     * @return Stat
     */
    public function getStatByName($statName)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->contains('name', $statName));
        return $this->stats->matching($criteria)[0];
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tablespaces = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->stats = new \Doctrine\Common\Collections\ArrayCollection();
        $this->locks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->longSessions = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set version
     *
     * @param string $version
     *
     * @return Snapshot
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocks()
    {
        return $this->locks;
    }

    /**
     * @param mixed $locks
     * @return Snapshot
     */
    public function setLocks($locks)
    {
        $this->locks = $locks;
        return $this;
    }
    /**
     * @param $locks Lock
     * @return Snapshot
     */
    public function addLocks(\AppBundle\Entity\ComplementaryFlow\Lock $lock)
    {
        $this->locks[] = $lock;
        $lock->setSnapshot($this);
        return $this;
    }

    /**
     * Remove tablespace
     *
     * @param \AppBundle\Entity\Snapshots\Tablespace $tablespace
     */
    public function removeLock(\AppBundle\Entity\ComplementaryFlow\Lock $lock)
    {
        $this->locks->removeElement($lock);
    }


    /**
     * @return mixed
     */
    public function getLongSessions()
    {
        return $this->longSessions;
    }

    /**
     * @param mixed $longSessions
     * @return Snapshot
     */
    public function setLongSessions($longSessions)
    {
        $this->longSessions = $longSessions;
        return $this;
    }
    /**
     * @param $longSession LongSession
     * @return Snapshot
     */
    public function addLongSession(\AppBundle\Entity\ComplementaryFlow\LongSession $longSession)
    {
        $this->longSessions[] = $longSession;
        $longSession->setSnapshot($this);
        return $this;
    }

    /**
     * Remove long session
     *
     * @param \AppBundle\Entity\Snapshots\Tablespace $tablespace
     */
    public function removeLongSession(\AppBundle\Entity\ComplementaryFlow\LongSession $longSession)
    {
        $this->longSessions->removeElement($longSession);
    }
    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Snapshot
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Snapshot
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Add tablespace
     *
     * @param \AppBundle\Entity\Snapshots\Tablespace $tablespace
     *
     * @return Snapshot
     */
    public function addTablespace(\AppBundle\Entity\Snapshots\Tablespace $tablespace)
    {
        $this->tablespaces[] = $tablespace;
        $tablespace->setSnapshot($this);
        return $this;
    }

    /**
     * Remove tablespace
     *
     * @param \AppBundle\Entity\Snapshots\Tablespace $tablespace
     */
    public function removeTablespace(\AppBundle\Entity\Snapshots\Tablespace $tablespace)
    {
        $this->tablespaces->removeElement($tablespace);
    }

    /**
     * Get tablespaces
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTablespaces()
    {
        return $this->tablespaces;
    }

    /**
     * Add note
     *
     * @param \AppBundle\Entity\Snapshots\Note $note
     *
     * @return Snapshot
     */
    public function addNote(\AppBundle\Entity\Snapshots\Note $note)
    {

        $this->notes[] = $note;
        $note->setSnapshot($this);
        return $this;
    }

    /**
     * Remove note
     *
     * @param \AppBundle\Entity\Snapshots\Note $note
     */
    public function removeNote(\AppBundle\Entity\Snapshots\Note $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Add event
     *
     * @param \AppBundle\Entity\Snapshots\Event $event
     *
     * @return Snapshot
     */
    public function addEvent(\AppBundle\Entity\Snapshots\Event $event)
    {
        $this->events[] = $event;
        $event->setSnapshot($this);
        return $this;
    }

    /**
     * Remove event
     *
     * @param \AppBundle\Entity\Snapshots\Event $event
     */
    public function removeEvent(\AppBundle\Entity\Snapshots\Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set instanceState
     *
     * @param \AppBundle\Entity\Snapshots\InstanceState $instanceState
     *
     * @return Snapshot
     */
    public function setInstanceState(\AppBundle\Entity\Snapshots\InstanceState $instanceState = null)
    {
        $this->instanceState = $instanceState;

        return $this;
    }

    /**
     * Get instanceState
     *
     * @return \AppBundle\Entity\Snapshots\InstanceState
     */
    public function getInstanceState()
    {
        return $this->instanceState;
    }

    /**
     * Set loadProfile
     *
     * @param \AppBundle\Entity\Snapshots\LoadProfile $loadProfile
     *
     * @return Snapshot
     */
    public function setLoadProfile(\AppBundle\Entity\Snapshots\LoadProfile $loadProfile = null)
    {
        $this->loadProfile = $loadProfile;

        return $this;
    }

    /**
     * Get loadProfile
     *
     * @return \AppBundle\Entity\Snapshots\LoadProfile
     */
    public function getLoadProfile()
    {
        return $this->loadProfile;
    }

    /**
     * Set efficiencyIndicator
     *
     * @param \AppBundle\Entity\Snapshots\EfficiencyIndicator $efficiencyIndicator
     *
     * @return Snapshot
     */
    public function setEfficiencyIndicator(\AppBundle\Entity\Snapshots\EfficiencyIndicator $efficiencyIndicator = null)
    {
        $this->efficiencyIndicator = $efficiencyIndicator;

        return $this;
    }

    /**
     * Get efficiencyIndicator
     *
     * @return \AppBundle\Entity\Snapshots\EfficiencyIndicator
     */
    public function getEfficiencyIndicator()
    {
        return $this->efficiencyIndicator;
    }

    /**
     * Set sqlInfo
     *
     * @param \AppBundle\Entity\Snapshots\SqlInfo $sqlInfo
     *
     * @return Snapshot
     */
    public function setSqlInfo(\AppBundle\Entity\Snapshots\SqlInfo $sqlInfo = null)
    {
        $this->sqlInfo = $sqlInfo;

        return $this;
    }

    /**
     * Get sqlInfo
     *
     * @return \AppBundle\Entity\Snapshots\SqlInfo
     */
    public function getSqlInfo()
    {
        return $this->sqlInfo;
    }

    /**
     * Set osState
     *
     * @param \AppBundle\Entity\Snapshots\OSState $osState
     *
     * @return Snapshot
     */
    public function setOsState(\AppBundle\Entity\Snapshots\OSState $osState = null)
    {
        $this->osState = $osState;

        return $this;
    }

    /**
     * Get osState
     *
     * @return \AppBundle\Entity\Snapshots\OSState
     */
    public function getOsState()
    {
        return $this->osState;
    }

    /**
     * Set instance
     *
     * @param \AppBundle\Entity\Base\Instance $instance
     *
     * @return Snapshot
     */
    public function setInstance(\AppBundle\Entity\Base\Instance $instance = null)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get instance
     *
     * @return \AppBundle\Entity\Base\Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Add stats
     *
     * @param \AppBundle\Entity\Snapshots\Stat $stat
     *
     * @return Snapshot
     */
    public function addStat(\AppBundle\Entity\Snapshots\Stat $stat)
    {
        $this->stats[] = $stat;
        $stat->setSnapshot($this);
        return $this;
    }

    /**
     * Remove stats
     *
     * @param \AppBundle\Entity\Snapshots\Stat $stat
     */
    public function removeStat(\AppBundle\Entity\Snapshots\Stat $stat)
    {
        $this->stats->removeElement($stat);
    }

    /**
     * Get stat
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * Set parameters
     *
     * @param \AppBundle\Entity\Snapshots\Parameters $parameters
     *
     * @return Snapshot
     */
    public function setParameters(\AppBundle\Entity\Snapshots\Parameters $parameters = null)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return \AppBundle\Entity\Snapshots\Parameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    /**
     * Add note
     *
     * @param \AppBundle\Entity\Snapshots\Parameters $parameters
     *
     * @return Snapshot
     */
    public function addParameters(\AppBundle\Entity\Snapshots\Parameters $parameters)
    {

        $this->parameters[] = $parameters;
        $parameters->setSnapshot($this);
        return $this;
    }



    /**
     * Add parameter
     *
     * @param \AppBundle\Entity\Snapshots\Parameters $parameter
     *
     * @return Snapshot
     */
    public function addParameter(\AppBundle\Entity\Snapshots\Parameters $parameter)
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * Remove parameter
     *
     * @param \AppBundle\Entity\Snapshots\Parameters $parameter
     */
    public function removeParameter(\AppBundle\Entity\Snapshots\Parameters $parameter)
    {
        $this->parameters->removeElement($parameter);
    }


    /**
     * Add advisory
     *
     * @param \AppBundle\Entity\Snapshots\AdvisorySnapshot $advisory
     *
     * @return Snapshot
     */
    public function addAdvisory(\AppBundle\Entity\Snapshots\AdvisorySnapshot $advisory)
    {
        $this->advisory[] = $advisory;
        $advisory->setSnapshot($this);
        return $this;
    }

    /**
     * Remove advisory
     *
     * @param \AppBundle\Entity\Snapshots\AdvisorySnapshot $advisory
     */
    public function removeAdvisory(\AppBundle\Entity\Snapshots\AdvisorySnapshot $advisory)
    {
        $this->advisory->removeElement($advisory);
    }

    /**
     * Get advisory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdvisory()
    {
        return $this->advisory;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return Snapshot
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Add lock
     *
     * @param \AppBundle\Entity\ComplementaryFlow\Lock $lock
     *
     * @return Snapshot
     */
    public function addLock(\AppBundle\Entity\ComplementaryFlow\Lock $lock)
    {
        $this->locks[] = $lock;

        return $this;
    }

    /**
     * Set performancebench
     *
     * @param \AppBundle\Entity\ComplementaryFlow\PerformanceBench $performancebench
     *
     * @return Snapshot
     */
    public function setPerformancebench(\AppBundle\Entity\ComplementaryFlow\PerformanceBench $performancebench = null)
    {
        $this->performancebench = $performancebench;

        return $this;
    }

    /**
     * Get performancebench
     *
     * @return \AppBundle\Entity\ComplementaryFlow\PerformanceBench
     */
    public function getPerformancebench()
    {
        return $this->performancebench;
    }

    public function displaySnapshotInfos(){
        print(
            "Version: ".$this->version."\n".
            "StartDate: ".$this->startDate->format('Y-m-d H:i:s')."\n".
            "EndDate: ".$this->endDate->format('Y-m-d H:i:s')."\n".
            "Instance: ".$this->instance->getId()
        );
    }

    public function findSnapshotByConstraintKey(){
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $instanceId = $this->getInstance()->getId();
        if($startDate != null && $endDate != null && $instanceId != null){
            return true;
        }
        else{
            return false;
        }
    }
}
