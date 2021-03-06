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
 *     name="prices"
 *  )
 *
 */
class Price
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
    * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\CloudBase\CloudInstance", inversedBy="prices",cascade={"persist"})
    */
    private $cloudInstance;

    /**
     * Nom de la base de donnée
     * @ORM\Column(type="string",length=30)
     */
    private $name;
    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * Nom de l edition
     * @ORM\Column(type="string",length=40)
     */
    private $edition;


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
     * @return Price
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
     * Set value
     *
     * @param integer $value
     *
     * @return Price
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
    * @param AppBundle\Entity\CloudBase\CloudInstance $cloudInstance
     * @return AppBundle\Entity\CloudBase\CloudInstance
     */
    public function setCloudInstance($cloudInstance)
    {
        $this->cloudInstance = $cloudInstance;
    }
    /**
     *
     * @return AppBundle\Entity\CloudBase\CloudInstance
     */
    public function getCloudInstance()
    {
        return $this->cloudInstance;
    }

    /**
     * Set edition name
     *
     * @param string $edition
     *
     * @return Price
     */
    public function setEdition($edition)
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition name
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->edition;
    }
}
