<?php
// src/AppBundle/Entity/Base/Instance.php
namespace AppBundle\Entity\CloudBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Translation\Interval;

/**
 * Created by PhpStorm.
 * User: allonzo
 * Date: 22/11/16
 * Time: 08:22
 */
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="cloud_instance"
 *  )
 *
 */
class CloudInstance
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\ComplementaryFlow\PerformanceBench",cascade={"persist","remove"})
     * @ORM\OrderBy({"timestamp" = "ASC"})
     */
    private $performanceBench;
    /**
     * @ORM\Column(type="string",length=30)
     */
    private $name='unamed';
    /**
     * @ORM\Column(type="text")
     */
    private $technicalInfos='';
    /**
     * @ORM\Column(type="text")
     */
    private $cpuCountRule='1';
    /**
     * @ORM\Column(type="float")
     */
    private $costPerCpu=0;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
     private $baseCost;
    /**
    * @ORM\Column(type="integer")
    */
    private $baseStorageCapacity=50;
    /**
    * @ORM\Column(type="integer")
    */
    private $minCpuCount=1;
    /**
    * @ORM\Column(type="integer")
    */
    private $maxCpuCount=500;
    /**
    * @ORM\Column(type="float")
    */
    private $costPerGo=0;
    /**
    * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\CloudBase\Provider")
    */
    private $provider;
    /**
     * @ORM\Column(type="string",length=40,nullable =true)
     */
    private $edition;
    /**
     * @ORM\Column(type="string",length=70,nullable =true)
     */
    private $banner;
    /**
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\CloudBase\Tarif", mappedBy="cloudInstance")
     */
    private $tarifs;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarifs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return CloudInstance
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
     * Set technicalInfos
     *
     * @param string $technicalInfos
     *
     * @return CloudInstance
     */
    public function setTechnicalInfos($technicalInfos)
    {
        $this->technicalInfos = $technicalInfos;

        return $this;
    }

    /**
     * Get technicalInfos
     *
     * @return string
     */
    public function getTechnicalInfos()
    {
        return $this->technicalInfos;
    }

    /**
     * Set performanceBench
     *
     * @param \AppBundle\Entity\ComplementaryFlow\PerformanceBench $performanceBench
     *
     * @return CloudInstance
     */
    public function setPerformanceBench(\AppBundle\Entity\ComplementaryFlow\PerformanceBench $performanceBench = null)
    {
        $this->performanceBench = $performanceBench;

        return $this;
    }

    /**
     * Get performanceBench
     *
     * @return \AppBundle\Entity\ComplementaryFlow\PerformanceBench
     */
    public function getPerformanceBench()
    {
        return $this->performanceBench;
    }

     /* Set provider
     *
     * @param \AppBundle\Entity\CloudBase\Provider $provider
     *
     * @return CloudInstance
     */
    public function setProvider(\AppBundle\Entity\CloudBase\Provider $provider = null)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return \AppBundle\Entity\CloudBase\Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set costPerCpu
     *
     * @param integer $costPerCpu
     *
     * @return CloudInstance
     */
    public function setCostPerCpu($costPerCpu)
    {
        $this->costPerCpu = $costPerCpu;

        return $this;
    }

    /**
     * Get costPerCpu
     *
     * @return float
     */
    public function getCostPerCpu()
    {
        return $this->costPerCpu;
    }

    /**
     * Set baseCost
     *
     * @param integer $baseCost
     *
     * @return CloudInstance
     */
    public function setBaseCost($baseCost)
    {
        $this->baseCost = $baseCost;

        return $this;
    }

    /**
     * Get baseCost
     *
     * @return integer
     */
    public function getBaseCost()
    {
        return $this->baseCost;
    }

    /**
     * Set baseStorageCapacity
     *
     * @param integer $baseStorageCapacity
     *
     * @return CloudInstance
     */
    public function setBaseStorageCapacity($baseStorageCapacity)
    {
        $this->baseStorageCapacity = $baseStorageCapacity;

        return $this;
    }

    /**
     * Get baseStorageCapacity
     *
     * @return integer
     */
    public function getBaseStorageCapacity()
    {
        return $this->baseStorageCapacity;
    }

    /**
     * Set costPerGo
     *
     * @param integer $costPerGo
     *
     * @return CloudInstance
     */
    public function setCostPerGo($costPerGo)
    {
        $this->costPerGo = $costPerGo;

        return $this;
    }

    /**
     * Get costPerGo
     *
     * @return integer
     */
    public function getCostPerGo()
    {
        return $this->costPerGo;
    }

    /**
     * Set cpuCountRule
     *
     * @param string $cpuCountRule
     *
     * @return CloudInstance
     */
    public function setCpuCountRule($cpuCountRule)
    {
        $this->cpuCountRule = $cpuCountRule;

        return $this;
    }

    /**
     * Get cpuCountRule
     *
     * @return string
     */
    public function getCpuCountRule()
    {
        return $this->cpuCountRule;
    }

    /**
     * Set minCpuCount
     *
     * @param integer $minCpuCount
     *
     * @return CloudInstance
     */
    public function setMinCpuCount($minCpuCount)
    {
        $this->minCpuCount = $minCpuCount;

        return $this;
    }

    /**
     * Get minCpuCount
     *
     * @return integer
     */
    public function getMinCpuCount()
    {
        return $this->minCpuCount;
    }

    /**
     * Set maxCpuCount
     *
     * @param integer $maxCpuCount
     *
     * @return CloudInstance
     */
    public function setMaxCpuCount($maxCpuCount)
    {
        $this->maxCpuCount = $maxCpuCount;

        return $this;
    }

    /**
     * Get maxCpuCount
     *
     * @return integer
     */
    public function getMaxCpuCount()
    {
        return $this->maxCpuCount;
    }

    /**
     * @return mixed
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * @param mixed $text
     * @return Daily
     */
    public function setEdition($edition)
    {
        $this->text = $edition;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * @param mixed $banner
     * @return Daily
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;
        return $this;
    }

    /**
     * Add tarif
     *
     * @param \AppBundle\Entity\CloudBase\Tarif $tarif
     *
     * @return CloudInstance
     */
    public function addTarif(\AppBundle\Entity\CloudBase\InstanceDetails $tarif)
    {
        $tarifs->setCloudInstance($this);
        $this->tarifs[] = $tarif;
        return $this;
    }

    /**
     * Get tarif
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarif()
    {
        return $this->tarifs;
    }
}
