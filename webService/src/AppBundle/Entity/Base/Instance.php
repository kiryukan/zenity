<?php
// src/AppBundle/Entity/Base/Instance.php
namespace AppBundle\Entity\Base;
use AppBundle\Entity\Snapshots\Snapshot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Translation\Interval;


/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="instance",uniqueConstraints={@ORM\UniqueConstraint(columns={"base_id", "name","server_name"})}
 *  )
 *
 * Une instance de BDD, ne contient aucune information, juste des liens vers d'autre tables:
 * les configurations du serveur :
 *          @link OracleConfig
 *          @link ServerConfig
 * l'ensemble de ses snapshots (image a un instant T)
 *          @link Ressources
 * unicité par rapport au nom et a la database mère
 */
class Instance
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /**
     * La @link DataBase mère de l'instance
     * @ORM\ManyToOne(targetEntity="Base", inversedBy="instances")
     */
    private $base;

    /**
     * La configuration du server physique stocker dans @link ServerConfig
     * @ORM\OneToOne(targetEntity="ServerConfig",cascade={"persist","remove"})
     */
    private $serverConfig;

    /** La configuration des paramètres du SGBD (Tuning)
     * @ORM\OneToOne(targetEntity="SgbdConfig",cascade={"persist","remove"})
     */
    private $sgbdConfig;
    /** Les conseils Oracle sur les zones mémoire
     * @ORM\OneToMany(targetEntity="Advisory", mappedBy="instance",cascade={"persist","remove"})
     */
    private $advisory;
    /** Les conseils Oracle sur les zones mémoire
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\ComplementaryFlow\AlertLog", mappedBy="instance",cascade={"persist","remove"})
     */
    private $alertLogs;
    /** Le statut de perfStat sur cette instance
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\ComplementaryFlow\PerfStatInfo",cascade={"persist","remove"})
     */
    private $perfStatInfo;

    /**
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Snapshots\Snapshot", mappedBy="instance",cascade={"persist","remove"})
     * @ORM\OrderBy({"startDate" = "ASC"})
     */
    private $snapshots;
    /**
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\ComplementaryFlow\TablespaceSegInfo", mappedBy="instance",cascade={"persist","remove"})
     * @ORM\OrderBy({"timestamp" = "ASC"})
     */
    private $tablespaceSegInfo;
    /**
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\ComplementaryFlow\Rman", mappedBy="instance",cascade={"persist","remove"})
     */
    private $rman;
    ///**
    // * @ORM\OneToOne(targetEntity="\AppBundle\Entity\ComplementaryFlow\Options", cascade={"persist","remove"})
    // */
    //private $options;
    /**
     * Nom de la base de donnée
     * @ORM\Column(type="string",length=30)
     */
    private $name;

    /**
     * Nom du serveur sur lequel tourne l'instance
     * @ORM\Column(type="string",length=30)
     */
    private $serverName;

    /**
    * mapping to instance_details
    * @ORM\OneToMany(targetEntity="\AppBundle\Entity\CloudBase\InstanceDetails", mappedBy="instance")
    */
    private $instanceDetails;

    public function updateFromJson($json)
    {
        $jsonDecoded = json_decode($json,true);
        $this->name = (isset($jsonDecoded['name']))?$jsonDecoded['name']:$this->name;
        $this->serverName = (isset($jsonDecoded['serverName']))?$jsonDecoded['serverName']:$this->serverName;
        if (isset($jsonDecoded['ServerConfig'])) {
            $this->serverConfig = (!$this->serverConfig) ? new ServerConfig() : $this->serverConfig;
            $this->serverConfig->updateFromJson(json_encode($jsonDecoded['ServerConfig']));
        }
        if (isset($jsonDecoded['SgbdConfig'])) {
            $this->sgbdConfig = (!$this->sgbdConfig) ? new SgbdConfig() : $this->sgbdConfig;
            $this->sgbdConfig->updateFromJson(json_encode($jsonDecoded['SgbdConfig']));
        }
        if (isset($jsonDecoded['Advisory'])) {
            foreach ($jsonDecoded['Advisory'] as $jsonAdvisory ) {
                $criteria = Criteria::create();
                $expr = Criteria::expr();
                $criteria->where($expr->eq('name', $jsonAdvisory['name']));
                $advisory = $this->advisory->matching($criteria)[0];
                if($advisory){
                    $advisory->updateFromJson(json_encode($jsonAdvisory));
                }else{
                    $advisory =  new Advisory();
                    $advisory->updateFromJson(json_encode($jsonAdvisory));
                    $this->addAdvisory($advisory);
                }
            }
        }
        return $this;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->snapshots = new \Doctrine\Common\Collections\ArrayCollection();
        $this->advisory = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tablespaceSegInfo = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instanceDetails = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Instance
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
     * Set base
     *
     * @param \AppBundle\Entity\Base\Base $base
     *
     * @return Instance
     */
    public function setBase(\AppBundle\Entity\Base\Base $base = null)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Get base
     *
     * @return \AppBundle\Entity\Base\Base
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set serverConfig
     *
     * @param \AppBundle\Entity\Base\ServerConfig $serverConfig
     *
     * @return Instance
     */
    public function setServerConfig(\AppBundle\Entity\Base\ServerConfig $serverConfig = null)
    {
        $this->serverConfig = $serverConfig;
        return $this;
    }

    /**
     * Get serverConfig
     *
     * @return \AppBundle\Entity\Base\ServerConfig
     */
    public function getServerConfig()
    {
        return $this->serverConfig;
    }

    /**
     * Set sgbdConfig
     *
     * @param \AppBundle\Entity\Base\SgbdConfig $sgbdConfig
     *
     * @return Instance
     */
    public function setSgbdConfig(\AppBundle\Entity\Base\SgbdConfig $sgbdConfig = null)
    {
        $this->sgbdConfig = $sgbdConfig;
        return $this;
    }

    /**
     * Get sgbdConfig
     *
     * @return \AppBundle\Entity\Base\SgbdConfig
     */
    public function getSgbdConfig()
    {
        return $this->sgbdConfig;
    }

    /**
     * Add snapshot
     *
     * @param \AppBundle\Entity\Snapshots\Snapshot $snapshot
     *
     * @return Instance
     */
    public function addSnapshot(\AppBundle\Entity\Snapshots\Snapshot $snapshot)
    {
        $this->snapshots[] = $snapshot;
        $snapshot->setInstance($this);
        return $this;
    }

    /**
     * Remove snapshot
     *
     * @param \AppBundle\Entity\Snapshots\Snapshot $snapshot
     */
    public function removeSnapshot(\AppBundle\Entity\Snapshots\Snapshot $snapshot)
    {
        $snapshot->setInstance(null);
        $this->snapshots->removeElement($snapshot);
    }

    /**
     * Get snapshots
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSnapshots()
    {
        return $this->snapshots;
    }

    /**
     * @param $from
     * @param $to
     * @return ArrayCollection
     */
    public function getSnapshotsBetween($from,$to){
        $interval = new \DateInterval("PT5M");
//        $interval->i = 5;
        $from = date_sub($from,$interval);
        $to = date_add($to,$interval);
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->gte('startDate', $from));
        $criteria->andWhere($expr->lte('endDate', $to));
        $criteria->orderBy(['startDate'=>Criteria::ASC]);
        return $this->snapshots->matching($criteria);
    }
    /**
     * @return \DateTime
     */
    public function getFirstSnapshotDate(){
        $criteria = Criteria::create();
        $criteria->orderBy(['startDate'=>Criteria::ASC]);
        $criteria->getFirstResult();
        /* @var $snapshot Snapshot */
        $snapshot = $this->snapshots->matching($criteria)[0];
        if($snapshot){
            return $snapshot->getStartDate();
        }else {
            return null;
        }
    }
    /**
     * @return \DateTime
     */
    public function getLastSnapshotDate(){
        $criteria = Criteria::create();
        $criteria->orderBy(['endDate'=>Criteria::DESC]);
        $snapshots = $this->snapshots->matching($criteria);
        /* @var $snapshot Snapshot */
        $snapshot = $snapshots[0];
        if($snapshot){
            return $snapshot->getEndDate();
        }else {
            return null;
        }
    }
    /**
     * @param $from \DateTime
     * @return Snapshot
     */
    public function getSnapshotsForStartDate($from){
        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where($expr->gte('startDate', $from));
        $criteria->getFirstResult();
        return $this->snapshots->matching($criteria)[0];
    }
    /**
     * Set serverName
     *
     * @param string $serverName
     *
     * @return Instance
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;

        return $this;
    }

    /**
     * Get serverName
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * @return mixed
     */
    public function getPerfStatInfo()
    {
        return $this->perfStatInfo;
    }

    /**
     * @param mixed $perfStatInfo
     * @return Instance
     */
    public function setPerfStatInfo($perfStatInfo)
    {
        $this->perfStatInfo = $perfStatInfo;
        return $this;
    }

    /**
     * Add advisory
     *
     * @param \AppBundle\Entity\Base\Advisory $advisory
     *
     * @return Instance
     */
    public function addAdvisory(\AppBundle\Entity\Base\Advisory $advisory)
    {
        $this->advisory[] = $advisory;
        $advisory->setInstance($this);
        return $this;
    }

    /**
     * Remove advisory
     *
     * @param \AppBundle\Entity\Base\Advisory $advisory
     */
    public function removeAdvisory(\AppBundle\Entity\Base\Advisory $advisory)
    {
        $this->advisory->removeElement($advisory);
    }

    /**
     * Get advisory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdvisory()
    {
        return $this->advisory;
    }

    /**
     * Add TableSpaceSegInfo
     *
     * @param \AppBundle\Entity\ComplementaryFlow\TablespaceSegInfo $tableSpaceSegInfo
     *
     * @return Instance
     */
    public function addTableSpaceSegInfo(\AppBundle\Entity\ComplementaryFlow\TablespaceSegInfo $tableSpaceSegInfo)
    {
        $this->advisory[] = $tableSpaceSegInfo;
        $tableSpaceSegInfo->setInstance($this);
        return $this;
    }


    /**
     * Add alertLog
     *
     * @param \AppBundle\Entity\ComplementaryFlow\AlertLog $alertLog
     *
     * @return Instance
     */
    public function addAlertLog(\AppBundle\Entity\ComplementaryFlow\AlertLog $alertLog)
    {
        $this->alertLogs[] = $alertLog;

        return $this;
    }

    /**
     * Remove alertLog
     *
     * @param \AppBundle\Entity\ComplementaryFlow\AlertLog $alertLog
     */
    public function removeAlertLog(\AppBundle\Entity\ComplementaryFlow\AlertLog $alertLog)
    {
        $this->alertLogs->removeElement($alertLog);
    }

    /**
     * Get alertLogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlertLogs()
    {
        return $this->alertLogs;
    }

    /**
     * Remove tablespaceSegInfo
     *
     * @param \AppBundle\Entity\ComplementaryFlow\TablespaceSegInfo $tablespaceSegInfo
     */
    public function removeTablespaceSegInfo(\AppBundle\Entity\ComplementaryFlow\TablespaceSegInfo $tablespaceSegInfo)
    {
        $this->tablespaceSegInfo->removeElement($tablespaceSegInfo);
    }

    /**
     * Get tablespaceSegInfo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTablespaceSegInfo()
    {
        return $this->tablespaceSegInfo;
    }


    /**
     * Add rman
     *
     * @param \AppBundle\Entity\ComplementaryFlow\Rman $rman
     *
     * @return Instance
     */
    public function addRman(\AppBundle\Entity\ComplementaryFlow\Rman $rman)
    {
        $this->rman[] = $rman;

        return $this;
    }

    /**
     * Remove rman
     *
     * @param \AppBundle\Entity\ComplementaryFlow\Rman $rman
     */
    public function removeRman(\AppBundle\Entity\ComplementaryFlow\Rman $rman)
    {
        $this->rman->removeElement($rman);
    }

    /**
     * Get rman
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRman()
    {
        return $this->rman;
    }

    ///**
    // * Add option
    // *
    // * @param \AppBundle\Entity\ComplementaryFlow\Options $option
    // *
    // * @return Instance
    // */
    /*public function addOption(\AppBundle\Entity\ComplementaryFlow\Options $option)
    {
        $this->options[] = $option;

        return $this;
    }*/

    ///**
    // * Remove option
    // *
    // * @param \AppBundle\Entity\ComplementaryFlow\Options $option
    // */
    /*public function removeOption(\AppBundle\Entity\ComplementaryFlow\Options $option)
    {
        $this->options->removeElement($option);
    }*/

    ///**
    // * Get options
    // *
    // * @return \Doctrine\Common\Collections\Collection
    // */
    /*public function getOptions()
    {
        return $this->options;
    }*/

    ///**
    // * Set options
    // *
    // * @param \AppBundle\Entity\ComplementaryFlow\Options $options
    // *
    // * @return Instance
    // */
    /*public function setOptions(\AppBundle\Entity\ComplementaryFlow\Options $options = null)
    {
        $this->options = $options;

        return $this;
    }*/

    /* ------------------------------------------ */
    /* TO REMOVE IF MOVED IN INSTANCE CLOUD TABLE */
    /* ------------------------------------------ */

    // MODIF NEW PRICES
    //-----------------
    //-----------------
    /**
     * Add instance_details
     *
     * @param \AppBundle\Entity\CloudBase\InstanceDetails $instanceDetails
     *
     * @return Instance
     */
    public function setInstanceDetails(\AppBundle\Entity\CloudBase\InstanceDetails $instanceDetails)
    {
        $instanceDetails->setInstance($this);
        $this->instanceDetails[] = $instanceDetails;
        return $this;
    }

    /**
     * Remove instance_details
     *
     * @param \AppBundle\Entity\CloudBase\InstanceDetails $instanceDetails
     */
    public function removeInstanceDetails(\AppBundle\Entity\CloudBase\InstanceDetails $instanceDetails)
    {
        $instanceDetails->setInstance(null);
        $this->instanceDetails->removeElement($instanceDetails);
    }

    /**
     * Get instance_details
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInstanceDetails()
    {
        return $this->instanceDetails;
    }
}
