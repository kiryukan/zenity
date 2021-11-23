<?php


namespace AppBundle\Entity\Snapshots;
use AppBundle\Entity\IJsonUpdatable;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="loadProfile")
 * l'état de l'OS (prend en compte toutes les applications du serveur et pas juste la BDD)
 */
class LoadProfile
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
    private $blockChange;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $execute;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $transactions;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $userCalls;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $parses;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $physicalRead;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $rollbacks;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $physicalWrite;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $redoSize;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $hardParses;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $logons;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $dbTime;

    /**
     * Set blockChange
     *
     * @param float $blockChange
     *
     * @return LoadProfile
     */
    public function setBlockChange($blockChange)
    {
        $this->blockChange = $blockChange;

        return $this;
    }

    /**
     * Get blockChange
     *
     * @return float
     */
    public function getBlockChange()
    {
        return $this->blockChange;
    }

    /**
     * Set execute
     *
     * @param float $execute
     *
     * @return LoadProfile
     */
    public function setExecute($execute)
    {
        $this->execute = $execute;

        return $this;
    }

    /**
     * Get execute
     *
     * @return float
     */
    public function getExecute()
    {
        return $this->execute;
    }

    /**
     * Set transaction
     *
     * @param float $transaction
     *
     * @return LoadProfile
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return float
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set userCalls
     *
     * @param float $userCalls
     *
     * @return LoadProfile
     */
    public function setUserCalls($userCalls)
    {
        $this->userCalls = $userCalls;

        return $this;
    }

    /**
     * Get userCalls
     *
     * @return float
     */
    public function getUserCalls()
    {
        return $this->userCalls;
    }

    /**
     * Set parses
     *
     * @param float $parses
     *
     * @return LoadProfile
     */
    public function setParses($parses)
    {
        $this->parses = $parses;

        return $this;
    }

    /**
     * Get parses
     *
     * @return float
     */
    public function getParses()
    {
        return $this->parses;
    }

    /**
     * Set physicalRead
     *
     * @param float $physicalRead
     *
     * @return LoadProfile
     */
    public function setPhysicalRead($physicalRead)
    {
        $this->physicalRead = $physicalRead;

        return $this;
    }

    /**
     * Get physicalRead
     *
     * @return float
     */
    public function getPhysicalRead()
    {
        return $this->physicalRead;
    }

    /**
     * Set rollbacks
     *
     * @param float $rollbacks
     *
     * @return LoadProfile
     */
    public function setRollbacks($rollbacks)
    {
        $this->rollbacks = $rollbacks;

        return $this;
    }

    /**
     * Get rollbacks
     *
     * @return float
     */
    public function getRollbacks()
    {
        return $this->rollbacks;
    }

    /**
     * Set physicalWrite
     *
     * @param float $physicalWrite
     *
     * @return LoadProfile
     */
    public function setPhysicalWrite($physicalWrite)
    {
        $this->physicalWrite = $physicalWrite;

        return $this;
    }

    /**
     * Get physicalWrite
     *
     * @return float
     */
    public function getPhysicalWrite()
    {
        return $this->physicalWrite;
    }

    /**
     * Set redoSize
     *
     * @param float $redoSize
     *
     * @return LoadProfile
     */
    public function setRedoSize($redoSize)
    {
        $this->redoSize = $redoSize;

        return $this;
    }

    /**
     * Get redoSize
     *
     * @return float
     */
    public function getRedoSize()
    {
        return $this->redoSize;
    }

    /**
     * Set hardParses
     *
     * @param float $hardParses
     *
     * @return LoadProfile
     */
    public function setHardParses($hardParses)
    {
        $this->hardParses = $hardParses;

        return $this;
    }

    /**
     * Get hardParses
     *
     * @return float
     */
    public function getHardParses()
    {
        return $this->hardParses;
    }

    /**
     * Set logons
     *
     * @param float $logons
     *
     * @return LoadProfile
     */
    public function setLogons($logons)
    {
        $this->logons = $logons;

        return $this;
    }

    /**
     * Get logons
     *
     * @return float
     */
    public function getLogons()
    {
        return $this->logons;
    }

    /**
     * Set dbTime
     *
     * @param float $dbTime
     *
     * @return LoadProfile
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set transactions
     *
     * @param float $transactions
     *
     * @return LoadProfile
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transactions;

        return $this;
    }

    /**
     * Get transactions
     *
     * @return float
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
