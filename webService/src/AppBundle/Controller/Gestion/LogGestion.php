<?php
namespace AppBundle\Controller\Gestion;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogGestion extends Controller
{
    /**
     * @Route("/gestion/logs", name="gestion_log")
     */
    public function logAction(Request $request)
    {
        $logs = [];
        $path = $this->get('kernel')->getRootDir().'/../var/logs/';
        $files = scandir($path);

        foreach ($files as $file){
            $fileExploded = explode(".",$file);

            if (end($fileExploded) === "log"){
                array_push($logs, [
                    "link" => "gestion/logs/".$file,
                    "name" =>$file
                ]);
            }
        }
        return $this->render('gestion/logGestion.html.twig', [
            "logs"=>$logs
        ]);
    }
    /**
     * @Route("/gestion/logs/{log_name}", name="gestion_printLog")
     */
    public function printLog(Request $request,$log_name){
        return $this->render('gestion/logs.html.twig', [
            "logContent"=>$this->loadLog(0,$log_name),
        ]);
    }
    private function loadLog($offset,$log_name){
        $LOG_SIZE = 500;
        $path = $this->get('kernel')->getRootDir().'/../var/logs/';
        $textLog = file_get_contents($path.$log_name);
        $lines = explode("\n", $textLog);
        $lines = array_reverse($lines);
        $lines=array_slice ( $lines , $offset,$offset+$LOG_SIZE);
        $offset += $LOG_SIZE;
        $textLog =  implode("\n", $lines);
        $htmlLog = preg_replace("/\n/i","<br>--------------------------------------------------<br>",$textLog);
        $htmlLog = preg_replace("/,/i",",<br>",$htmlLog);
        $htmlLog = preg_replace("/{/i","<br>{<br>",$htmlLog);
        $htmlLog = preg_replace("/}/i","<br>}",$htmlLog);
        return $htmlLog;
    }
}
