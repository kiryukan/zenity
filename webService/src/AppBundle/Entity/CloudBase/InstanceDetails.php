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
 *     name="instance_details", uniqueConstraints={@ORM\UniqueConstraint(columns={"instance_id", "options_cloud_instance_id"})}
 *  )
 *
 */
class InstanceDetails
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
    * mapping to options_cloud_instance
    * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Base\Instance", inversedBy="instanceDetails")
    */
    private $instance;
    /**
    * mapping to options_cloud_instance
    * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\CloudBase\OptionsCloudInstance", inversedBy="instanceDetails")
    */
    private $optionsCloudInstance;


    public function __construct(){
        //$this->optionsCloudInstance = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    // MODIF NEW PRICES
    //-----------------
    //-----------------
    /**
     * Set instance
     * @param \AppBundle\Entity\Base\Instance $instance
     * @return InstanceDetails
     */
    public function setInstance(\AppBundle\Entity\Base\Instance $instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * Get instance
     *
     * @return \AppBundle\Entity\Base\Instance
     */
    public function getInstance(){
        return $this->instance;
    }

    /**
     * Set option
     *
     * @param \AppBundle\Entity\CloudBase\OptionsCloudInstance $optionsCloudInstance
     *
     * @return InstanceDetails
     */
    public function setOptionsCloudInstance(\AppBundle\Entity\CloudBase\OptionsCloudInstance $optionsCloudInstance)
    {
        $this->optionsCloudInstance = $optionsCloudInstance;

        return $this;
    }

    /**
     * Get option
     *
     * @return \AppBundle\Entity\CloudBase\OptionsCloudInstance
     */
    public function getOptionsCloudInstance()
    {
        return $this->optionsCloudInstance;
    }
}