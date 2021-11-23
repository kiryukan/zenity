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
 *     name="options_cloud_instance"
 *  )
 *
 */
class OptionsCloudInstance
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * Nom du type
     * @ORM\Column(type="string",length=50)
     */
    private $name;
    /**
    * mapping to instance_details
    * @ORM\OneToMany(targetEntity="\AppBundle\Entity\CloudBase\InstanceDetails", mappedBy="optionsCloudInstance")
    */
    private $instanceDetails;
    /**
    * mapping to pack_edition
    * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\CloudBase\PackEdition", inversedBy="optionsCloudInstanceArray")
    */
    private $packEdition;

    public function __construct(){
        $this->instanceDetails = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     * @return integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Set name
     * @param string $name
     * @return OptionsCloudInstance
     */
    public function setName($name){
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName(){
        return $this->name;
    }
    
    // MODIF NEW PRICES
    //-----------------
    //-----------------
    /**
     * Add instance details
     * @param \AppBundle\Entity\CloudBase\InstanceDetails $instanceDetails
     * @return OptionsCloudInstance
     */
    public function addInstanceDetails(\AppBundle\Entity\CloudBase\InstanceDetails $instanceDetails)
    {
        $instanceDetails->setOptionsCloudInstance($this);
        $this->instanceDetails[] = $instanceDetails;
        return $this;
    }

    /**
     * Get instance details
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInstanceDetails()
    {
        return $this->instanceDetails;
    }

    /**
     * Set pack edition
     * @param \AppBundle\Entity\CloudBase\PackEdition $packEdition
     *
     * @return OptionsCloudInstance
     */
    public function setPackEdition(\AppBundle\Entity\CloudBase\PackEdition $packEdition)
    {
        $this->packEdition = $packEdition;

        return $this;
    }

    /**
     * Get pack edition
     * @return \AppBundle\Entity\CloudBase\PackEdition
     */
    public function getPackEdition()
    {
        return $this->packEdition;
    }
}