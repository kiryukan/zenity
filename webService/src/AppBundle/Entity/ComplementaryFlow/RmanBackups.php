<?php
namespace AppBundle\Entity\ComplementaryFlow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="RmanBackups")
 *
 */

class RmanBackups
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /** nécessaire pour un OneToMany dans @link Rman
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\ComplementaryFlow\Rman", inversedBy="backups",cascade={"persist","remove"}))
     */
    private $rman;
    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $startDate;
    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $endDate;
    /**
     * @ORM\Column(type="string",length=30)
     */
    private $type;
    /**
     * @ORM\Column(type="string",length=30)
     */
    private $outputDevice;
    /**
     * @ORM\Column(type="integer")
     */
    private $inputSize;
    /**
     * @ORM\Column(type="integer")
     */
    private $outputSize;

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
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return RmanBackups
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return RmanBackups
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return RmanBackups
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set outputDevice
     *
     * @param string $outputDevice
     *
     * @return RmanBackups
     */
    public function setOutputDevice($outputDevice)
    {
        $this->outputDevice = $outputDevice;

        return $this;
    }

    /**
     * Get outputDevice
     *
     * @return string
     */
    public function getOutputDevice()
    {
        return $this->outputDevice;
    }

    /**
     * Set inputSize
     *
     * @param integer $inputSize
     *
     * @return RmanBackups
     */
    public function setInputSize($inputSize)
    {
        $this->inputSize = $inputSize;

        return $this;
    }

    /**
     * Get inputSize
     *
     * @return integer
     */
    public function getInputSize()
    {
        return $this->inputSize;
    }

    /**
     * Set outputSize
     *
     * @param integer $outputSize
     *
     * @return RmanBackups
     */
    public function setOutputSize($outputSize)
    {
        $this->outputSize = $outputSize;

        return $this;
    }

    /**
     * Get outputSize
     *
     * @return integer
     */
    public function getOutputSize()
    {
        return $this->outputSize;
    }

    /**
     * Set rman
     *
     * @param \AppBundle\Entity\ComplementaryFlow\Rman $rman
     *
     * @return RmanBackups
     */
    public function setRman(\AppBundle\Entity\ComplementaryFlow\Rman $rman = null)
    {
        $this->rman = $rman;

        return $this;
    }

    /**
     * Get rman
     *
     * @return \AppBundle\Entity\ComplementaryFlow\Rman
     */
    public function getRman()
    {
        return $this->rman;
    }
}
