<?php


namespace AppBundle\Entity\Metadata;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="stat_metadata",
 *     indexes={@ORM\Index(name="name_idx", columns={"name"})})
 */
class StatMetadata
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string",length=80)
     **/
    private $name;
    /**
     * @ORM\Column(type="text",nullable=true)
     **/
    private $description;
    /**
     * @ORM\Column(type="string",length=80)
     **/
    private $className;

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return StatMetadata
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @return StatMetadata
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
     * Set description
     *
     * @param string $description
     *
     * @return StatMetadata
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set className
     *
     * @param string $className
     *
     * @return StatMetadata
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get className
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
