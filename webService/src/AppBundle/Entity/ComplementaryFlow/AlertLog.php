<?php
/**
 * Created by PhpStorm.
 * User: simonvivier
 * Date: 17/01/18
 * Time: 11:15
 */

namespace AppBundle\Entity\ComplementaryFlow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="alertLog",uniqueConstraints={@ORM\UniqueConstraint(columns={"date", "code"})}
 * )
 *
 * Contient le contenu des alertLog
 */

class AlertLog
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(name = "date",type="datetime")
     */
    private $date;
    /**
     * @ORM\Column(type="string",length=30,nullable =true)
     */
    private $text;
    /**
     * @ORM\Column(type="string",length=30,nullable =true)
     */
    private $code;
    /** nécessaire pour un OneToMany dans @link Ressources
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Base\Instance", inversedBy="alertLogs",cascade={"persist","remove"}))
     */
    private $instance;
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AlertLog
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return AlertLog
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return AlertLog
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return AlertLog
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }


    /**
     * Set instance
     *
     * @param \AppBundle\Entity\Base\Instance $instance
     *
     * @return AlertLog
     */
    public function setInstance(\AppBundle\Entity\Base\Instance $instance = null)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get instance
     *
     * @return \AppBundle\Entity\Base\Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }
}
