<?php


namespace AppBundle\RessourceProvider;

use AppBundle\RessourceProvider\AbstractClass\AbstractRessource;
use AppBundle\RessourceProvider\Exception\UnvalidRessourceException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class RessourceFactory
{    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * @param $ressourceName
     * @return AbstractRessource
     */
    public function get($ressourceName){
        try{
            return $this->container->get('ressource.'.strtolower($ressourceName));
        }catch (ServiceNotFoundException $e){
            print $ressourceName;
            throw $e;
        }
    }
}