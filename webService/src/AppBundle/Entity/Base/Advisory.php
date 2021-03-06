<?php

namespace AppBundle\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
/**
 * @ORM\Entity
 * @ORM\Table(name="advisory",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"instance_id", "name"})},
 *     indexes={@Index(name="search_idx", columns={"name", "instance_id"})})
 * Contient les conseils sur les tailles de pga/sga...
 */
class Advisory
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /** nécessaire pour un OneToMany dans @link Ressources
     * @ORM\ManyToOne(targetEntity="Instance", inversedBy="advisory",cascade={"persist","remove"}))
     */
    private $instance;
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

    public function updateFromJson($json)
    {
        $jsonDecoded = json_decode($json,true);
        $this->name = (isset($jsonDecoded['name']))?$jsonDecoded['name']:$this->name;
        $this->advisoryMap = (isset($jsonDecoded['advisoryMap']))?$jsonDecoded['advisoryMap']:$this->name;
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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Advisory
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
     * @return Advisory
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
     * Set instance
     *
     * @param \AppBundle\Entity\Base\Instance $instance
     *
     * @return Advisory
     */
    public function setInstance(\AppBundle\Entity\Base\Instance $instance = null)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get instance
     *
     * @return \AppBundle\Entity\Base\Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }
}
