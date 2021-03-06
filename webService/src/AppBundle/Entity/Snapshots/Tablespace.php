<?php
// src/AppBundle/Entity/Snapshots/Tablespace.php
namespace AppBundle\Entity\Snapshots;
use AppBundle\Entity\INamable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="tablespace")
 * Contient les infos sur les lectures/écritures sur une BDD
 */

class Tablespace implements INamable
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /** Nom du tablespace
     * @ORM\Column(type="string",length=250)
     */
    private $name;

    /**
     *  @ORM\Column(type="integer",nullable=true)
     */
    private $readNb;
    /**
     *  @ORM\Column(type="integer",nullable=true)
     */
    private $avReadS;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $avRead;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $avBlkReads;
    /**
     *  @ORM\Column(type="integer",nullable=true)
     */
    private $avWritesS;
    /**
     *  @ORM\Column(type="integer",nullable=true)
     */
    private $bufferWaits;
    /**
     *  @ORM\Column(type="float",nullable=true)
     */
    private $avBufferWaits;
    
    /**
     *  @ORM\Column(type="integer",nullable=true)
     */
    private $writesNb;
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $histogram;
    /** nécessaire pour un OneToMany dans @link Ressources
     * @ORM\ManyToOne(targetEntity="Snapshot", inversedBy="tablespaces")
     */
    private $snapshot;

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
     * @return Tablespace
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
     * Set readNb
     *
     * @param integer $readNb
     *
     * @return Tablespace
     */
    public function setReadNb($readNb)
    {
        $this->readNb = $readNb;

        return $this;
    }

    /**
     * Get readNb
     *
     * @return integer
     */
    public function getReadNb()
    {
        return $this->readNb;
    }

    /**
     * Set avReadS
     *
     * @param integer $avReadS
     *
     * @return Tablespace
     */
    public function setAvReadS($avReadS)
    {
        $this->avReadS = $avReadS;

        return $this;
    }

    /**
     * Get avReadS
     *
     * @return integer
     */
    public function getAvReadS()
    {
        return $this->avReadS;
    }

    /**
     * Set avRead
     *
     * @param float $avRead
     *
     * @return Tablespace
     */
    public function setAvRead($avRead)
    {
        $this->avRead = $avRead;

        return $this;
    }

    /**
     * Get avRead
     *
     * @return float
     */
    public function getAvRead()
    {
        return $this->avRead;
    }

    /**
     * Set avBlkReads
     *
     * @param float $avBlkReads
     *
     * @return Tablespace
     */
    public function setAvBlkReads($avBlkReads)
    {
        $this->avBlkReads = $avBlkReads;

        return $this;
    }

    /**
     * Get avBlkReads
     *
     * @return float
     */
    public function getAvBlkReads()
    {
        return $this->avBlkReads;
    }

    /**
     * Set avWritesS
     *
     * @param integer $avWritesS
     *
     * @return Tablespace
     */
    public function setAvWritesS($avWritesS)
    {
        $this->avWritesS = $avWritesS;

        return $this;
    }

    /**
     * Get avWritesS
     *
     * @return integer
     */
    public function getAvWritesS()
    {
        return $this->avWritesS;
    }

    /**
     * Set bufferWaits
     *
     * @param integer $bufferWaits
     *
     * @return Tablespace
     */
    public function setBufferWaits($bufferWaits)
    {
        $this->bufferWaits = $bufferWaits;

        return $this;
    }

    /**
     * Get bufferWaits
     *
     * @return integer
     */
    public function getBufferWaits()
    {
        return $this->bufferWaits;
    }

    /**
     * Set avBufferWaits
     *
     * @param float $avBufferWaits
     *
     * @return Tablespace
     */
    public function setAvBufferWaits($avBufferWaits)
    {
        $this->avBufferWaits = $avBufferWaits;

        return $this;
    }

    /**
     * Get avBufferWaits
     *
     * @return float
     */
    public function getAvBufferWaits()
    {
        return $this->avBufferWaits;
    }

    /**
     * Set histogram
     *
     * @param array $histogram
     *
     * @return Tablespace
     */
    public function setHistogram($histogram)
    {
        $this->histogram = $histogram;

        return $this;
    }

    /**
     * Get histogram
     *
     * @return array
     */
    public function getHistogram()
    {
        return $this->histogram;
    }

    /**
     * Set snapshot
     *
     * @param \AppBundle\Entity\Snapshots\Snapshot $snapshot
     *
     * @return Tablespace
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
     * Set writesNb
     *
     * @param integer $writesNb
     *
     * @return Tablespace
     */
    public function setWritesNb($writesNb)
    {
        $this->writesNb = $writesNb;

        return $this;
    }

    /**
     * Get writesNb
     *
     * @return integer
     */
    public function getWritesNb()
    {
        return $this->writesNb;
    }
}
