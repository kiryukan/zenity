<?php
namespace AppBundle\Entity\Metadata;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="event_metadata",
 *     indexes={@ORM\Index(name="name_idx", columns={"wait_class"})})
 */
class EventMetadata
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
    private $wait_class;
    /**
     * @ORM\Column(type="bigint")
     **/
    private $wait_class_id;

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
     * @return EventMetadata
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
     * @return EventMetadata
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
     * Set waitClass
     *
     * @param string $waitClass
     *
     * @return EventMetadata
     */
    public function setWaitClass($waitClass)
    {
        $this->wait_class = $waitClass;

        return $this;
    }

    /**
     * Get waitClass
     *
     * @return string
     */
    public function getWaitClass()
    {
        return $this->wait_class;
    }

    /**
     * Set waitClassId
     *
     * @param integer $waitClassId
     *
     * @return EventMetadata
     */
    public function setWaitClassId($waitClassId)
    {
        $this->wait_class_id = $waitClassId;

        return $this;
    }

    /**
     * Get waitClassId
     *
     * @return integer
     */
    public function getWaitClassId()
    {
        return $this->wait_class_id;
    }
    /**
     * Set id
     *
     * @param integer $id
     *
     * @return EventMetadata
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
