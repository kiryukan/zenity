<?php


namespace AppBundle\RessourceProvider\AbstractClass;

use AppBundle\Entity\Base\Instance;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\RessourceProvider\Interfaces\IRessourceMetadata;
use AppBundle\RessourceProvider\RessourceFactory;
use AppBundle\RessourceProvider\Traits\RessourceFromSnapshotTrait;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Snapshots\EfficiencyIndicator;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

abstract class AbstractRessource implements IRessourceMetadata
{
    use RessourceFromSnapshotTrait;

    /**
     * @var RessourceFactory
     */
    protected $ressourceFactory;
    /**
     * @var EntityManager
     */
    protected $em;
    public function __construct(RessourceFactory $ressourceFactory,EntityManager $em) {
        $this->em = $em;
        $this->ressourceFactory = $ressourceFactory;
    }
}