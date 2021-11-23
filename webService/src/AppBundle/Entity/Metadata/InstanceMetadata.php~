<?php

namespace AppBundle\Entity\Metadata;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="instance_metadata")
 */
class InstanceMetadata
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     *  @ORM\Column(type="integer")
     *  @ORM\OneToOne(targetEntity="Instance")
     */
    private $instance;
    /**
     *  @ORM\Column(type="integer",options={"default" = 0})
     */
    private $nbSnapshot;

    /**
     * Set instance
     *
     * @param integer $instance
     *
     * @return InstanceMetadata
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get instance
     *
     * @return integer
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Set nbSnapshot
     *
     * @param integer $nbSnapshot
     *
     * @return InstanceMetadata
     */
    public function setNbSnapshot($nbSnapshot)
    {
        $this->nbSnapshot = $nbSnapshot;

        return $this;
    }

    /**
     * Get nbSnapshot
     *
     * @return integer
     */
    public function getNbSnapshot()
    {
        return $this->nbSnapshot;
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return InstanceMetadata
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
}
