<?php


namespace AppBundle\Entity\Snapshots;
use AppBundle\Entity\INamable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="stat")
 * Un event important en ressources sur un @link Ressources
 */
class Stat implements INamable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;
    /** nécessaire pour un OneToMany dans @link Ressources
     * @ORM\ManyToOne(targetEntity="Snapshot", inversedBy="stats",cascade={"persist","remove"}))
     */
    private $snapshot;
    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Metadata\StatMetadata")
     */
    private $statMetadata;
    /**
     * @ORM\Column(type="string",length=80)
     **/
    private $name;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $total;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $perSec;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $perTrans;


    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $perHour;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Stat
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return Stat
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
     * Set perSec
     *
     * @param float $perSec
     *
     * @return Stat
     */
    public function setPerSec($perSec)
    {
        $this->perSec = $perSec;

        return $this;
    }

    /**
     * Get perSec
     *
     * @return float
     */
    public function getPerSec()
    {
        return $this->perSec;
    }

    /**
     * Set perTrans
     *
     * @param float $perTrans
     *
     * @return Stat
     */
    public function setPerTrans($perTrans)
    {
        $this->perTrans = $perTrans;

        return $this;
    }

    /**
     * Get perTrans
     *
     * @return float
     */
    public function getPerTrans()
    {
        return $this->perTrans;
    }
    /**
     * Set perHour
     *
     * @param float $perTrans
     *
     * @return Stat
     */
    public function setPerHour($perHour)
    {
        $this->perHour = $perHour;

        return $this;
    }

    /**
     * Get perHour
     *
     * @return float
     */
    public function getPerHour()
    {
        return $this->perHour;
    }
    /**
     * Set snapshot
     *
     * @param \AppBundle\Entity\Snapshots\Snapshot $snapshot
     *
     * @return Stat
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
     * Set statMetadata
     *
     * @param \AppBundle\Entity\Metadata\StatMetadata $statMetadata
     *
     * @return Stat
     */
    public function setStatMetadata(\AppBundle\Entity\Metadata\StatMetadata $statMetadata = null)
    {
        $this->statMetadata = $statMetadata;

        return $this;
    }

    /**
     * Get statMetadata
     *
     * @return \AppBundle\Entity\Metadata\StatMetadata
     */
    public function getStatMetadata()
    {
        return $this->statMetadata;
    }
}
