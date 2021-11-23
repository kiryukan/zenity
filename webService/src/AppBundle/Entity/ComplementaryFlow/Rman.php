<?php
namespace AppBundle\Entity\ComplementaryFlow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Rman")
 *
 */

class Rman
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /** nécessaire pour un OneToMany dans @link Ressources
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Base\Instance", inversedBy="rman",cascade={"persist","remove"}))
     */
    private $instance;
    /**
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\ComplementaryFlow\RmanParameters",mappedBy="rman",cascade={"persist","remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $parameters;
    /**
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\ComplementaryFlow\RmanBackups",mappedBy="rman",cascade={"persist","remove"})
     * @ORM\OrderBy({"startDate" = "ASC"})
     */
    private $backups;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameters = new \Doctrine\Common\Collections\ArrayCollection();
        $this->backups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set instance
     *
     * @param \AppBundle\Entity\Base\Instance $instance
     *
     * @return Rman
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

    /**
     * Add parameter
     *
     * @param \AppBundle\Entity\ComplementaryFlow\RmanParameters $parameter
     *
     * @return Rman
     */
    public function addParameter(\AppBundle\Entity\ComplementaryFlow\RmanParameters $parameter)
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * Remove parameter
     *
     * @param \AppBundle\Entity\ComplementaryFlow\RmanParameters $parameter
     */
    public function removeParameter(\AppBundle\Entity\ComplementaryFlow\RmanParameters $parameter)
    {
        $this->parameters->removeElement($parameter);
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Add backup
     *
     * @param \AppBundle\Entity\ComplementaryFlow\RmanBackups $backup
     *
     * @return Rman
     */
    public function addBackup(\AppBundle\Entity\ComplementaryFlow\RmanBackups $backup)
    {
        $this->backups[] = $backup;

        return $this;
    }

    /**
     * Remove backup
     *
     * @param \AppBundle\Entity\ComplementaryFlow\RmanBackups $backup
     */
    public function removeBackup(\AppBundle\Entity\ComplementaryFlow\RmanBackups $backup)
    {
        $this->backups->removeElement($backup);
    }

    /**
     * Get backups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBackups()
    {
        return $this->backups;
    }
}
