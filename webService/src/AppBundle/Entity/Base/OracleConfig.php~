<?php
// src/AppBundle/Entity/Base/MemoryPRM.php
namespace AppBundle\Entity\Base;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="oracleConfig")
 * Contient les paramètres de tuning de mémoire d'une intance de BDD
 */
class OracleConfig
{

    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $sgaSize;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $bufferCacheSize;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $sharedPoolSize;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $pgaSize;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $largePoolSize;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $javaPoolSize;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $sgaAuto;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $pgaAuto;

    public function updateFromJson($json){
        $jsonDecoded = json_decode($json,true);
        $this->sgaSize = (isset($jsonDecoded['sgaSize']))?$jsonDecoded['sgaSize']:$this->sgaSize;
        $this->bufferCacheSize = (isset($jsonDecoded['bufferCacheSize']))?$jsonDecoded['bufferCacheSize']:$this->bufferCacheSize;
        $this->sharedPoolSize = (isset($jsonDecoded['sharedPoolSize']))?$jsonDecoded['sharedPoolSize']:$this->sharedPoolSize;
        $this->pgaSize = (isset($jsonDecoded['pgaSize']))?$jsonDecoded['pgaSize']:$this->pgaSize;
        $this->largePoolSize = (isset($jsonDecoded['largePoolSize']))?$jsonDecoded['largePoolSize']:$this->largePoolSize;
        $this->javaPoolSize = (isset($jsonDecoded['javaPoolSize']))?$jsonDecoded['javaPoolSize']:$this->javaPoolSize;
        $this->sgaAuto = (isset($jsonDecoded['sgaAuto']))?$jsonDecoded['sgaAuto']:$this->sgaAuto;
        $this->pgaAuto = (isset($jsonDecoded['pgaAuto']))?$jsonDecoded['pgaAuto']:$this->pgaAuto;
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
     * Set sgaSize
     *
     * @param integer $sgaSize
     *
     * @return OracleConfig
     */
    public function setSgaSize($sgaSize)
    {
        $this->sgaSize = $sgaSize;

        return $this;
    }

    /**
     * Get sgaSize
     *
     * @return integer
     */
    public function getSgaSize()
    {
        return $this->sgaSize;
    }

    /**
     * Set bufferCacheSize
     *
     * @param float $bufferCacheSize
     *
     * @return OracleConfig
     */
    public function setBufferCacheSize($bufferCacheSize)
    {
        $this->bufferCacheSize = $bufferCacheSize;

        return $this;
    }

    /**
     * Get bufferCacheSize
     *
     * @return float
     */
    public function getBufferCacheSize()
    {
        return $this->bufferCacheSize;
    }

    /**
     * Set sharedPoolSize
     *
     * @param float $sharedPoolSize
     *
     * @return OracleConfig
     */
    public function setSharedPoolSize($sharedPoolSize)
    {
        $this->sharedPoolSize = $sharedPoolSize;

        return $this;
    }

    /**
     * Get sharedPoolSize
     *
     * @return float
     */
    public function getSharedPoolSize()
    {
        return $this->sharedPoolSize;
    }

    /**
     * Set pgaSize
     *
     * @param float $pgaSize
     *
     * @return OracleConfig
     */
    public function setPgaSize($pgaSize)
    {
        $this->pgaSize = $pgaSize;

        return $this;
    }

    /**
     * Get pgaSize
     *
     * @return float
     */
    public function getPgaSize()
    {
        return $this->pgaSize;
    }

    /**
     * Set largePoolSize
     *
     * @param float $largePoolSize
     *
     * @return OracleConfig
     */
    public function setLargePoolSize($largePoolSize)
    {
        $this->largePoolSize = $largePoolSize;

        return $this;
    }

    /**
     * Get largePoolSize
     *
     * @return float
     */
    public function getLargePoolSize()
    {
        return $this->largePoolSize;
    }

    /**
     * Set javaPoolSize
     *
     * @param float $javaPoolSize
     *
     * @return OracleConfig
     */
    public function setJavaPoolSize($javaPoolSize)
    {
        $this->javaPoolSize = $javaPoolSize;

        return $this;
    }

    /**
     * Get javaPoolSize
     *
     * @return float
     */
    public function getJavaPoolSize()
    {
        return $this->javaPoolSize;
    }

    /**
     * Set sgaAuto
     *
     * @param boolean $sgaAuto
     *
     * @return OracleConfig
     */
    public function setSgaAuto($sgaAuto)
    {
        $this->sgaAuto = $sgaAuto;

        return $this;
    }

    /**
     * Get sgaAuto
     *
     * @return boolean
     */
    public function getSgaAuto()
    {
        return $this->sgaAuto;
    }

    /**
     * Set pgaAuto
     *
     * @param boolean $pgaAuto
     *
     * @return OracleConfig
     */
    public function setPgaAuto($pgaAuto)
    {
        $this->pgaAuto = $pgaAuto;

        return $this;
    }

    /**
     * Get pgaAuto
     *
     * @return boolean
     */
    public function getPgaAuto()
    {
        return $this->pgaAuto;
    }
}
