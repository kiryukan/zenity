<?php
// src/AppBundle/Entity/Client.php
namespace AppBundle\Entity;
use AppBundle\Entity\Base\Base;
use AppBundle\Entity\Base\Instance;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * @ORM\Entity
 * @ORM\Table(name="client")
 * Un client avec son nom
 * contient l'ensemble de ses @link Bases
 */
class Client
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="datetime",nullable=true)
     * la date d'inscription du client
     */
    private $registrationDate;

    /** Nom de la base de donnée
     * @ORM\Column(type="string",length=30,unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Base\Base", mappedBy="client",cascade={"persist","remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $bases;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bases = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Count the number of databases owned by the client
     *
     */
    public function countBases(){
        return $this->getBases()->count();
    }
    /**
     * Count the number of databases owned by the client
     *
     */
    public function countSnapshot(){
        $count = 0;
        /* @var $base Base */
        foreach ($this->getBases() as $base){
            /* @var $instance Instance*/
            foreach ($base->getInstances() as $instance){
                $count+= $instance->getSnapshots()->count();
            }
        }
        return $count;
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
     * @return Client
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
     * Add Base
     *
     * @param \AppBundle\Entity\Base\Instance $instance
     *
     * @return Client
     */
    public function addBase(\AppBundle\Entity\Base\Base $base)
    {
        $this->bases[] = $base;
        $base->setClient($this);
        return $this;
    }

    /**
     *
     *
     * @param \AppBundle\Entity\Base\Instance $instance
     */
    public function removeBase(\AppBundle\Entity\Base\Base $base)
    {
        $this->bases->removeElement($base);
        $base->setClient(null);
    }


    /**
     * Get bases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBases()
    {
        return $this->bases;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     *
     * @return Client
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }
}
