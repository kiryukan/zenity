<?php
// src/AppBundle/Entity/Snapshots/SqlInfo.php
namespace AppBundle\Entity\Snapshots;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="sqlInfo")
 * Les données liée a des requetes SQL
 */
class SqlInfo
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * L'utilisation mémoire des requetes executée plus d'une fois
     * @ORM\Column(type="float",nullable=true)
     */
    private $repeatedRequestMemory;

    /**
     * Le pourcentage de requetes éxécutées plus de une fois
     * @ORM\Column(type="float",nullable=true)
     */
    private $repeatedRequestPercent;
    /**
     * Le pourcentage de requetes éxécutées plus de une fois
     * @ORM\Column(type="integer",nullable=true)
     */
    private $nbExec;
    /**
     * Les requetes SQL les plus importantes
     * @ORM\OneToMany(targetEntity="Request", mappedBy="sqlInfo",cascade={"persist","remove"})
     */
    private $requests;



    /**
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->requests = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add request
     *
     * @param \AppBundle\Entity\Snapshots\Request $request
     *
     * @return SqlInfo
     */
    public function addRequest(\AppBundle\Entity\Snapshots\Request $request)
    {
        $request->setSqlInfo($this);
        $this->requests[] = $request;

        return $this;
    }

    /**
     * Remove request
     *
     * @param \AppBundle\Entity\Snapshots\Request $request
     */
    public function removeRequest(\AppBundle\Entity\Snapshots\Request $request)
    {
        $request->setSqlInfo(null);
        $this->requests->removeElement($request);
    }

    /**
     * Get requests
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Set repeatedRequestMemory
     *
     * @param integer $repeatedRequestMemory
     *
     * @return SqlInfo
     */
    public function setRepeatedRequestMemory($repeatedRequestMemory)
    {
        $this->repeatedRequestMemory = $repeatedRequestMemory;

        return $this;
    }

    /**
     * Get repeatedRequestMemory
     *
     * @return integer
     */
    public function getRepeatedRequestMemory()
    {
        return $this->repeatedRequestMemory;
    }

    /**
     * Set repeatedRequestNb
     *
     * @param integer $repeatedRequestNb
     *
     * @return SqlInfo
     */
    public function setRepeatedRequestNb($repeatedRequestNb)
    {
        $this->repeatedRequestNb = $repeatedRequestNb;

        return $this;
    }


    /**
     * Set repeatedRequestPercent
     *
     * @param integer $repeatedRequestPercent
     *
     * @return SqlInfo
     */
    public function setRepeatedRequestPercent($repeatedRequestPercent)
    {
        $this->repeatedRequestPercent = $repeatedRequestPercent;

        return $this;
    }

    /**
     * Get repeatedRequestPercent
     *
     * @return integer
     */
    public function getRepeatedRequestPercent()
    {
        return $this->repeatedRequestPercent;
    }

    /**
     * Set nbExec
     *
     * @param integer $nbExec
     *
     * @return SqlInfo
     */
    public function setNbExec($nbExec)
    {
        $this->nbExec = $nbExec;

        return $this;
    }

    /**
     * Get nbExec
     *
     * @return integer
     */
    public function getNbExec()
    {
        return $this->nbExec;
    }

    //-------------------------------------------------Calculated stats-------------------------------------------------


    /**
     * Set memoryUsage
     *
     * @param float $memoryUsage
     *
     * @return SqlInfo
     */
    public function setMemoryUsage($memoryUsage)
    {
        $this->memoryUsage = $memoryUsage;

        return $this;
    }

    /**
     * Get memoryUsage
     *
     * @return float
     */
    public function getMemoryUsage()
    {
        return $this->memoryUsage;
    }
}
