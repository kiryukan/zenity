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
 *     name="providers"
 *  )
 *
 */
class Provider
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * Nom de la base de donnée
     * @ORM\Column(type="string",length=30)
     */
    private $name;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Provider
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
