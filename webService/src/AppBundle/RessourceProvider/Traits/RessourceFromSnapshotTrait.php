<?php

namespace AppBundle\RessourceProvider\Traits;


trait RessourceFromSnapshotTrait
{
    Use FiltrableTrait;
    abstract public function getRessourceForSnapshot($snapshot);
    public function getRessourceBySnapshotAndIndicator($snapshot,$indicatorName,$filterArray=null){
        $ressources = $this->getRessourceForSnapshot($snapshot);
        if($filterArray){
            $ressources = $this->filterRessource($filterArray,$ressources);
        }
        $data = [];
        foreach ($ressources as $ressourceKey=>$ressource){
            $data[$ressourceKey] = (isset($ressource[$indicatorName]))?$ressource[$indicatorName]:null;
        }
        return $data;
    }
}