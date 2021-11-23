<?php
use AppBundle\Entity\Snapshots\Snapshot;

namespace AppBundle\Services;

use AppBundle\Entity;
use AppBundle\RessourceProvider\RessourceFactory;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class ComplementaryFlowReader
{
    /**
     * @var EntityManager
     */
    public $em;
    private $ressourceFactory;
    public function __construct(RessourceFactory $ressourceFactory,EntityManager $entityManager, $snapshotBaseDir)
    {
        $this->ressourceFactory = $ressourceFactory;
        $this->em = $entityManager;
    }
    // ----------------------------------------------
    // ----------------- READ LOCKS -----------------
    // ----------------------------------------------
    public function readLocks($locks,$instance){
        foreach ($locks as $lock){
            $lock = (array)$lock;
            $timeStamp = date_create_from_format("Y-m-d H:i",$lock['TIMESTAMP']);
            $startDate = clone $timeStamp;
            $startDate->sub(date_interval_create_from_date_string("1 hour"));
            $endDate = clone $timeStamp;
            $endDate->add(date_interval_create_from_date_string("1 hour"));
            /* @var $snapshot Entity\Snapshots\Snapshot */
            $snapshot = $instance->getSnapshotsBetween(
                $startDate,
                $endDate
            )[0];
            if ($snapshot !== null && array_key_exists('LOCK_ID',$lock) ){
                $lockEntity = $this->em->getRepository('AppBundle\Entity\ComplementaryFlow\Lock')->findOneBy(['timestamp'=>$timeStamp,'lock_id'=>$lock['LOCK_ID']]);
                $lockEntity = $lockEntity !== null?$lockEntity:new Entity\ComplementaryFlow\Lock();
                $this->ressourceFactory->get('lock')->updateFromJson($lockEntity,json_encode($lock));
                $snapshot->addLocks($lockEntity);
                $this->em->persist($lockEntity);
                $this->em->flush();
                return true;
            }else{
                if($snapshot == null){
                    print "No snapshot (yet) for this lock\n";
                }else{
                    print "malformed lock\n";
                }
                return false;
            }
        }
        return true;
    }
    // ----------------------------------------------
    // ------------- READ LONG SESSIONS -------------
    // ----------------------------------------------
    public function readLongSessions($longSessions,$instance){
        foreach ($longSessions as $longSession){
            $longSession = (array)$longSession;
            $timeStamp = date_create_from_format("Y-m-d H:i",$longSession['TIMESTAMP']);
            $startDate = clone $timeStamp;
            $startDate->sub(date_interval_create_from_date_string("1 hour"));
            $endDate = clone $timeStamp;
            $endDate->add(date_interval_create_from_date_string("1 hour"));
            /* @var $snapshot Entity\Snapshots\Snapshot */
            $snapshot = $instance->getSnapshotsBetween(
                $startDate,
                $endDate
            )[0];
            if ($snapshot !== null && array_key_exists('LONG_SESSION_ID',$longSession) ){
                $longSessionEntity = $this->em->getRepository('AppBundle\Entity\ComplementaryFlow\LongSession')->findOneBy(['timestamp'=>$timeStamp,'long_session_id'=>$longSession['LONG_SESSION_ID']]);
                $longSessionEntity = $longSessionEntity !== null?$longSessionEntity:new Entity\ComplementaryFlow\LongSession();
                $this->ressourceFactory->get('longSession')->updateFromJson($longSessionEntity,json_encode($longSession));
                $snapshot->addLongSession($longSessionEntity);
                $this->em->persist($longSessionEntity);
                $this->em->flush();
                return true;
            }else{
                if($snapshot == null){
                    print "No snapshot (yet) for this lock\n";
                }else{
                    print "malformed lock\n";
                }
                return false;
            }
        }
        return true;
    }
    // ----------------------------------------------
    // ------------------ READ XML ------------------
    // ----------------------------------------------
    public function readXml($xmlStr,$fileName){ // XML reading function - xmlStr file to be parsed - filename is the name of the current file
        $dom = new \DOMDocument; // create DOM
        $tags = null;
        $infos = null;
        try{
            $dom->loadXML($xmlStr);                     // get all xml file content as DOM
            $tags = $dom->getElementsByTagName("*");  // get all tags from DOM
            foreach($tags as $info){
                $infos[$info->tagName] = $info->nodeValue; // mapping tags as key and content as value
            }
            
            // Retrieve client name from filename
            $infos['clientName'] = explode('_', $fileName)[0]; // create key clientName and value is the first part of fileName
            if(!array_key_exists('clientName',$infos) || !array_key_exists('DB_NAME',$infos) ) {
                print "error: file probably empty\n";
                return false;
            }
            //print "test: READ EDITION\n"; //ok            
            print_r("client name xml: ".$infos['clientName']);
            try{
                $client = $this->em->getRepository('AppBundle\Entity\Client')->findOneBy(['name'=>$infos['clientName']]); // search client by name
            }catch(\Exception $e){
                print_r("\033[91mClient name not found in db... Line ".__LINE__."\033[39m\n");
            }
            // search base from client
            try{
                $database = $this->em->getRepository('AppBundle\Entity\Base\Base')->findOneBy(['name' => $infos['DB_NAME'], 'client' => $client->getId()]); // search db by db name and client id
            }catch(\Exception $e){
                print_r("\033[91mDatabase name not found in db... Line ".__LINE__."\033[39m\n");
            }

            if($client->getId()){
                print "client ".$client->getId().": line".__LINE__." \33[39m\n";
            }
            try{
                if($database){
                    print "db ".$database->getId().": line".__LINE__." \033[39m\n";
                }
            }catch(\Exception $e){
                print "No database id\n";
            }

            // SEARCH INSTANCE NAME IN DB
            try{
                if($database){
                    /* @var $instance Entity\Base\Instance */
                    $instance = $this->em->getRepository('AppBundle\Entity\Base\Instance')->findOneBy(['name' => $infos['INSTANCE_NAME'], 'base' => $database->getId()]);
                    if ($instance == null) {
                        throw new Exception("Instance : " . $infos['INSTANCE_NAME'] . " (from dataBase " . $infos['DB_NAME'] . ") not found");
                    }
                }
            }catch(\Exception $e){
                print_r("\033[91mInstance name not found in db... Line ".__LINE__."\033[39m\n");
            }
            $status = false;
            
            if(array_key_exists('PERFORMANCE',$infos) && !is_string($infos['PERFORMANCE']))$status = $this->readPerformance($infos['PERFORMANCE'],$instance,$infos['TIMESTAMP']);
            if(array_key_exists('OPTIONS',$infos) && !is_string($infos['OPTIONS']))$status = $this->readOptions($infos['OPTIONS'],$instance);
            if(array_key_exists('LOCKS',$infos) && !is_string($infos['LOCKS']))$status = $this->readLocks($infos['LOCKS'],$instance);
            if(array_key_exists('LONG_SESSIONS',$infos)&& !is_string($infos['LONG_SESSIONS']) )$status = $status?$this->readLongSessions($infos['LONG_SESSIONS'],$instance):false;
            if(array_key_exists('RMAN',$infos)&& !is_string($infos['RMAN'])
            &&array_key_exists('RMAN_CONFIGURATION',$infos)&& !is_string($infos['RMAN_CONFIGURATION'])
            )$status = $status?$this->readRman($infos['RMAN'],$infos['RMAN_CONFIGURATION'],$instance):false;
            
            //TEST READ EDITION
            print "test: READ EDITION\n";
            if(array_key_exists('DB_NAME',$infos))print "DB_NAME: ".$infos["DB_NAME"]."\n";
            if(array_key_exists('DB_NAME',$infos) &&
            array_key_exists('INSTANCE_NAME',$infos) &&
            array_key_exists('HOST_NAME',$infos) &&
            array_key_exists('EDITION',$infos) &&
            array_key_exists('BANNER',$infos)){
                print "test: KEYS READ\n";
                $instanceId;
                try{
                    $instanceId = $this->getInstanceIdFromXML($infos['DB_NAME'],$infos['INSTANCE_NAME'],$infos['HOST_NAME'],$infos['clientName'],$infos['EDITION'],$infos['BANNER']);
                    print_r("\ninstance id from db: ".$instanceId."\n");
                }catch(\Exception $e){
                    print_r("\033[91mInstance id not found in db... Line ".__LINE__."\033[39m\n");
                }
                if($instanceId){
                    $this->readOptionsEditionFromXML($dom, $infos['EDITION'], $instanceId, $this->em);
                    return $instanceId;
                }
            }
            else{
                print "test: KEYS NOT READ\n";
            }

            // END TEST EDITION
        }catch(\Exception $e){

        }
    }
    // ----------------------------------------------
    // --------------- READ OPTIONS -----------------
    // ----------------------------------------------    
    public function readOptions($options,$instance){ // read and decode OPTIONS flows
        $optionsEntity = $instance->getOptions()?? new Entity\ComplementaryFlow\Options();
        $this->ressourceFactory->get('options')->updateFromJson($optionsEntity,json_encode($options));
        $this->em->persist($optionsEntity);
        $instance->setOptions($optionsEntity);
        $this->em->persist($instance);
        $this->em->flush();
    }
    // ----------------------------------------------
    // ----------------- READ PERFS -----------------
    // ----------------------------------------------
    public function readPerformance($perf,$instance,$timeStamp){ // read and decode PERFORMANCE flows
      $perf = json_decode(json_encode($perf),True);
      $perf['TIMESTAMP'] = $timeStamp;
      $date = \DateTime::createFromFormat('Y-m-d H:i:s',$timeStamp);
      $snapshot = $instance->getSnapshotsForStartDate($date);
      if($snapshot == null){
          print "No snapshot (yet) for this benchmark\n";
          return null;
      }
      $performanceBenchEntity = $snapshot->getPerformanceBench()??new Entity\ComplementaryFlow\PerformanceBench();
      $this->ressourceFactory->get('performancebench')->updateFromJson($performanceBenchEntity,json_encode($perf));
      $snapshot->setPerformanceBench($performanceBenchEntity);
      $this->em->persist($snapshot);
      $this->em->persist($performanceBenchEntity);
      $this->em->flush();
    }
    // ---------------------------------------------
    // ----------------- READ RMAN -----------------
    // ---------------------------------------------
    public function readRman($rman,$rmanConfiguration,$instance,$timeStamp){ // read and decode RMAN flows
        $rman = json_decode(json_encode($rman),True);
        $rmanConfiguration = json_decode(json_encode($rmanConfiguration),True);
        $rman["TIMESTAMP"] = $timeStamp;
        $rman["RMAN_CONFIGURATION"] = $rmanConfiguration;
        $date = \DateTime::createFromFormat("Y-m-d H:i:s",$timeStamp);
        $snapshot = $instance->getSnapshotsForStartDate($date);
        if($snapshot == null){
            print "No snapshot (yet) for this rman report\n";
            return null;
        }
        $rmanEntity = null;
        $this->ressourceFactory->get('rman')->updateFromJson($rmanEntity,json_encode($rman));
        $this->em->persist($rmanEntity);
        $this->em->flush();
    }

    // -----------------------------------------------------------------------------------
    // ------------------------- GET INSTANCE ID FROM XML --------------------------------
    // -----------------------------------------------------------------------------------

    public function getInstanceIdFromXML($dbName, $instanceName, $hostName, $clientName, $edition, $banner){ // readEdition to decode edition

        // ------------ GET THE PROVIDER NAME FROM BANNER
        $bannerSegments = explode(" ", $banner);
        $provider = $bannerSegments[0];
        $cloudProviderId = 0; // set provider id to 0
        if($cloudProviderId == "Oracle")$cloudProviderId = "4"; // if provider is oracle set to it's id manually
        
        // ------------ SEARCH FOR CLIENT ID BY CLIENT NAME
        print("\nsearch client\n");
        try{
            $queryClientId = "SELECT client.id FROM client WHERE client.name='$clientName';";
            $clientIdStatement = $this->em->getConnection()->prepare($queryClientId);
            $clientIdStatement->execute();
            $clientIdResult = $clientIdStatement->fetchAll();
            print("\nclient id count: ".var_dump($clientIdResult)."\n");
        }catch(\Exception $e){
            print("\033[91mQuery for client id didn't return a result!\033[39m\n");
        }            
        if($clientIdResult && count($clientIdResult) > 0) // if client found
        {
            print("\nsearch base id\n");
        // ------------ SEARCH FOR BASE ID BY BASE NAME
            try{
                $queryBaseId = "SELECT base.id FROM base WHERE base.client_id='".$clientIdResult[0]["id"]."' and base.name='$dbName';";
                $baseIdStatement = $this->em->getConnection()->prepare($queryBaseId);
                $baseIdStatement->execute();
                $baseIdResult = $baseIdStatement->fetchAll();
                print("\nbase id count: ".count($baseIdResult)."\n");
            }catch(\Exception $e){
                print("\033[91mQuery for base id didn't return a result!\033[39m\n");
            }
            if(count($baseIdResult) > 0) // if base found
            {
                print("\nsearch instance\n");
        // ----------- SEARCH FOR INSTANCE BY BASE ID, INSTANCE NAME AND SERVER NAME
                try{
                    $queryInstance = "SELECT instance.id FROM instance WHERE instance.base_id='".$baseIdResult[0]["id"]."' and instance.name='$instanceName' and instance.server_name='$hostName';";
                    print "Query: ".$queryInstance."\n";
                    $instanceStatement = $this->em->getConnection()->prepare($queryInstance);
                    $instanceStatement->execute();
                    $instanceIdResult = $instanceStatement->fetchAll();
                    print("\ninstance id count: ".count($instanceIdResult)."\n");
                }catch(\Exception $e){
                    print("\033[91mQuery for instance id didn't return a result!\033[39m\n");
                }

                if(count($instanceIdResult) > 0){
                    print_r("Instance id result: ".$instanceIdResult[0]["id"]);
                    return $instanceIdResult[0]["id"];
                }
            }
            else{
                print("\033[91mno base found\033[39m\n");
                return -1;
            }
        }
        else{
            print("\nerror, no client found with the specified name...".$clientName."\n");
            return -1;
        }
        
    }

    //---------------------------------------
    //----- READ OPTIONS FROM XML FILE ------
    //---------------------------------------
    public function readOptionsEditionFromXML($dom, $edition, $instanceId, $em){
        $domOptions = $dom->getElementsByTagName("FEATURE_USAGE_DETAILS"); // RETRIEVE ALL TAGS FEATURE_USAGE_DETAILS
        print_r("read options edition\n");
        if(strcmp($edition, "Enterprise Edition") == 0){ // if the package is enterprise edition...
            $optionsCloudInstance = $this->getOptionsFromCloudInstance($em); // RETRIEVE ALL OPTIONS FROM DB
            $options;
            $lastProductName = ""; // keep last processed product in loop
            //$i = 0;
            // SEARCH AND RETRIEVE ALL PRODUCT AND USAGE TAGS FROM XML
            foreach($domOptions as $opt){
                $j = 0;
                foreach($opt->childNodes as $tag){
                    $j++;
                }
                for($x = 0; $x < $j; $x++){
                    if($opt->childNodes[$x]->nodeName == "PRODUCT"){
                        if($lastProductName != $opt->childNodes[$x]->nodeValue){
                            for($y = $x; $y < $j; $y++){
                                if($opt->childNodes[$y]->nodeName == "USAGE"){
                                    $this->options[$opt->childNodes[$x]->nodeValue] = $opt->childNodes[$y]->nodeValue;
                                    //print_r($opt->childNodes[$x]->nodeValue.": - x=".$x);
                                    //print_r($opt->childNodes[$y]->nodeValue." y=".$y."\n");
                                    $lastProductName = $opt->childNodes[$x]->nodeValue;
                                    break;
                                }
                            }
                        }else{
                            for($y = $x; $y < $j; $y++){
                                if($opt->childNodes[$y]->nodeName == "USAGE"){
                                    if(($opt->childNodes[$y]->nodeValue == "PAST_USAGE") || ($opt->childNodes[$y]->nodeValue == "PAST_OR_CURRENT_USAGE") || ($opt->childNodes[$y]->nodeValue == "CURRENT_USAGE")){
                                        $this->options[$lastProductName] = $opt->childNodes[$y]->nodeValue; // get option name as key and usage as value
                                        //$lastProductName = $opt->childNodes[$x]->nodeValue;
                                    }
                                }
                            }
                        }
                    }
                }
                //$i++;
            }
            // KEEP ONLY ACTIVE OPTIONS (eventually change some names to match with ODBCS options names )
            // -----------------------------------------------------------------------------------------
            // NORMALIZE NAMES TO MATCH THE ODBCS...
            foreach($this->options as $key => $value){
                if($value == "PAST_USAGE" or $value == "PAST_OR_CURRENT_USAGE" or $value == "CURRENT_USAGE"){
                    print_r("USAGE ACTIVED...\n");
                    if(($key == "Real Application Clusters" or $key == "RAC or RAC One Node" or $key == "Real Application Clusters One Node")){
                        //print_r("Value used: ".$key."\n");
                        $this->options["RAC"] = $value;
                        $key = "RAC"; // assign name from ODBCS - acronyme for Real Application Clusters
                        if(array_key_exists("Real Application Clusters", $this->options)){
                            unset($this->options["Real Application Clusters"]);  // delete old name
                        }
                        if(array_key_exists("RAC or RAC One Node", $this->options)){
                            unset($this->options["RAC or RAC One Node"]); // delete old name
                        }
                        if(array_key_exists("Real Application Clusters One Node", $this->options)){
                            unset($this->options["Real Application Clusters One Node"]); // delete old name
                        }
                    }else{
                        print_r("No active RAC found: ".__LINE__."\n");
                    }

                    if($key == "Database In-Memory"){
                        $this->options["In-Memory Database"] = $value;
                        $key = "In-Memory Database"; // assign name from ODBCS
                        unset($this->options["Database In-Memory"]); // delete old name
                    }else{
                        print_r("No active In-Memory Database found: \n");
                    }

                }else{ // ERASE UNUSED OPTIONS...
                    unset($this->options[$key]);
                }
            }
            /*foreach($this->options as $key => $value){
                print_r("PRODUCT: ".$key." USAGE: ".$value."\n");
            } // TEST IF ARRAY IS FULL*/
            $packEditionId = 0;
            print_r("INSERTION OPTION EDITION : LINE ".__LINE__."\n");
            foreach($this->options as $key => $value){
                print_r("PRODUCT: ".$key." USAGE: ".$value."\n");
                foreach($optionsCloudInstance as $optCloudInstance){
                    //var_dump($optCloudInstance);
                    if($optCloudInstance["name"] == $key){
                        // -----------------------------------
                        // !!!!!REPRISE INSERTION OPTION!!!!!
                        // -----------------------------------
                        if($optCloudInstance["pack_edition_id"] > $packEditionId){
                            $packEditionId = $optCloudInstance["pack_edition_id"]; // save packEdition Id
                        }
                        $queryInstanceOption = "INSERT INTO instance_details VALUES(null, $instanceId, ".$optCloudInstance["id"].");";
                        print "Query: ".$queryInstanceOption."\n";
                        try{
                            $instanceStatement = $this->em->getConnection()->prepare($queryInstanceOption);
                            $instanceStatement->execute();
                        }catch(\Exception $e){
                            print_r("exist already...");
                        }
                        //$instanceIdResult = $instanceStatement->fetchAll();
                        //print("\ninstance id count: ".count($instanceIdResult)."\n");
                    }

                }
            } // TEST IF OPTION EXISTS AND BIND IT TO INSTANCE
            if($packEditionId == 0){
                print_r("Normal Enterprise Edition must be inserted\n");
                foreach($optionsCloudInstance as $element){
                    /*$packEditionId++;
                    print_r($packEditionId." - ");
                    print_r("Pack ed:".$element["pack_edition_id"]);*/
                    if($element["pack_edition_id"] == 2){
                        $queryInstanceOption = "INSERT INTO instance_details VALUES(null, $instanceId, ".$element["id"].");";
                        print "Query: ".$queryInstanceOption."\n";
                        try{
                            $instanceStatement = $this->em->getConnection()->prepare($queryInstanceOption);
                            $instanceStatement->execute();
                        }catch(\Exception $e){
                            print_r("exist already...");
                        }
                    }
                }
            }
            

            // Check if options from XML are used and change it's name to match the ODBCS name option
            // var_dump($this->options);
            $foundUsage = 0;
            /*foreach($options as $key => $value){
                print_r("parse option...");
                if(($value == "PAST_USAGE") || ($value == "PAST_OR_CURRENT_USAGE") || ($value == "CURRENT_USAGE")){
                    if(($key == "Real Application Clusters" || $key == "RAC or RAC One Node" || $key == "Real Application Clusters One Node")){
                        print_r("Value used: ".$key."\n");
                        $options["RAC"] = $value;
                        $key = "RAC";
                        if(array_key_exists("Real Application Clusters", $options)){
                            unset($options["Real Application Clusters"]);
                        }
                        if(array_key_exists("RAC or RAC One Node", $options)){
                            unset($options["RAC or RAC One Node"]);
                        }
                        if(array_key_exists("Real Application Clusters One Node", $options)){
                            unset($options["Real Application Clusters One Node"]);
                        }
                    }
                    if($key == "Database In-Memory"){
                        $options["In-Memory Database"] = $value;
                        $key = "In-Memory Database";
                        unset($options["Database In-Memory"]);
                    }
                    // Test if option exists in db, if yes insert it...
                    if($optionsCloudInstance){
                        foreach($optionsCloudInstance as $optCloudInstance){
                            if($optCloudInstance['name'] === $key){
                                //print_r("option: ".$key." found! \n");
                                print_r("option id: ".$optCloudInstance['id']." \n");
                                print_r("instance id: ".$instanceId." \n");
                                //$result = $this->insertOptionsEdition($optCloudInstance['id'], $instanceId);
                                $foundUsage++;
                            }
                        }
                    }else{
                        print_r("nothing from repository\n");
                    }
                }
            }*/
            /*if($foundUsage === 0){
                //$this->insertOptionsEdition("1", $instanceId, $em);
            }*/
            /*foreach($options as $key => $value){
                print_r("PRODUCT: ".$key." USAGE: ".$value."\n");
            }*/
        }else{
            print_r("not an enterprise edition\n");
        }
    }

    //-----------------------------------
    //----- INSERT OPTION INTO DB -------
    //-----------------------------------
    public function insertOptionsEdition($optionId, $instanceId, $em){
        $msg = "option well inserted!\n";
        try{
            $queryOptionsCloudInstance = "INSERT INTO instance_details VALUES(null, $instanceId, $optionId);";
            print_r($queryOptionsCloudInstance);
            $optionsCloudInstanceStatement = $em->getConnection()->prepare($queryOptionsCloudInstance);
            $optionsCloudInstanceStatement->execute();
        }catch(\Exception $e){
            $msg = "ERROR INSERTING OPTION details:".$e."\n";
        }
        return $msg;
    }


    // GET OPTIONS FROM DB
    public function getOptionsFromCloudInstance($em){
        $queryOptionsCloudInstance = "SELECT * FROM options_cloud_instance;";
        $optionsCloudInstanceStatement = $em->getConnection()->prepare($queryOptionsCloudInstance);
        $optionsCloudInstanceStatement->execute();
        $optionsCloudInstance = $optionsCloudInstanceStatement->fetchAll();
        return $optionsCloudInstance;
    }
}
