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
 *     name="tarifs"
 *  )
 *
 */
class Tarif
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * Type d edition
     * @ORM\Column(type="string",length=15)
     */
    private $typeName;
    /**
     * if is byol or not
     * @ORM\Column(type="boolean")
     */
    private $byol;
    /**
     * Define price by hour or by month
     * @ORM\Column(type="string",length=15)
     */
    private $paygOrFlex;
    /**
     * Price for first OCPU
     * @ORM\Column(type="float")
     */
    private $ocpu1;
    /**
     * Price for two first OCPU
     * @ORM\Column(type="float")
     */
    private $ocpu2;
    /**
     * Price for more than 2 OCPU
     * @ORM\Column(type="float")
     */
    private $MoreThan2_ocpu;
    /**
     * storage included
     * @ORM\Column(type="integer")
     */
    private $storage;
    /**
    * mapping to pack_edition
    * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\CloudBase\PackEdition", inversedBy="tarifs")
    */
    private $packEdition;
    /**
    * mapping to cloud instance
    * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\CloudBase\CloudInstance", inversedBy="tarifs")
    */
    private $cloudInstance;
    /**
     * Comment to specify
     * @ORM\Column(type="text")
     */
    private $additionalInformations;

    /**
     * Get id
     * @return integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Set typeName
     * @param string $type
     * @return Tarif
     */
    public function setTypeName($typeName){
        $this->typeName = $typeName;
        return $this;
    }
    /**
     * Get typeName
     * @return string
     */
    public function getTypeName(){
        return $this->typeName;
    }
    /**
     * Set byol
     * @param boolean $byol
     * @return Tarif
     */
    public function setByol($byol){
        $this->byol = $byol;
        return $this;
    }
    /**
     * Get byol
     * @return boolean
     */
    public function getByol(){
        return $this->byol;
    }
    /**
     * Set paygOrFlex
     * @param string $paygOrFlex
     * @return Tarif
     */
    public function setPaygOrFlex($paygOrFlex){
        $this->paygOrFlex = $paygOrFlex;
        return $this;
    }
    /**
     * Get paygOrFlex
     * @return string
     */
    public function getPaygOrFlex(){
        return $this->paygOrFlex;
    }
    /**
     * Set ocpu1
     * @param boolean $ocpu1
     * @return Tarif
     */
    public function setOcpu1($ocpu1){
        $this->ocpu1 = $ocpu1;
        return $this;
    }
    /**
     * Get ocpu1
     * @return float
     */
    public function getOcpu1(){
        return $this->ocpu1;
    }
    /**
     * Set ocpu2
     * @param boolean $ocpu2
     * @return Tarif
     */
    public function setOcpu2($ocpu2){
        $this->ocpu2 = $ocpu2;
        return $this;
    }
    /**
     * Get ocpu2
     * @return float
     */
    public function getOcpu2(){
        return $this->ocpu2;
    }
    /**
     * Set moreThan2_ocpu
     * @param float $moreThan2_ocpu
     * @return Tarif
     */
    public function setMoreThan2_ocpu($moreThan2_ocpu){
        $this->moreThan2_ocpu = $moreThan2_ocpu;
        return $this;
    }
    /**
     * Get moreThan2_ocpu
     * @return float
     */
    public function getMoreThan2_ocpu(){
        return $this->moreThan2_ocpu;
    }
    /**
     * Set storage
     * @param float $storage
     * @return Tarif
     */
    public function setStorage($storage){
        $this->storage = $storage;
        return $this;
    }
    /**
     * Get storage
     * @return float
     */
    public function getStorage(){
        return $this->storage;
    }

    /**
     * Set pack edition
     * @param \AppBundle\Entity\CloudBase\PackEdition $packEdition
     * @return Tarif
     */
    public function setPackEdition(\AppBundle\Entity\CloudBase\PackEdition $packEdition){
        $this->packEdition = $packEdition;
        return $this;
    }

    /**
     * Get pack edition
     * @return PackEdition
     */
    public function getPackEdition(){
        return $this->packEdition;
    }

    /**
     * Set cloud instance
     * @param \AppBundle\Entity\CloudBase\CloudInstance $cloudInstance
     * @return Tarif
     */
    public function setCloudInstance(\AppBundle\Entity\CloudBase\CloudInstance $cloudInstance){
        $this->cloudInstance = $cloudInstance;
        return $this;
    }

    /**
     * Get cloud instance
     * @return CloudInstance
     */
    public function getCloudInstance(){
        return $this->cloudInstance;
    }

    /**
     * Set additional informations
     * @param string $additionalInformations
     * @return Tarif
     */
    public function setAdditionalInformations($additionalInformations){
        $this->additionalInformations = $additionalInformations;
        return $this;
    }
    /**
     * Get additional informations
     * @return string
     */
    public function getAdditionalInformations(){
        return $this->additionalInformations;
    }
}
