<?php
// src/AppBundle/Entity/Snapshots/Request.php
namespace AppBundle\Entity\Snapshots;
use AppBundle\Entity\INamable;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="request")
 * Une requete importante sur un snapshot
 */
class Request implements INamable
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /** nécessaire pour un OneToMany dans @link SqlInfo
     * @ORM\ManyToOne(targetEntity="SqlInfo", inversedBy="requests",cascade={"persist","remove"})
     */
    private $sqlInfo;

    /**
     * @ORM\Column(type="string",length=30,nullable=true)
     */
    private $sqlId;

    /**
     * @ORM\Column(type="string",length=50,nullable=true)
     */
    private $hash;
    /**
     * @ORM\Column(type="string",length=100,nullable=true)
     */
    private $module;
    /**
     * @ORM\Column(type="string",length=4000,nullable=true)
     */
    private $sqlCode;

    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $elapPerExec;

    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $elapTime;

    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $cpuTime;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $cpu;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $io;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $getPerExec;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $bufferGet;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $parseCall;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $totalParse;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $maxVersionCount;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $totalCpu;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $totalGets;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $totalReads;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $totalElapTime;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $exec;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $cpuPerExec;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $rowProcessed;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $rowPerExec;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $sharableMemory;
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
     *
     */
    public function getName()
    {
        if ($this->hash)
            return $this->hash;
        else if ($this->sqlId)
            return $this->sqlId;
        else
            return null;
    }
    /**
     * Set sqlId
     *
     * @param string $sqlId
     *
     * @return Request
     */
    public function setSqlId($sqlId)
    {
        $this->sqlId = $sqlId;

        return $this;
    }

    /**
     * Get sqlId
     *
     * @return string
     */
    public function getSqlId()
    {
        return $this->sqlId;
    }

    /**
     * Set hash
     *
     * @param integer $hash
     *
     * @return Request
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return integer
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set sqlCode
     *
     * @param string $sqlCode
     *
     * @return Request
     */
    public function setSqlCode($sqlCode)
    {
        $this->sqlCode = $sqlCode;

        return $this;
    }

    /**
     * Get sqlCode
     *
     * @return string
     */
    public function getSqlCode()
    {
        return $this->sqlCode;
    }

    /**
     * Set elapPerExec
     *
     * @param float $elapPerExec
     *
     * @return Request
     */
    public function setElapPerExec($elapPerExec)
    {
        $this->elapPerExec = $elapPerExec;

        return $this;
    }

    /**
     * Get elapPerExec
     *
     * @return float
     */
    public function getElapPerExec()
    {
        return $this->elapPerExec;
    }

    /**
     * Set elapsedTime
     *
     * @param float $elapsedTime
     *
     * @return Request
     */
    public function setElapsedTime($elapsedTime)
    {
        $this->elapsedTime = $elapsedTime;

        return $this;
    }

    /**
     * Get elapsedTime
     *
     * @return float
     */
    public function getElapsedTime()
    {
        return $this->elapsedTime;
    }

    /**
     * Set cpuTime
     *
     * @param float $cpuTime
     *
     * @return Request
     */
    public function setCpuTime($cpuTime)
    {
        $this->cpuTime = $cpuTime;

        return $this;
    }

    /**
     * Get cpuTime
     *
     * @return float
     */
    public function getCpuTime()
    {
        return $this->cpuTime;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return Request
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set sqlInfo
     *
     * @param \AppBundle\Entity\Snapshots\SQLInfo $sqlInfo
     *
     * @return Request
     */
    public function setSqlInfo(\AppBundle\Entity\Snapshots\SQLInfo $sqlInfo = null)
    {
        $this->sqlInfo = $sqlInfo;

        return $this;
    }

    /**
     * Get sqlInfo
     *
     * @return \AppBundle\Entity\Snapshots\SQLInfo
     */
    public function getSqlInfo()
    {
        return $this->sqlInfo;
    }

    /**
     * Set elapTime
     *
     * @param float $elapTime
     *
     * @return Request
     */
    public function setElapTime($elapTime)
    {
        $this->elapTime = $elapTime;

        return $this;
    }

    /**
     * Get elapTime
     *
     * @return float
     */
    public function getElapTime()
    {
        return $this->elapTime;
    }

    /**
     * Set cpu
     *
     * @param float $cpu
     *
     * @return Request
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
     * Set io
     *
     * @param float $io
     *
     * @return Request
     */
    public function setIo($io)
    {
        $this->io = $io;

        return $this;
    }

    /**
     * Get io
     *
     * @return float
     */
    public function getIo()
    {
        return $this->io;
    }

    /**
     * Set getPerExec
     *
     * @param float $getPerExec
     *
     * @return Request
     */
    public function setGetPerExec($getPerExec)
    {
        $this->getPerExec = $getPerExec;

        return $this;
    }

    /**
     * Get getPerExec
     *
     * @return float
     */
    public function getGetPerExec()
    {
        return $this->getPerExec;
    }

    /**
     * Set bufferGet
     *
     * @param float $bufferGet
     *
     * @return Request
     */
    public function setBufferGet($bufferGet)
    {
        $this->bufferGet = $bufferGet;

        return $this;
    }

    /**
     * Get bufferGet
     *
     * @return float
     */
    public function getBufferGet()
    {
        return $this->bufferGet;
    }

    /**
     * Set parseCall
     *
     * @param float $parseCall
     *
     * @return Request
     */
    public function setParseCall($parseCall)
    {
        $this->parseCall = $parseCall;

        return $this;
    }

    /**
     * Get parseCall
     *
     * @return float
     */
    public function getParseCall()
    {
        return $this->parseCall;
    }

    /**
     * Set totalParse
     *
     * @param float $totalParse
     *
     * @return Request
     */
    public function setTotalParse($totalParse)
    {
        $this->totalParse = $totalParse;

        return $this;
    }

    /**
     * Get totalParse
     *
     * @return float
     */
    public function getTotalParse()
    {
        return $this->totalParse;
    }

    /**
     * Set maxVersionCount
     *
     * @param float $maxVersionCount
     *
     * @return Request
     */
    public function setMaxVersionCount($maxVersionCount)
    {
        $this->maxVersionCount = $maxVersionCount;

        return $this;
    }

    /**
     * Get maxVersionCount
     *
     * @return float
     */
    public function getMaxVersionCount()
    {
        return $this->maxVersionCount;
    }

    /**
     * Set cpuPerExec
     *
     * @param float $cpuPerExec
     *
     * @return Request
     */
    public function setCpuPerExec($cpuPerExec)
    {
        $this->cpuPerExec = $cpuPerExec;

        return $this;
    }

    /**
     * Get cpuPerExec
     *
     * @return float
     */
    public function getCpuPerExec()
    {
        return $this->cpuPerExec;
    }

    /**
     * Set rowProcessed
     *
     * @param float $rowProcessed
     *
     * @return Request
     */
    public function setRowProcessed($rowProcessed)
    {
        $this->rowProcessed = $rowProcessed;

        return $this;
    }

    /**
     * Get rowProcessed
     *
     * @return float
     */
    public function getRowProcessed()
    {
        return $this->rowProcessed;
    }

    /**
     * Set sharableMemory
     *
     * @param float $sharableMemory
     *
     * @return Request
     */
    public function setSharableMemory($sharableMemory)
    {
        $this->sharableMemory = $sharableMemory;

        return $this;
    }

    /**
     * Get sharableMemory
     *
     * @return float
     */
    public function getSharableMemory()
    {
        return $this->sharableMemory;
    }

    /**
     * Set rowPerExec
     *
     * @param float $rowPerExec
     *
     * @return Request
     */
    public function setRowPerExec($rowPerExec)
    {
        $this->rowPerExec = $rowPerExec;

        return $this;
    }

    /**
     * Get rowPerExec
     *
     * @return float
     */
    public function getRowPerExec()
    {
        return $this->rowPerExec;
    }

    /**
     * Set totalCpu
     *
     * @param float $totalCpu
     *
     * @return Request
     */
    public function setTotalCpu($totalCpu)
    {
        $this->totalCpu = $totalCpu;

        return $this;
    }

    /**
     * Get totalCpu
     *
     * @return float
     */
    public function getTotalCpu()
    {
        return $this->totalCpu;
    }

    /**
     * Set totalGets
     *
     * @param float $totalGets
     *
     * @return Request
     */
    public function setTotalGets($totalGets)
    {
        $this->totalGets = $totalGets;

        return $this;
    }

    /**
     * Get totalGets
     *
     * @return float
     */
    public function getTotalGets()
    {
        return $this->totalGets;
    }

    /**
     * Set totalReads
     *
     * @param float $totalReads
     *
     * @return Request
     */
    public function setTotalReads($totalReads)
    {
        $this->totalReads = $totalReads;

        return $this;
    }

    /**
     * Get totalReads
     *
     * @return float
     */
    public function getTotalReads()
    {
        return $this->totalReads;
    }

    /**
     * Set totalElapTime
     *
     * @param float $totalElapTime
     *
     * @return Request
     */
    public function setTotalElapTime($totalElapTime)
    {
        $this->totalElapTime = $totalElapTime;

        return $this;
    }

    /**
     * Get totalElapTime
     *
     * @return float
     */
    public function getTotalElapTime()
    {
        return $this->totalElapTime;
    }

    /**
     * Set exec
     *
     * @param float $exec
     *
     * @return Request
     */
    public function setExec($exec)
    {
        $this->exec = $exec;

        return $this;
    }

    /**
     * Get exec
     *
     * @return float
     */
    public function getExec()
    {
        return $this->exec;
    }

    /**
     * Set module
     *
     * @param string $module
     *
     * @return Request
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }
}
