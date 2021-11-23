<?php

namespace AppBundle\Controller;

use AppBundle\RessourceProvider\AbstractClass\AbstractEntityRessource;
use AppBundle\RessourceProvider\AbstractClass\AbstractRessource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MetadataProvider extends Controller
{
    /**
     * @Route("/api/metadata/{className}", name="api_metadataByClass")
     * @Route("/gestion/metadata/{className}",name="gestion_metadataByClass")
     */
    public function getSnapshotMetadataByClass(Request $request, $className)
    {
        /** @var $ressourceProvider AbstractRessource */
        $ressourceFactory = $this->get('ressource.factory');
        $ressourceProvider = $ressourceFactory->get($className);
        $metadata = $ressourceProvider->getMetadata();
        if($metadata instanceof AbstractEntityRessource){
            /** @var $ressourceProvider AbstractEntityRessource */
            $metadata['other'] = $ressourceProvider->getMetadataStats();
        }

        return new JsonResponse($metadata);
    }

    /**
     * @Route("/api/metadata", name="api_metadata")
     * @Route("/gestion/metadata",name="gestion_metadata")
     */
    public function getSnapshotMetadata(Request $request)
    {
        $classes = [
            "advisory",
            "efficiencyIndicator",
            "event",
            "instanceState",
            "loadProfile",
            "network",
            "note",
            "oSState",
            "request",
            "sqlInfo",
            "stat",
            "statistics",
            "tableSpace",
            "tmpTablespace",
        ];
        $metadata = [];
        foreach ($classes as $className) {
            $ressourceFactory = $this->get('ressource.factory');
            $ressourceProvider = $ressourceFactory->get($className);
            $metadata[$className] = $ressourceProvider->getMetadata();
            if(method_exists($ressourceProvider,'getMetadataStats')){
                array_merge($metadata[$className],$ressourceProvider->getMetadataStats());
            }
        }
        return new JsonResponse($metadata);
    }

}
