<?php


namespace AppBundle\Entity\Snapshots;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="advisory_snapshot")
 * Contient les conseils sur les tailles de pga/sga...
 */
class AdvisorySnapshot
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /** nécessaire pour un OneToMany dans @link Ressources
     * @ORM\ManyToOne(targetEntity="Snapshot", inversedBy="advisory",cascade={"persist","remove"}))
     */
    private $snapshot;
    /**
     *
     * @ORM\Column(type="string",nullable=true)
     */
    private $name;
    /**
     *
     * @ORM\Column(type="json_array",nullable=true)
     */
    private $advisoryMap;

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
     * @return AdvisorySnapshot
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
     * Set advisoryMap
     *
     * @param array $advisoryMap
     *
     * @return AdvisorySnapshot
     */
    public function setAdvisoryMap($advisoryMap)
    {
        $this->advisoryMap = $advisoryMap;

        return $this;
    }

    /**
     * Get advisoryMap
     *
     * @return array
     */
    public function getAdvisoryMap()
    {
        return $this->advisoryMap;
    }

    /**
     * Set snapshot
     *
     * @param \AppBundle\Entity\Snapshots\Snapshot $snapshot
     *
     * @return AdvisorySnapshot
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
}
