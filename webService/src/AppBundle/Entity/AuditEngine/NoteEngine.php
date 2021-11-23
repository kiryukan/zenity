<?php
// src/AppBundle/Entity/AuditEngine/Note.php
namespace AppBundle\Entity\AuditEngine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="note_engine"
 * )
 */
class NoteEngine
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * la categorie a la quelle appartient ce conseil
     * @ORM\Column(type="string",length=100)
     */
    private $name;
    /**
     * @ORM\OneToMany(targetEntity="Indicator", mappedBy="note",cascade={"persist","remove"})
     * @ORM\OrderBy({"class" = "ASC","field"="ASC"})

     */
    private $indicators;
    /**
     * le nom du sgbd lié a la note
     * @ORM\Column(type="string",length=25)
     */
    private $sgbd = 'oracle';
    /**
     * la version du dit sgbd, en regex, avec par defaut *
     * @ORM\Column(type="string",length=25)
     */
    private $versionPattern = '.*';
    /**
    * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isStored = false;
    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isAvg = false;
    /**
     * Constructor
     */
    public function __construct($name = null)
    {
        $this->name = $name;
        $this->indicators = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param integer $id
     *
     * @return NoteEngine
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return NoteEngine
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
     * Add indicator
     *
     * @param \AppBundle\Entity\AuditEngine\Indicator $indicator
     *
     * @return NoteEngine
     */
    public function addIndicator(\AppBundle\Entity\AuditEngine\Indicator $indicator)
    {
        $this->indicators[] = $indicator;
        $indicator->setNote($this);
        return $this;
    }

    /**
     * Remove indicator
     *
     * @param \AppBundle\Entity\AuditEngine\Indicator $indicator
     */
    public function removeIndicator(\AppBundle\Entity\AuditEngine\Indicator $indicator)
    {
        $this->indicators->removeElement($indicator);
    }

    /**
     * Get indicators
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIndicators()
    {
        return $this->indicators;
    }

    /**
     * Set sgbd
     *
     * @param string $sgbd
     *
     * @return NoteEngine
     */
    public function setSgbd($sgbd)
    {
        $this->sgbd = $sgbd;

        return $this;
    }

    /**
     * Get sgbd
     *
     * @return string
     */
    public function getSgbd()
    {
        return $this->sgbd;
    }

    /**
     * Set version pattern
     *
     * @param string $versionPattern
     *
     * @return NoteEngine
     */
    public function setVersionPattern ($versionPattern)
    {
        $this->versionPattern = $versionPattern;

        return $this;
    }

    /**
     * Get version pattern
     *
     * @return string
     */
    public function getVersionPattern ()
    {
        return $this->versionPattern;
    }

    /**
     * Set isStored
     *
     * @param boolean $isStored
     *
     * @return NoteEngine
     */
    public function setIsStored($isStored)
    {
        $this->isStored = $isStored;

        return $this;
    }

    /**
     * Get isStored
     *
     * @return boolean
     */
    public function isStored()
    {
        return $this->isStored;
    }

    /**
     * Get isStored
     *
     * @return boolean
     */
    public function getIsStored()
    {
        return $this->isStored;
    }

    /**
     * Set isAvg
     *
     * @param boolean $isAvg
     *
     * @return NoteEngine
     */
    public function setIsAvg($isAvg)
    {
        $this->isAvg = $isAvg;

        return $this;
    }

    /**
     * Get isAvg
     *
     * @return boolean
     */
    public function getIsAvg()
    {
        return $this->isAvg;
    }
}
