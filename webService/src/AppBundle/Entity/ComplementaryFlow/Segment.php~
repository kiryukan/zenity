<?php
/**
 * Created by PhpStorm.
 * User: simonvivier
 * Date: 24/01/18
 * Time: 10:22
 */

namespace AppBundle\Entity\ComplementaryFlow;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="segment")
 */
class Segment
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $name;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $type;
    /**
     * @ORM\Column(type="string",length=50,nullable =true)
     */
    private $owner;
    /**
     * @ORM\Column(type="integer")
     */
    private $bytes;
    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\ComplementaryFlow\TablespaceSegInfo", inversedBy="segments"))
     */
    private $tablespaceSegInfo;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Segment
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Segment
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Segment
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     * @return Segment
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBytes()
    {
        return $this->bytes;
    }

    /**
     * @param mixed $bytes
     * @return Segment
     */
    public function setBytes($bytes)
    {
        $this->bytes = $bytes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTablespaceSegInfo()
    {
        return $this->tablespaceSegInfo;
    }

    /**
     * @param mixed $tablespaceSegInfo
     * @return Segment
     */
    public function setTablespaceSegInfo($tablespaceSegInfo)
    {
        $this->tablespaceSegInfo = $tablespaceSegInfo;
        return $this;
    }

}
