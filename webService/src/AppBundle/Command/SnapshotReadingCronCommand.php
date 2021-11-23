<?php


namespace AppBundle\Command;

use ZipArchive;
use JMS\JobQueueBundle\Console\CronCommand;
use JMS\JobQueueBundle\Console\ScheduleHourly;
use JMS\JobQueueBundle\Console\ScheduleEveryOtherMinute;
use JMS\JobQueueBundle\Entity\Job;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use AppBundle\Services\LoggerSnapshots;



// Reading snapshots cron - load-all command
class SnapshotReadingCronCommand extends ContainerAwareCommand //implements CronCommand
{
    //use ScheduleHourly;
    // ...
    private $logSnap;       // log object that will contain process infos
    private $logTxt = "";   // temporary text to display...
    //declare(ticks = 1);
    

    /*public function __construct(LoggerSnapshots $loggerSnapshots){
        $this->$logSnap = $loggerSnapshots;
    }*/

    protected function configure(){
        $this
            // the name of the command (the part after "bin/console")
            ->setName('snapshot:load-all')
            ->addOption('debug','d',InputOption::VALUE_NONE,"dump the json in the log ")
            ->addOption('keep-here','k',InputOption::VALUE_NONE,"dont move file after parsing it")
            ->addOption('sub-dir','s',InputOption::VALUE_OPTIONAL,"crawl a spécific subdirectory")
            ->addOption('json-dump-dir','j',InputOption::VALUE_OPTIONAL,"dump the json to a directory")
            // the short description shown while running "php bin/console list"
            ->setDescription('Load all snapshots every hour')
            ->setHelp('')
        ;
    }
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){

        // ---------------------------------------------------------------------
        // -- OUTPUT LOG FILE

        // TITLE MSG - JOB NAME - EXEC TIME LOG FILE*/
        $logsDir = $this->getContainer()->getParameter("logsDir");
        $logFile = fopen($logsDir."snapshot_process.log", "w") or die("Unable to open file!"); // open snapshot_process.log
        $logErrFile = fopen($logsDir."snapshot_error.log", "w+") or die("Unable to open file!"); // open snapshot_error.log
        $snapshotDir = $this->getContainer()->getParameter('snapshot_baseDir'); // get snapshot baseDir
        $maxSnapshotsFiles = exec("ls ".$snapshotDir."*.txt | wc -l"); // get max number of txt files

        $logSnap = new LoggerSnapshots("SNAPSHOT:LOAD-ALL", $maxSnapshotsFiles); // create new logfile with name and number of files

        echo $logSnap->getLogFile();
        //fwrite($logFile, "Process did'nt started");
        fwrite($logErrFile, "\n\n\033[1;34m--------------------------------------------------------------"."\n".
                            "LAST EXECUTION: \033[1;34m".date("d-m-Y")." at: ".date("H:i:s")."\n".
                            "--------------------------------------------------------------"."\n"); 
        fclose($logErrFile); // close snapshot_error in w+ mode and reopen it in w mode
        $logErrFile = fopen($logsDir."snapshot_error.log", "a") or die("Unable to open file!");

        // -- END OF OUTPUT LOG FILE
        // ---------------------------------------------------------------------



        
        /* @var $logger Logger */
        $logger = $this->getContainer()->get("logger");
        $snapshotReader = $this->getContainer()->get('snapshot_reading'); // get service snapshot-reading
        $inputDir = $this->getContainer()->getParameter('snapshot_baseDir'); // get input file directory
        if ($subdir = $input->getOption('sub-dir')){
            $inputDir .=  '/'.$subdir;
        }
        $fileList = scandir($inputDir);             // scan directory to create a fileList
        $progress = new ProgressBar($output, 0);    // Create progress bar

        // ---------------------------------------------------------------------
        // -- ZIP EXTRACTION

        print "\nextraction of zips:\n";            // text progress bar
        $progress->start();                         // start progress animation
        foreach($fileList as $fileName ){           // for each file of the list
            $explodedFileName = explode('.', $fileName);
            $ext = end($explodedFileName);          // get the extention of the file
            if ($ext === "zip") {                   // if file is a zip file
                $zip = new ZipArchive();
                if ($zip->open($inputDir . DIRECTORY_SEPARATOR . $fileName) === TRUE) { // If opening is ok
                    $zip->extractTo($inputDir);     // Extract
                    $zip->close();                  // then close
                    rename($inputDir . DIRECTORY_SEPARATOR . $fileName, $this->getContainer()->getParameter('snapshot_zip_archive') . DIRECTORY_SEPARATOR . $fileName);
                    $progress->advance();           // go to next file
                    
                } else {
                    echo 'zip extraction failed';   // failed extraction
                }
            }
        }
        $progress->finish();

        // -- END OF ZIP EXTRACTION
        // ---------------------------------------------------------------------

        // -----------------------------------------------------------------------------------------------------------------
        
        // ---------------------------------------------------------------------        
        // -- SNAPSHOTS READING

        $fileList = scandir($inputDir);
        $progressBar = sizeof($fileList)-2;
        $progress = new ProgressBar($output, $progressBar);
        
        print "\nsnapshot file reading:\n";
        $progress->start();
        foreach($fileList as $fileName ){
            $explodedFileName = explode('.', $fileName);
            $ext = end($explodedFileName); # Get extention of the file
            if (in_array($ext, ["lst", "TXT", "LST", "txt"])) { // if file extention is LST or TXT
                try{
                    $pythonResponse = $snapshotReader->loadSnapshot($inputDir,$fileName); // load snapshot and parse it - webService AppBundle Services SnapshotReading
                    if($input->getOption('debug')){         // if we select -debug- option
                        print('raw json output for '.$fileName."\n".$pythonResponse."\n");
                    }
                    if($input->getOption('json-dump-dir')){ // if we select -json-dump-dir- option
                        $dumpFile = fopen($input->getOption('json-dump-dir').DIRECTORY_SEPARATOR.$fileName.".json.test", "w");
                        fwrite($dumpFile,$pythonResponse);
                        fclose($dumpFile);
                    }
                    if(!$input->getOption('keep-here')) {   // if we select -keep-here- option
                        $outputDir = $this->getContainer()->getParameter('snapshot_doneDir'); // put files into doneDir
                        rename($inputDir . DIRECTORY_SEPARATOR . $fileName, $outputDir . DIRECTORY_SEPARATOR . $fileName);
                        
                        // MODIF
                        $logSnap->increaseNbDoneFiles();
                    }
                    gc_collect_cycles();//dont know why, but manually calling the garbage collector make things better ...
                    $progress->advance();

                    // MODIF
                    echo " --> processing file: ".$fileName;

                }catch(Exception $e){
                    $trashDir = $this->getContainer()->getParameter('snapshot_trashDir'); // if errors where found throw files into trashDir
                    rename($inputDir . DIRECTORY_SEPARATOR . $fileName, $trashDir . DIRECTORY_SEPARATOR . $fileName);

                    // MODIF
                    $logSnap->increaseNbThrownFiles();
                    // IF SIGINT SENDED
                    /*$logTxt = $logSnap->getLogFile();
                    fwrite($logFile, $logTxt);
                    pcntl_signal(SIGINT, function(){
                        $logTxt = $logSnap->getLogFile();
                        fwrite($logFile, $logTxt);                        
                    });*/
                    fwrite($logErrFile, "\033[1;36m- Filename: ".$fileName."\033[0m\n".$e."\n\n");


                }
            }

        }
        $logTxt = $logSnap->getLogFile();
        fwrite($logFile, $logTxt);
        fclose($logFile); // MODIF close logFile
        fclose($logErrFile); // MODIF close logErrFile

        // -- END OF SNAPSHOTS READING
        // ---------------------------------------------------------------------
    }
}
