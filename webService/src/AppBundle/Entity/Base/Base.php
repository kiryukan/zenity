<?php
// src/AppBundle/Entity/Base/Base.php
namespace AppBundle\Entity\Base;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Knp\JsonSchemaBundle\Annotations as Json;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="base",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"name","client_id"})})
 * )
 * Description d'une BDD avec son nom, son SGBD
 * contient l'ensemble de ses @link Instance
 */

class Base
{


    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /** Nom de la base de donnée
     * @ORM\Column(type="string",length=30)
     *
     */
    private $name;

    /** Gestionnaire de base de donnée( ORACLE,PGSQL ...)
     * @ORM\Column(type="string",length=30)
     */
    private $sgbd;

    /** N° de version du SGBD
     * @ORM\Column(type="string",length=30)
     */
    private $version;

    /**
     * @ORM\OneToMany(targetEntity="Instance", mappedBy="base",cascade={"persist","remove"})
     */
    private $instances;

    /**
     * La @link Client qui possède la base
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Client", inversedBy="bases")
     */
    private $client;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->instances = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function updateFromJson($json)
    {
        $jsonDecoded = json_decode($json,true);
        $this->name = (isset($jsonDecoded['name']))?$jsonDecoded['name']:$this->name;
        $this->sgbd = (isset($jsonDecoded['sgbd']))?$jsonDecoded['sgbd']:$this->sgbd;
        $this->version = (isset($jsonDecoded['version']))?$jsonDecoded['version']:$this->version;
        $this->instances = ($this->instances ) ? new \Doctrine\Common\Collections\ArrayCollection(): $this->instances;
        if (isset($jsonDecoded['Instance'])) {
            foreach ($jsonDecoded['Instance'] as $jsonDecodedInstance) {
                $instance = new Instance();
                $instance->updateFromJson(json_encode($jsonDecodedInstance));
                $this->addInstance($instance);
            }
        }
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
     * @return Base
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
     * Set sgbd
     *
     * @param string $sgbd
     *
     * @return Base
     */
    public function setSgbd($sgbd)
    {
        $this->sgbd = $sgbd;

        return $this;
    }

    /**
     * Get sgbd
     *
     * @return string
     */
    public function getSgbd()
    {
        return $this->sgbd;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return Base
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }


    /**
     * Add instance
     *
     * @param \AppBundle\Entity\Base\Instance $instance
     *
     * @return Base
     */
    public function addInstance(\AppBundle\Entity\Base\Instance $instance)
    {
        $this->instances[] = $instance;
        $instance->setBase($this);
        return $this;
    }

    /**
     * Remove instance
     *
     * @param \AppBundle\Entity\Base\Instance $instance
     */
    public function removeInstance(\AppBundle\Entity\Base\Instance $instance)
    {
        $this->instances->removeElement($instance);
        $instance->setBase(null);
    }

    /**
     * Get instances
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInstances()
    {
        return $this->instances;
    }
    /**
     * Get instance
     *
     * @return Instance
     */
    public function getInstanceByNameAndServerName($name,$serverName)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria
            ->where($expr->eq('name', $name))
            ->andWhere($expr->eq('serverName', $serverName));
        return $this->instances->matching($criteria)[0];
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $base
     *
     * @return Base
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get base
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
