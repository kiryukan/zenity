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
 *     name="category_edition"
 *  )
 *
 */
class CategoryEdition
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * Nom du type
     * @ORM\Column(type="string",length=30)
     */
    private $name;
    /*
    * mapping to price
    * @ORM\OneToMany(targetEntity="\AppBundle\Entity\CloudBase\Price", mappedBy="categoryEdition")
    */
    private $prices;
    /*
    * mapping to edition
    * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\CloudBase\Edition", mappedBy="categoriesEdition")
    */
    private $packEdition;


    public function __construct(){
        $this->editions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->prices = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return CategoryEdition
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
}