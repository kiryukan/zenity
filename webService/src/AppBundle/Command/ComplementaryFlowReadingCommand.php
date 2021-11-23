<?php

namespace AppBundle\Command;
use JMS\JobQueueBundle\Console\CronCommand;
use JMS\JobQueueBundle\Console\ScheduleEveryMinute;
use JMS\JobQueueBundle\Entity\Job;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;


class ComplementaryFlowReadingCommand extends ContainerAwareCommand implements CronCommand
{
    use ScheduleEveryMinute;
    protected function configure(){
        $this
            // the name of the command (the part after "bin/console")
            ->setName('complementaryflow:load')
            ->addOption('debug','d',InputOption::VALUE_NONE,"dump the json in the log ")
            ->addOption('keep-here','k',InputOption::VALUE_NONE,"dont move file after parsing it")
            ->addOption('sub-dir','s',InputOption::VALUE_OPTIONAL,"crawl a spécific subdirectory")
            // the short description shown while running "php bin/console list"
            ->setDescription('Load all xml every minutes')
            ->setHelp('')
        ;
    }
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        /* @var $logger Logger */
        $logger = $this->getContainer()->get("logger");
        $additionnalInfosReader = $this->getContainer()->get('complementaryFlow_reader');
        $inputDir = $this->getContainer()->getParameter('snapshot_baseDir');
        if ($subdir = $input->getOption('sub-dir')){
            $inputDir .=  '/'.$subdir;
        }
        $fileList = scandir($inputDir); // scan directory and get file list
        $progressBar = sizeof($fileList)-2;
        $nbFiles = 0;
        $totalFiles = sizeof($fileList) -2;
        $progress = new ProgressBar($output, $progressBar);
        print "\nadditional info reading:\n";
        $progress->start();
        foreach($fileList as $fileName ){
            print("\nfilename: ".$fileName."\n");
            $explodedFileName = explode('.', $fileName);
            $ext = end($explodedFileName);
            if (in_array($ext, ["xml"])) {
                try{
                    $xml = file_get_contents($inputDir.'/'.$fileName);
                    $xml = utf8_encode(preg_replace("(\n|\r|\r\n)","",$xml));
                    
                    $fetched = $additionnalInfosReader->readXml($xml,$fileName);
                    if($fetched && !$input->getOption('keep-here')) { // if file fetched and option keep here is not selected then file is parsed
                        $outputDir = $this->getContainer()->getParameter('snapshot_doneDir');
                        rename($inputDir . DIRECTORY_SEPARATOR . $fileName, $outputDir . DIRECTORY_SEPARATOR . $fileName);
                    }
                }catch(Exception $e){
                    $logger->error($e);
                }
            }
            $nbFiles++;
        }
        print("\ntotal files before process: ".$totalFiles."\n");
        print("\ntotal files processed: ".$nbFiles."\n");
    }
}
