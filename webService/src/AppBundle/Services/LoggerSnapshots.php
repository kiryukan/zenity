<?php

namespace AppBundle\Services;

// CLASS LOGGER - CREATE LOG FILE FOR SNAPSHOTS
class LoggerSnapshots{

    private $logFileContent = ""; 
    private $processName = "UNDEFINED";
    private $status = "";
    private $lastExec = "";
    private $maxFiles = 0;
    private $totalProcessedFiles = 0;
    private $nbDoneFiles = 0;
    private $nbThrownFiles = 0;

    public function __construct($pN="", $mF=0){
        $this->processName = $pN;
        
        $this->maxFiles = $mF;
        $this->status = "INCOMPLETE";
        $this->lastExec = "\033[1;34m".date("d-m-Y")." at: ".date("H:i:s")."\n";
    }

    public function setProcessName($processName = ''){
        $this->processName = $processName;
        echo  $this->processName;
    }

    public function setStatus($status = ''){
        $this->status = $status;
    }    

    public function increaseTotalProcessedFile(){
        $this->totalProcessedFiles ++;
    }
    
    public function increaseNbDoneFiles(){
        $this->nbDoneFiles ++;
        $this->increaseTotalProcessedFile();
    }

    public function increaseNbThrownFiles(){
        $this->nbThrownFiles ++;
        $this->increaseTotalProcessedFile();
    }

    // GETTERS

    public function getProcessName(){
        return $this->processName;
        //return "Hello";
    }

    public function getStatut(){
        return $this->statut;
    }

    public function getTotalProcessedFiles(){
        return $this->totalProcessedFiles;
    }
    
    public function getNbDoneFiles(){
        return $this->nbDoneFiles;
    }

    public function getNbThrownFiles(){
        return $this->nbThrownFiles;
    }

    public function changeStatut(){
        if($this->maxFiles == $this->totalProcessedFiles && $this->maxFiles > 0){
            if($this->getNbThrownFiles() == 0){
                $this->setStatus("\033[32m COMPLETED WITHOUT ERROR \033[0m");
            }else
            {
                $this->setStatus("\033[31m COMPLETED WITH ERROR \033[0m");
            }
        }
        else{
            if($this->maxFiles == 0){
                $this->setStatus("\033[32m NOT ANY FILES PROCESSED - EMPTY DIRECTORY  \033[0m");
            }
            else{
                $this->setStatus("\033[31m INCOMPLETE \033[0m");
            }
        }
    }

    public function getLogFile(){

        $this->changeStatut();

        // TITLE

        $logFileTxt = "";
        $txtProcessName = "\n\033[0;32m--------------------------------------------------------------------------\n".
        "\033[1;33mJOB NAME: ".$this->processName."\n".
        "\033[0;32m--------------------------------------------------------------------------\033[1;34m\n\n";
        //$txt = $txtProcessName."LAST EXECUTION: ".date("d-m-Y")." at: ".date("H:i:s")."\n\n"; // display time of execution
        $txt = "";
        $txt = $txtProcessName."\033[1;33mLAST EXECUTION: \033[1;34m".$this->lastExec;
        $txt = $txt."\033[0;32m---------------\033[1;34m\n\n";

        // STATUS
        $statusTxt = "\033[1;33mSTATUS: ".$this->status."\n";
        $statusTxt = $statusTxt."\033[0;32m-------\033[0m\n";
        $txt = $txt.$statusTxt."\n";

        // STATS
        $nbFilesTxt = "\033[1;33mSTATS:\n";
        $nbFilesTxt = $nbFilesTxt."\033[0;32m------\n";
        $nbFilesTxt = $nbFilesTxt."\033[0;37mNB TOTAL FILES IN SNAPSHOTS DIRECTORY: \033[1;34m".$this->maxFiles."\n";
        $nbFilesTxt = $nbFilesTxt."\033[0;37mNB TOTAL FILES PROCESSED: \033[1;34m".$this->totalProcessedFiles."\n";
        $nbFilesTxt = $nbFilesTxt."\033[0;37mNB FILES DONE - INTO SNAPSHOTS-DONE: \033[1;34m".$this->nbDoneFiles."\n";
        $nbFilesTxt = $nbFilesTxt."\033[0;37mNB FILES THROWN - INTO SNAPSHOTS-TRASHDIR: \033[1;34m".$this->nbThrownFiles."\033[0m\n";
        $txt = $txt.$nbFilesTxt."\n\033[0;32m--------------------------------------------------------------------------\033[0m\n\n";

        return $txt;
    }
}