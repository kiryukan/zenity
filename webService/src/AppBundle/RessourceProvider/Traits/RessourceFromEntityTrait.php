<?php

namespace AppBundle\RessourceProvider\Traits;


trait RessourceFromEntityTrait
{
    abstract public function getRessource($entity);
    public function getRessourceByIndicator($entity,$indicatorName){
        return (isset($this->getRessource($entity)[$indicatorName]))?
            $this->getRessource($entity)[$indicatorName]
            :null;
    }
}