<?php
// src/AppBundle/Entity/Snapshots/EfficiencyIndicator.php
namespace AppBundle\Entity\Snapshots;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="efficiencyIndicator")
 * Contient des infos génerale sur la performance d'une instance
 */

class EfficiencyIndicator
{

    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /**
     *
     * @ORM\Column(type="integer",nullable=true)
     */
    private $bufferNoWait;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $redoNoWait;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $inMemorySort;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $optimalWA;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $bufferHit;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $libraryHit;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $softParse;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $execToParse;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $latchHit;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $cpuToParse;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $nonParseCpu;

    /**
     * @ORM\Column(type="integer",nullable=True)
     */
    private $pgaCacheHit;


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
     * Set bufferNoWait
     *
     * @param integer $bufferNoWait
     *
     * @return EfficiencyIndicator
     */
    public function setBufferNoWait($bufferNoWait)
    {
        $this->bufferNoWait = $bufferNoWait;

        return $this;
    }

    /**
     * Get bufferNoWait
     *
     * @return integer
     */
    public function getBufferNoWait()
    {
        return $this->bufferNoWait;
    }

    /**
     * Set redoNoWait
     *
     * @param integer $redoNoWait
     *
     * @return EfficiencyIndicator
     */
    public function setRedoNoWait($redoNoWait)
    {
        $this->redoNoWait = $redoNoWait;

        return $this;
    }

    /**
     * Get redoNoWait
     *
     * @return integer
     */
    public function getRedoNoWait()
    {
        return $this->redoNoWait;
    }

    /**
     * Set optimalWA
     *
     * @param integer $optimalWA
     *
     * @return EfficiencyIndicator
     */
    public function setOptimalWA($optimalWA)
    {
        $this->optimalWA = $optimalWA;

        return $this;
    }

    /**
     * Get optimalWA
     *
     * @return integer
     */
    public function getOptimalWA()
    {
        return $this->optimalWA;
    }

    /**
     * Set bufferHit
     *
     * @param integer $bufferHit
     *
     * @return EfficiencyIndicator
     */
    public function setBufferHit($bufferHit)
    {
        $this->bufferHit = $bufferHit;

        return $this;
    }

    /**
     * Get bufferHit
     *
     * @return integer
     */
    public function getBufferHit()
    {
        return $this->bufferHit;
    }

    /**
     * Set libraryHit
     *
     * @param integer $libraryHit
     *
     * @return EfficiencyIndicator
     */
    public function setLibraryHit($libraryHit)
    {
        $this->libraryHit = $libraryHit;

        return $this;
    }

    /**
     * Get libraryHit
     *
     * @return integer
     */
    public function getLibraryHit()
    {
        return $this->libraryHit;
    }

    /**
     * Set softParse
     *
     * @param integer $softParse
     *
     * @return EfficiencyIndicator
     */
    public function setSoftParse($softParse)
    {
        $this->softParse = $softParse;

        return $this;
    }

    /**
     * Get softParse
     *
     * @return integer
     */
    public function getSoftParse()
    {
        return $this->softParse;
    }

    /**
     * Set execToParse
     *
     * @param integer $execToParse
     *
     * @return EfficiencyIndicator
     */
    public function setExecToParse($execToParse)
    {
        $this->execToParse = $execToParse;

        return $this;
    }

    /**
     * Get execToParse
     *
     * @return integer
     */
    public function getExecToParse()
    {
        return $this->execToParse;
    }

    /**
     * Set latchHit
     *
     * @param integer $latchHit
     *
     * @return EfficiencyIndicator
     */
    public function setLatchHit($latchHit)
    {
        $this->latchHit = $latchHit;

        return $this;
    }

    /**
     * Get latchHit
     *
     * @return integer
     */
    public function getLatchHit()
    {
        return $this->latchHit;
    }

    /**
     * Set cpuToParse
     *
     * @param integer $cpuToParse
     *
     * @return EfficiencyIndicator
     */
    public function setCpuToParse($cpuToParse)
    {
        $this->cpuToParse = $cpuToParse;

        return $this;
    }

    /**
     * Get cpuToParse
     *
     * @return integer
     */
    public function getCpuToParse()
    {
        return $this->cpuToParse;
    }

    /**
     * Set nonParseCpu
     *
     * @param integer $nonParseCpu
     *
     * @return EfficiencyIndicator
     */
    public function setNonParseCpu($nonParseCpu)
    {
        $this->nonParseCpu = $nonParseCpu;

        return $this;
    }

    /**
     * Get nonParseCpu
     *
     * @return integer
     */
    public function getNonParseCpu()
    {
        return $this->nonParseCpu;
    }

    /**
     * Set pgaCacheHit
     *
     * @param integer $pgaCacheHit
     *
     * @return EfficiencyIndicator
     */
    public function setPgaCacheHit($pgaCacheHit)
    {
        $this->pgaCacheHit = $pgaCacheHit;

        return $this;
    }

    /**
     * Get pgaCacheHit
     *
     * @return integer
     */
    public function getPgaCacheHit()
    {
        return $this->pgaCacheHit;
    }

    /**
     * Set inMemorySort
     *
     * @param integer $inMemorySort
     *
     * @return EfficiencyIndicator
     */
    public function setInMemorySort($inMemorySort)
    {
        $this->inMemorySort = $inMemorySort;

        return $this;
    }

    /**
     * Get inMemorySort
     *
     * @return integer
     */
    public function getInMemorySort()
    {
        return $this->inMemorySort;
    }
}
