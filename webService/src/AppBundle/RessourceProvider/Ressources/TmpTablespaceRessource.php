<?php


namespace AppBundle\RessourceProvider\Ressources;


use AppBundle\Entity\Snapshots\Tablespace;
use AppBundle\RessourceProvider\AbstractClass\AbstractVirtualRessource;
use AppBundle\Entity\Snapshots\Snapshot;

class TmpTablespaceRessource extends AbstractVirtualRessource
{
    /*
     * @param $snapshot Snapshot
     */
    private function getTmpTablespace($snapshot){
            return $this->em->createQueryBuilder()
                ->select("Tablespace")
                ->from("AppBundle\Entity\Snapshots\Tablespace","Tablespace")
                ->where("Tablespace.name LIKE 'tmp%' ")
                ->orWhere("Tablespace.name LIKE 'temp%' ")
                ->andWhere("Tablespace.snapshot = :snapshotId")
                ->setParameter("snapshotId",$snapshot->getId())
                ->getQuery()
                ->getFirstResult();

    }
    public function getRessourceForSnapshot($snapshot)
    {
        return [$this->ressourceFactory->get("tablespace")->getRessource($this->getTmpTablespace($snapshot))];
    }
    public function getMetadata()
    {
        $metadata = $this->ressourceFactory->get("tablespace")->getMetadata();
        $metadata["title"] = "tmptablespace";
        return $metadata;
    }
}