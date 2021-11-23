<?php
// src/AppBundle/Entity/AuditEngine/Indicator.php
namespace AppBundle\Entity\AuditEngine;
use AppBundle\Entity\Snapshots\Event;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;
/**
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="indicator"
 * )
 *
 */

class Indicator
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * permet de calculer la note
     * @ORM\OneToMany(targetEntity="Filter", mappedBy="indicator",cascade={"persist","remove"})
     */
    private $filter;
    /**
     * permet de nomer un indicateur
     * @ORM\Column(type="string",length=50,nullable=true)
     */
    private $name;
    /**
     * @ORM\ManyToOne(targetEntity="NoteEngine", inversedBy="indicators",cascade={"persist"})
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="Advice", inversedBy="indicators",cascade={"persist"})
     */
    private $advice;
    /**
     * la classe(entitée) sur laquelle porte l'indicateur
     * @ORM\Column(type="string",length=50,nullable=true)
     */
    private $class;

    /**
     * le champ (property) sur lequel porte l'indicateur
     * @ORM\Column(type="string",length=50,nullable=true)
     */
    private $field;

    /**
     * la valeur a ne pas depasser  pour que le paramètre rentre en compte dans le calcul
     * @ORM\Column(type="string",length=50,nullable=true)
     */
    private $fieldExactValue;

    /**
     * le coefficient de l'indicateur
     *  @ORM\Column(type="float",nullable=true)
     */
    private $coeff;

    /**
     * la valeur a ne pas depasser  pour que le paramètre rentre en compte dans le calcul
     *  @ORM\Column(type="float",nullable=true)
     */
    private $maxValue;

    /**
     * la valeur a depasser  pour que le paramètre rentre en compte dans le calcul
     *  @ORM\Column(type="float",nullable=true)
     */
    private $minValue;

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
     * Set class
     *
     * @param string $class
     *
     * @return Indicator
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set field
     *
     * @param string $field
     *
     * @return Indicator
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
     * Set coeff
     *
     * @param integer $coeff
     *
     * @return Indicator
     */
    public function setCoeff($coeff)
    {
        $this->coeff = $coeff;

        return $this;
    }

    /**
     * Get coeff
     *
     * @return integer
     */
    public function getCoeff()
    {
        return $this->coeff;
    }

    /**
     * Set maxValue
     *
     * @param integer $maxValue
     *
     * @return Indicator
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;

        return $this;
    }

    /**
     * Get maxValue
     *
     * @return integer
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * Set minValue
     *
     * @param integer $minValue
     *
     * @return Indicator
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;

        return $this;
    }

    /**
     * Get minValue
     *
     * @return integer
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * Set note
     *
     * @param \AppBundle\Entity\AuditEngine\NoteEngine $note
     *
     * @return Indicator
     */
    public function setNote(\AppBundle\Entity\AuditEngine\NoteEngine $note = null)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return \AppBundle\Entity\AuditEngine\NoteEngine
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set advice
     *
     * @param \AppBundle\Entity\AuditEngine\Advice $advice
     *
     * @return Indicator
     */
    public function setAdvice(\AppBundle\Entity\AuditEngine\Advice $advice = null)
    {
        $this->advice = $advice;

        return $this;
    }

    /**
     * Get advice
     *
     * @return \AppBundle\Entity\AuditEngine\Advice
     */
    public function getAdvice()
    {
        return $this->advice;
    }

    /**
     * Set fieldExactValue
     *
     * @param string $fieldExactValue
     *
     * @return Indicator
     */
    public function setFieldExactValue($fieldExactValue)
    {
        $this->fieldExactValue = $fieldExactValue;

        return $this;
    }

    /**
     * Get fieldExactValue
     *
     * @return string
     */
    public function getFieldExactValue()
    {
        return $this->fieldExactValue;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->filter = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add filter
     *
     * @param \AppBundle\Entity\AuditEngine\Filter $filter
     *
     * @return Indicator
     */
    public function addFilter(\AppBundle\Entity\AuditEngine\Filter $filter)
    {
        $this->filter[] = $filter;
        $filter->setIndicator($this);
        return $this;
    }

    /**
     * Remove filter
     *
     * @param \AppBundle\Entity\AuditEngine\Filter $filter
     */
    public function removeFilter(\AppBundle\Entity\AuditEngine\Filter $filter)
    {
        $this->filter->removeElement($filter);
    }

    /**
     * Get filter
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Indicator
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
}
