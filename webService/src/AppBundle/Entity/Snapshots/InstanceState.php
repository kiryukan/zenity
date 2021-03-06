<?php
// src/AppBundle/Entity/Snapshots/InstanceState.php
namespace AppBundle\Entity\Snapshots;
use AppBundle\Entity\IJsonUpdatable;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="instanceState")
 * Des infos génerale sur l'instance
 */
class InstanceState
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $redoLogSpaceRequests;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $redoLogSpaceWaitTime;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $sqlAreaEvicted;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $sqlAreaPurged;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $userCall;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $userCommit;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $nbSession;

    /**
     * @ORM\Column(type="bigint",nullable=true)
     */
    private $userIOWaitTime;
    /**
     * transform a valid json into $this
     * @param $json String
     */

    /*
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
     * Set redoLogSpaceRequests
     *
     * @param integer $redoLogSpaceRequests
     *
     * @return InstanceState
     */
    public function setRedoLogSpaceRequests($redoLogSpaceRequests)
    {
        $this->redoLogSpaceRequests = $redoLogSpaceRequests;

        return $this;
    }

    /**
     * Get redoLogSpaceRequests
     *
     * @return integer
     */
    public function getRedoLogSpaceRequests()
    {
        return $this->redoLogSpaceRequests;
    }

    /**
     * Set redoLogSpaceWaitTime
     *
     * @param integer $redoLogSpaceWaitTime
     *
     * @return InstanceState
     */
    public function setRedoLogSpaceWaitTime($redoLogSpaceWaitTime)
    {
        $this->redoLogSpaceWaitTime = $redoLogSpaceWaitTime;

        return $this;
    }

    /**
     * Get redoLogSpaceWaitTime
     *
     * @return integer
     */
    public function getRedoLogSpaceWaitTime()
    {
        return $this->redoLogSpaceWaitTime;
    }

    /**
     * Set sqlAreaEvicted
     *
     * @param integer $sqlAreaEvicted
     *
     * @return InstanceState
     */
    public function setSqlAreaEvicted($sqlAreaEvicted)
    {
        $this->sqlAreaEvicted = $sqlAreaEvicted;

        return $this;
    }

    /**
     * Get sqlAreaEvicted
     *
     * @return integer
     */
    public function getSqlAreaEvicted()
    {
        return $this->sqlAreaEvicted;
    }

    /**
     * Set sqlAreaPurged
     *
     * @param integer $sqlAreaPurged
     *
     * @return InstanceState
     */
    public function setSqlAreaPurged($sqlAreaPurged)
    {
        $this->sqlAreaPurged = $sqlAreaPurged;

        return $this;
    }

    /**
     * Get sqlAreaPurged
     *
     * @return integer
     */
    public function getSqlAreaPurged()
    {
        return $this->sqlAreaPurged;
    }

    /**
     * Set userCall
     *
     * @param integer $userCall
     *
     * @return InstanceState
     */
    public function setUserCall($userCall)
    {
        $this->userCall = $userCall;

        return $this;
    }

    /**
     * Get userCall
     *
     * @return integer
     */
    public function getUserCall()
    {
        return $this->userCall;
    }

    /**
     * Set userCommit
     *
     * @param integer $userCommit
     *
     * @return InstanceState
     */
    public function setUserCommit($userCommit)
    {
        $this->userCommit = $userCommit;

        return $this;
    }

    /**
     * Get userCommit
     *
     * @return integer
     */
    public function getUserCommit()
    {
        return $this->userCommit;
    }

    /**
     * Set userIOWaitTime
     *
     * @param integer $userIOWaitTime
     *
     * @return InstanceState
     */
    public function setUserIOWaitTime($userIOWaitTime)
    {
        $this->userIOWaitTime = $userIOWaitTime;

        return $this;
    }

    /**
     * Get userIOWaitTime
     *
     * @return integer
     */
    public function getUserIOWaitTime()
    {
        return $this->userIOWaitTime;
    }

    /**
     * Set nbSession
     *
     * @param integer $nbSession
     *
     * @return InstanceState
     */
    public function setNbSession($nbSession)
    {
        $this->nbSession = $nbSession;

        return $this;
    }

    /**
     * Get nbSession
     *
     * @return integer
     */
    public function getNbSession()
    {
        return $this->nbSession;
    }
}
