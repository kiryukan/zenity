<?php



namespace AppBundle\RessourceProvider\Traits;


Trait FiltrableTrait
{
    public function filterRessource($filterArray,$ressources){

        if(!$filterArray || !$ressources)return $ressources;
        foreach ($ressources as $ressourceKey=>$ressource){
            foreach ($filterArray as $filterField=>$filterValue){
                if(isset($ressource[$filterField])
                    && strtolower($ressource[$filterField]) !== strtolower($filterValue)
                    ){
                    unset($ressources[$ressourceKey]);
                }
            }
        }
        return $ressources;
    }
}