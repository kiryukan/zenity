<?php
/**
 * Created by PhpStorm.
 * User: simonvivier
 * Date: 18/01/18
 * Time: 10:23
 */

namespace AppBundle\Entity\ComplementaryFlow;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="instance_lock",
 * uniqueConstraints={@ORM\UniqueConstraint(name="unique",columns={"timestamp","lock_id"})},

 * )
 */

class Lock
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /** nécessaire pour un OneToMany dans @link Ressources
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Snapshots\Snapshot", inversedBy="locks",cascade={"persist","remove"}))
     */
    private $snapshot;
    /**
     * @ORM\Column(name = "timestamp",type="datetime",nullable =true)
     */
    private $timestamp;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $username;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $module;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $machine;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $terminal;
    /**
     * @ORM\Column(type="string",length=120,nullable =true)
     */
    private $program;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $sql_id;
    /**
     * @ORM\Column(type="string",length=30,nullable =true)
     */
    private $lock_id;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $sql_child_number;
    /**
     * @ORM\Column(type="integer",nullable =true)
     */
    private $duree;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $wait_class;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $event;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $object_owner;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $object_name;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $object_type;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $osuser;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $status;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $mode_held;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSnapshot()
    {
        return $this->snapshot;
    }

    /**
     * @param mixed $snapshot
     * @return Lock
     */
    public function setSnapshot($snapshot)
    {
        $this->snapshot = $snapshot;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLockId()
    {
        return $this->lock_id;
    }

    /**
     * @param mixed $lock_id
     * @return Lock
     */
    public function setLockId($lock_id)
    {
        $this->lock_id = $lock_id;
        return $this;
    }



    /**
     * @param mixed $instance
     * @return Lock
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     * @return Lock
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return Lock
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param mixed $module
     * @return Lock
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMachine()
    {
        return $this->machine;
    }

    /**
     * @param mixed $machine
     * @return Lock
     */
    public function setMachine($machine)
    {
        $this->machine = $machine;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTerminal()
    {
        return $this->terminal;
    }

    /**
     * @param mixed $terminal
     * @return Lock
     */
    public function setTerminal($terminal)
    {
        $this->terminal = $terminal;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @param mixed $program
     * @return Lock
     */
    public function setProgram($program)
    {
        $this->program = $program;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSqlId()
    {
        return $this->sql_id;
    }

    /**
     * @param mixed $sql_id
     * @return Lock
     */
    public function setSqlId($sql_id)
    {
        $this->sql_id = $sql_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSqlChildNumber()
    {
        return $this->sql_child_number;
    }

    /**
     * @param mixed $sql_child_number
     * @return Lock
     */
    public function setSqlChildNumber($sql_child_number)
    {
        $this->sql_child_number = $sql_child_number;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * @param mixed $duree
     * @return Lock
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWaitClass()
    {
        return $this->wait_class;
    }

    /**
     * @param mixed $wait_class
     * @return Lock
     */
    public function setWaitClass($wait_class)
    {
        $this->wait_class = $wait_class;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     * @return Lock
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObjectOwner()
    {
        return $this->object_owner;
    }

    /**
     * @param mixed $object_owner
     * @return Lock
     */
    public function setObjectOwner($object_owner)
    {
        $this->object_owner = $object_owner;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObjectName()
    {
        return $this->object_name;
    }

    /**
     * @param mixed $object_name
     * @return Lock
     */
    public function setObjectName($object_name)
    {
        $this->object_name = $object_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObjectType()
    {
        return $this->object_type;
    }

    /**
     * @param mixed $object_type
     * @return Lock
     */
    public function setObjectType($object_type)
    {
        $this->object_type = $object_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOsuser()
    {
        return $this->osuser;
    }

    /**
     * @param mixed $osuser
     * @return Lock
     */
    public function setOsuser($osuser)
    {
        $this->osuser = $osuser;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return Lock
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModeHeld()
    {
        return $this->mode_held;
    }

    /**
     * @param mixed $mode_held
     * @return Lock
     */
    public function setModeHeld($mode_held)
    {
        $this->mode_held = $mode_held;
        return $this;
    }

}
