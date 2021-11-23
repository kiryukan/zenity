<?php
// src/AppBundle/Entity/Base/ServerConfig.php
namespace AppBundle\Entity\Base;
use AppBundle\Entity\IJsonUpdatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="serverConfig")
 * Contient les caractéristique physique et OS d'un server
 */
class ServerConfig
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;


    /**
     * @ORM\Column(type="string",length=25,nullable=true)
     * L'OS qui fais tourner l'instance
     */
    private $os;

    /**
     * @ORM\Column(type="integer",nullable=true)
     * le nombre de CPU de la machine
     */
    private $nbCpu;
    /**
     * @ORM\Column(type="integer",nullable=true)
     * le nombre de Cores de la machine
     */
    private $nbCores;
    /**
     * @ORM\Column(type="integer",nullable=true)
     * le nombre de Sockets de la machine
     */
    private $nbSockets;
    /**
    * @ORM\Column(type="integer",nullable=true)
    */
    private $instanceStorageCapacity;
    public function updateFromJson($json)
    {
        $jsonDecoded = json_decode($json,true);
        $this->os = (isset($jsonDecoded['os']))?$jsonDecoded['os']:$this->os;
        $this->nbCpu = (isset($jsonDecoded['nbCpu']))?$jsonDecoded['nbCpu']:$this->nbCpu;
        $this->nbCores = (isset($jsonDecoded['nbCores']))?$jsonDecoded['nbCores']:$this->nbCores;
        $this->nbSockets = (isset($jsonDecoded['nbSockets']))?$jsonDecoded['nbSockets']:$this->nbSockets;
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
     * Set os
     *
     * @param string $os
     *
     * @return ServerConfig
     */
    public function setOs($os)
    {
        $this->os = $os;

        return $this;
    }

    /**
     * Get os
     *
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * Set nbCpu
     *
     * @param integer $nbCpu
     *
     * @return ServerConfig
     */
    public function setNbCpu($nbCpu)
    {
        $this->nbCpu = $nbCpu;

        return $this;
    }

    /**
     * Get nbCpu
     *
     * @return integer
     */
    public function getNbCpu()
    {
        return $this->nbCpu;
    }

    /**
     * Set nbCores
     *
     * @param integer $nbCores
     *
     * @return ServerConfig
     */
    public function setNbCores($nbCores)
    {
        $this->nbCores = $nbCores;

        return $this;
    }

    /**
     * Get nbCores
     *
     * @return integer
     */
    public function getNbCores()
    {
        return $this->nbCores;
    }

    /**
     * Set nbSockets
     *
     * @param integer $nbSockets
     *
     * @return ServerConfig
     */
    public function setNbSockets($nbSockets)
    {
        $this->nbSockets = $nbSockets;

        return $this;
    }

    /**
     * Get nbSockets
     *
     * @return integer
     */
    public function getNbSockets()
    {
        return $this->nbSockets;
    }

    /**
     * Set hist
     *
     * @param string $hist
     *
     * @return ServerConfig
     */
    public function setHist($hist)
    {
        $this->hist = $hist;

        return $this;
    }

    /**
     * Get hist
     *
     * @return string
     */
    public function getHist()
    {
        return $this->hist;
    }

    /**
     * Set instanceStorageCapacity
     *
     * @param integer $instanceStorageCapacity
     *
     * @return ServerConfig
     */
    public function setInstanceStorageCapacity($instanceStorageCapacity)
    {
        $this->instanceStorageCapacity = $instanceStorageCapacity;

        return $this;
    }

    /**
     * Get instanceStorageCapacity
     *
     * @return integer
     */
    public function getInstanceStorageCapacity()
    {
        return $this->instanceStorageCapacity;
    }
}
