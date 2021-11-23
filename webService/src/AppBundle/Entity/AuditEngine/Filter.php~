<?php

namespace AppBundle\Entity\AuditEngine;
use Doctrine\ORM\Mapping as ORM;

/**
 * contient un permet de filtrer des classes par une
 * -
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="filter")
 *
 */

class Filter
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Indicator", inversedBy="filter",cascade={"persist","remove"})
     */
    private $indicator;
    /**
     * le champ (property) sur lequel porte le filtre
     * @ORM\Column(type="string",length=50,nullable=true)
     */
    private $field;
    /**
     * la valeurs du filtre
     * @ORM\Column(type="string",length=50,nullable=true)
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
     * Set field
     *
     * @param string $field
     *
     * @return Filter
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Filter
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
     * Set indicator
     *
     * @param \AppBundle\Entity\AuditEngine\Indicator $indicator
     *
     * @return Filter
     */
    public function setIndicator(\AppBundle\Entity\AuditEngine\Indicator $indicator)
    {
        $this->indicator = $indicator;

        return $this;
    }

    /**
     * Get indicator
     *
     * @return \AppBundle\Entity\AuditEngine\Indicator
     */
    public function getIndicator()
    {
        return $this->indicator;
    }
}
