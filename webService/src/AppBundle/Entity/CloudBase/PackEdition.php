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
 *     name="pack_edition"
 *  )
 *
 */
class PackEdition
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * Nom du type
     * @ORM\Column(type="string",length=10)
     */
    private $edition;
    /**
     * Nom du type
     * @ORM\Column(type="string",length=100)
     */
    private $name;
    /**
    * mapping to OptionsCloudInstance
    * @ORM\OneToMany(targetEntity="\AppBundle\Entity\CloudBase\OptionsCloudInstance", mappedBy="packEdition")
    */
    private $optionsCloudInstanceArray;
    /**
    * mapping to prices
    * @ORM\OneToMany(targetEntity="\AppBundle\Entity\CloudBase\Tarif", mappedBy="packEdition")
    */
    private $tarifs;


    /**
     * Constructor
     */
    public function __construct()
    {
        //$this->categoriesEdition = new \Doctrine\Common\Collections\ArrayCollection();
        $this->optionsCloudInstanceArray = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set edition
     *
     * @param string $edition
     *
     * @return PackEdition
     */
    public function setEdition($edition)
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PackEdition
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


    /*public function addCategoryEdition(\AppBundle\Entity\CloudBase\CategoryEdition $categoryEdition){
        $categoryEdition->addEdition($this);
        $this->categoriesEdition[] = $categoryEdition;
    }

    public function getCategoryEdition(){
        return $this->categoriesEdition;
    }*/

    /**
     * Add option
     *
     * @param \AppBundle\Entity\CloudBase\OptionCloudInstance $option
     *
     * @return Edition
     */
    public function addOption(\AppBundle\Entity\CloudBase\OptionsCloudInstance $option)
    {
        $option->addEdition($this);
        $this->options[] = $option;
        return $this;
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOptions()
    {
        return $this->options;
    }

    // MODIF NEW PRICES
    //-----------------
    //-----------------
    /**
     * Add options_cloud_instance
     *
     * @param \AppBundle\Entity\CloudBase\InstanceDetails $instanceDetails
     *
     * @return OptionsCloudInstance
     */
    public function addOptionsCloudInstance(\AppBundle\Entity\CloudBase\OptionsCloudInstance $optionsCloudInstance)
    {
        $optionsCloudInstance->setPackEdition($this);
        $this->optionsCloudInstanceArray[] = $optionsCloudInstance;
        return $this;
    }

    /**
     * Remove options_cloud_instance
     *
     * @param \AppBundle\Entity\CloudBase\OptionsCloudInstance $optionsCloudInstance
     */
    public function removeOptionsCloudInstance(\AppBundle\Entity\CloudBase\OptionsCloudInstance $optionsCloudInstance)
    {
        $optionsCloudInstance->setPackEdition(null);
        $this->optionsCloudInstanceArray->removeElement($optionsCloudInstance);
    }

    /**
     * Get options_cloud_instance
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOptionsCloudInstance()
    {
        return $this->optionsCloudInstanceArray;
    }

    /**
     * Add tarif
     *
     * @param \AppBundle\Entity\CloudBase\Tarif $tarif
     *
     * @return PackEdition
     */
    public function addTarif(\AppBundle\Entity\CloudBase\Tarif $tarif)
    {
        $tarif->setPackEdition($this);
        $this->tarifs[] = $tarif;
        return $this;
    }

    /**
     * Remove tarif
     *
     * @param \AppBundle\Entity\CloudBase\Tarif $tarif
     */
    public function removeTarif(\AppBundle\Entity\CloudBase\Tarif $tarif)
    {
        $tarif->setPackEdition(null);
        $this->tarifs->removeElement($tarif);
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