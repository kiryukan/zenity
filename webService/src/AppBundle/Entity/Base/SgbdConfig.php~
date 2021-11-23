<?php
// src/AppBundle/Entity/Base/SgbdConfig.php
namespace AppBundle\Entity\Base;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sgbdConfig")
 * Contient les paramètres de configuration du SGBD (tuning)
 */
class SgbdConfig
{
    public function updateFromJson($json)
    {
        $jsonDecoded = json_decode($json,true);
        if (isset($jsonDecoded['OracleConfig'])) {
            $this->oracleConfig = (!$this->oracleConfig) ? new OracleConfig() : $this->oracleConfig;
            $this->oracleConfig->updateFromJson(json_encode($jsonDecoded['OracleConfig']));
        }
    }
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;


    /** le tuning mémoire de la bdd
     * @ORM\OneToOne(targetEntity="OracleConfig",cascade={"persist","remove"})
     */
    private $oracleConfig;

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
     * Set memoryPrm
     *
     * @param \AppBundle\Entity\Base\OracleConfig $memoryPrm
     *
     * @return SgbdConfig
     */
    public function setMemoryPrm(\AppBundle\Entity\Base\OracleConfig $memoryPrm = null)
    {
        $this->memoryPrm = $memoryPrm;

        return $this;
    }

    /**
     * Get memoryPrm
     *
     * @return \AppBundle\Entity\Base\OracleConfig
     */
    public function getMemoryPrm()
    {
        return $this->oracleConfig;
    }

    /**
     * Set oracleConfig
     *
     * @param \AppBundle\Entity\Base\OracleConfig $oracleConfig
     *
     * @return SgbdConfig
     */
    public function setOracleConfig(\AppBundle\Entity\Base\OracleConfig $oracleConfig = null)
    {
        $this->oracleConfig = $oracleConfig;

        return $this;
    }

    /**
     * Get oracleConfig
     *
     * @return \AppBundle\Entity\Base\OracleConfig
     */
    public function getOracleConfig()
    {
        return $this->oracleConfig;
    }
}
