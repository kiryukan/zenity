<?php
// src/AppBundle/Entity/ComplementaryFlow/Options.php
namespace AppBundle\Entity\ComplementaryFlow;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="options")
 */
class Options
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(name="product_usage",type="array")
     */
    private $productUsage ;
    /**
     * @ORM\Column(name="instance_parameters",type="array")
     */
    private $instanceParameters ;

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
     * Set productUsage
     *
     * @param array $productUsage
     *
     * @return Options
     */
    public function setProductUsage($productUsage)
    {
        $this->productUsage = $productUsage;

        return $this;
    }

    /**
     * Get productUsage
     *
     * @return array
     */
    public function getProductUsage()
    {
        return $this->productUsage;
    }

    /**
     * Set instanceParameters
     *
     * @param array $instanceParameters
     *
     * @return Options
     */
    public function setInstanceParameters($instanceParameters)
    {
        $this->instanceParameters = $instanceParameters;

        return $this;
    }

    /**
     * Get instanceParameters
     *
     * @return array
     */
    public function getInstanceParameters()
    {
        return $this->instanceParameters;
    }
}
