<?php


namespace AppBundle\Entity\Snapshots;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="parameters")
 *
 */
class Parameters
{
    /** nécessaire pour un OneToMany dans @link Ressources
     * @ORM\ManyToOne(targetEntity="Snapshot", inversedBy="parameters",cascade={"persist","remove"}))
     */
    private $snapshot;
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=30)
     */
    private $name;
    /**
     * @ORM\Column(type="string",length=300)
     */
    private $value;


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
     * @return Parameters
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
     * Set value
     *
     * @param string $value
     *
     * @return Parameters
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set snapshot
     *
     * @param \AppBundle\Entity\Snapshots\Snapshot $snapshot
     *
     * @return Parameters
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
