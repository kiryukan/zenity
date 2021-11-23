<?php
namespace AppBundle\Command;


use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Console\CronCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteBaseCommand extends ContainerAwareCommand
{
    protected function configure(){
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:delete-base')
            ->addArgument('clientName', InputArgument::REQUIRED, 'the name of the client')
            ->addArgument('baseName', InputArgument::REQUIRED, 'the name of the database')
            // the short description shown while running "php bin/console list"
            ->setDescription('Delete a base with cascade.')
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
        $client = $em->getRepository('AppBundle\Entity\Client')
            ->findOneBy(['name'=>$input->getArgument('clientName')]);
        $base = $em->getRepository('AppBundle\Entity\Base\Base')
            ->findOneBy(['name'=>$input->getArgument('baseName')]);
        if($base !== null && $client !== null) {
            $em->remove($base);
            $em->flush();
            print('La base '.$input->getArgument('baseName').'as été supprimer avec succès');
        }
    }
}
