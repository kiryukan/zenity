<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Base\Instance;
use AppBundle\Entity\Metadata\InstanceMetadata;
use Doctrine\ORM\EntityManager;

class SyncInstanceMetadataCommand extends ContainerAwareCommand
{
    protected function configure(){
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:metadata:sync-instance')
            // the short description shown while running "php bin/console list"
            ->setDescription('Synchronize the metadata or all instances')
            ->setHelp('')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        /* @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $instances = $em->getRepository('AppBundle\Entity\Base\Instance')->findAll();
        $em->createQueryBuilder()
            ->delete('AppBundle\Entity\Metadata\InstanceMetadata','InstanceMetada')
            ->getQuery()
            ->execute()
        ;
        /* @var $instance Instance */
        foreach ($instances as $instance){
            $instanceMetadata = new InstanceMetadata();
            $nbSnapshot = $em->createQueryBuilder()
                ->select("count(Snapshot)")
                ->from('AppBundle\Entity\Snapshots\Snapshot','Snapshot')
                ->where('Snapshot.instance = :instanceId')
                ->setParameter("instanceId",$instance->getId())
                ->getQuery()
                ->getResult()[0][1]
            ;
            $instanceMetadata->setInstance($instance->getId());
            $instanceMetadata->setNbSnapshot($nbSnapshot);
            $em->persist($instanceMetadata);
            $em->flush();
        }
    }
}
