<?php
namespace AppBundle\Entity\ComplementaryFlow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="RmanParameters")
 */

class RmanParameters
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /** nécessaire pour un OneToMany dans @link Rman
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\ComplementaryFlow\Rman", inversedBy="parameters",cascade={"persist","remove"}))
     */
    private $rman;
    /**
     * @ORM\Column(type="string",length=30)
     */
    private $name;
    /**
     * @ORM\Column(type="string",length=30)
     */
    private $value;


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
     * @return RmanParameters
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
     * @param string $value
     *
     * @return RmanParameters
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set rman
     *
     * @param \AppBundle\Entity\ComplementaryFlow\Rman $rman
     *
     * @return RmanParameters
     */
    public function setRman(\AppBundle\Entity\ComplementaryFlow\Rman $rman = null)
    {
        $this->rman = $rman;

        return $this;
    }

    /**
     * Get rman
     *
     * @return \AppBundle\Entity\ComplementaryFlow\Rman
     */
    public function getRman()
    {
        return $this->rman;
    }
}
