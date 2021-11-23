<?php
/**
 * Created by PhpStorm.
 * User: simonvivier
 * Date: 24/01/18
 * Time: 10:44
 */

namespace AppBundle\Entity\ComplementaryFlow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tablespace_seg_info")
 */
class TablespaceSegInfo
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
     * @ORM\Column(name = "timestamp",type="datetime",nullable =true)
     */
    private $timestamp;
    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Base\Instance", inversedBy="tablespaceSegInfo",cascade={"persist","remove"}))
     */
    private $instance;
    /**
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\ComplementaryFlow\Segment", mappedBy="tablespaceSegInfo",cascade={"persist","remove"})
     * @ORM\OrderBy({"bytes" = "ASC"})
     */
    private $segments;
    /**
     * @ORM\Column(type="integer")
     */
    private $totalBytes;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->segments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return TablespaceSegInfo
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
     * @return TablespaceSegInfo
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     * @return TablespaceSegInfo
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param mixed $instance
     * @return TablespaceSegInfo
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @param mixed $segments
     * @return TablespaceSegInfo
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalBytes()
    {
        return $this->totalBytes;
    }

    /**
     * @param mixed $totalBytes
     * @return TablespaceSegInfo
     */
    public function setTotalBytes($totalBytes)
    {
        $this->totalBytes = $totalBytes;
        return $this;
    }
    /**
     * Add TableSpaceSegInfo
     *
     * @param \AppBundle\Entity\ComplementaryFlow\Segment $tableSpaceSegInfo
     *
     * @return TablespaceSegInfo
     */
    public function addSegment(\AppBundle\Entity\ComplementaryFlow\Segment $tableSpaceSegInfo)
    {
        $this->segments[] = $tableSpaceSegInfo;
        $tableSpaceSegInfo->setInstance($this);
        return $this;
    }

    /**
     * Remove TableSpaceSegInfo
     *
     * @param \AppBundle\Entity\ComplementaryFlow\Segment $tableSpaceSegInfo
     */
    public function removeSegment(\AppBundle\Entity\ComplementaryFlow\Segment $tableSpaceSegInfo)
    {
        $this->segments->removeElement($tableSpaceSegInfo);
    }

}
