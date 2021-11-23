<?php


namespace AppBundle\RessourceProvider\Ressources;
use AppBundle\Entity\Snapshots\Snapshot;
use AppBundle\Entity\Snapshots\Parameters;
use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;

// GET "PARAMETERS" FOR REPORT
// see more changes line 77 //

class ParametersRessource extends AbstractEntityRessource
{

    /**
     * @return mixed
     */
    function getMetadata()
    {
        return [null];
    }

    /**
     * @param $entity Parameters
     * @param String $json
     * @return Parameters
     */
    public function updateFromJson($entity,$json){
        $jsonDecoded = json_decode($json,true);
        if(isset($jsonDecoded["name"]) === false){
            return $entity;
        }
        if(!$entity){
            $entity = new Parameters();
        }
        $entity->setName($jsonDecoded["name"]);
        $entity->setValue($jsonDecoded["value"]);

        return $entity;
    }

    /**
     * @param $entity Parameters
     * @return array
     */
    public function getRessource($entity){
        if ($entity){
            return [
                'name'=>$entity->getName(),
                'value'=>$entity->getValue(),
            ];
        }
        else{
            return null;
        }
    }
    /**
     * @param $snapshot Snapshot
     * @return mixed
     */
    public function getRessourceForSnapshot($snapshot)
    {
        $data = [];
        $parameters = $snapshot->getParameters();
        foreach ($parameters as $parameter){
            $ressource =  $this->getRessource($parameter);
            if(strcmp(strtoupper($ressource["name"]),$ressource["name"]) < 0){ // Avoid to display Bad name fields...
                $data[$ressource["name"]] = $ressource["value"];
            }
        }
        ksort($data);
        return [$data];
    }

}

// Issues fixed //
// line 67 - hide bad field names (uppercased)