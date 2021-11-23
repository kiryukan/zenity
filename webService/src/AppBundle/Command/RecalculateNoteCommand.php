<?php


namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RecalculateNoteCommand extends ContainerAwareCommand
{
    protected function configure(){
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:note:recalculate')
            ->addArgument('from', InputArgument::OPTIONAL, 'the date("Y-m-d H:i:s") where recalculation start?')
            ->addArgument('to', InputArgument::OPTIONAL, ' the date("Y-m-d H:i:s") where recalculation end?')
            ->addArgument('noteEngineId', InputArgument::OPTIONAL, 'the id of the note to recalculate ?')
            // the short description shown while running "php bin/console list"
            ->setDescription('Recalculate note for a given period.')
            ->setHelp('')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        $from = date_create_from_format("Y-m-d H:i:s",$input->getArgument('from'));
        $to = date_create_from_format("Y-m-d H:i:s",$input->getArgument('to'));
        $noteEngineId = $input->getArgument('noteEngineId');
        $this->getContainer()->get('ressource.note')->recalculateNote($noteEngineId,$from,$to);
    }
}
