<?php
// src/AppBundle/Entity/AuditEngine/Advice.php
namespace AppBundle\Entity\AuditEngine;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="advice"
 * )
 */
class Advice
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /**
     * le groupe auquel appartient le conseil
     * @ORM\Column(type="string",length=50,nullable=true)
     */
    private $groupName;

    /**
     * Le message a afficher si le conseil est activé
     * @ORM\Column(type="string",length=500,nullable=true)
     */
    private $message;
    /**
     * permet de trigger ou non le conseil
     * @ORM\OneToMany(targetEntity="Indicator", mappedBy="advice",cascade={"persist","remove"})
     */
    private $indicators;

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
     * Set message
     *
     * @param string $message
     *
     * @return Advice
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->indicators = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set groupName
     *
     * @param string $groupName
     *
     * @return Advice
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Add indicator
     *
     * @param \AppBundle\Entity\AuditEngine\Indicator $indicator
     *
     * @return Advice
     */
    public function addIndicator(\AppBundle\Entity\AuditEngine\Indicator $indicator)
    {
        $this->indicators[] = $indicator;
        $indicator->setAdvice($this);
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
}
