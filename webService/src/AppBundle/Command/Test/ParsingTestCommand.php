<?php
use Symfony\Component\HttpKernel\Kernel;

/**
 * Created by PhpStorm.
 * User: simonvivier
 * Date: 05/12/17
 * Time: 14:05
 */

namespace AppBundle\Command\Test;


use AppBundle\Command\SnapshotReadingCronCommand;
use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Console\Application;
use JMS\JobQueueBundle\Console\CronCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
function array_diff_recursive($aArray1, $aArray2) {
    $aReturn = array();
    foreach ($aArray1 as $mKey => $mValue) {
        if (array_key_exists($mKey, $aArray2)) {
            if (is_array($mValue)) {
                $aRecursiveDiff = array_diff_recursive($mValue, $aArray2[$mKey]);
                if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
            } else {
                if ($mValue != $aArray2[$mKey]) {
                    $aReturn[$mKey] = $mValue;
                }
            }
        } else {
            $aReturn[$mKey] = $mValue;
        }
    }
    return $aReturn;
}
class ParsingTestCommand extends ContainerAwareCommand
{
    protected function configure(){
        $this
            // the name of the command (the part after "bin/console")
            ->setName('test:parser')
            ->addOption('rebuild','r',InputOption::VALUE_NONE,"rebuild the test sample from snapshot:done")
            ->addOption('no-load','u',InputOption::VALUE_NONE,"just compare json files")
            // the short description shown while running "php bin/console list"
            ->setDescription('')
            ->setHelp('')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output){
        $test_dir = $this->getContainer()->getParameter('snapshot_baseDir') . DIRECTORY_SEPARATOR . 'test';
        if($input->getOption('rebuild')) {
           exec($this->getContainer()->getParameter('test_rebuild_command'));
        }
        /* @var $kernel KernelInterface */
        $kernel = $this->getContainer()->get( 'kernel' ) ;
        $application = new Application($kernel);

        $application->add(new SnapshotReadingCronCommand());
        $command = $application->find('snapshot:load-all');
        $commandTester = new CommandTester($command);
        if (!$input->getOption('no-load')){
            $commandTester->execute([
                'command'  => $command->getName(),
                '--sub-dir' => 'test',
                '--keep-here' => true,
                '--json-dump-dir' => $test_dir
            ]);
        }

        $jsonFiles = glob($test_dir.'/*.json');
        foreach($jsonFiles as $jsonFile){
            $json = json_decode(file_get_contents($jsonFile),true);
            $test_json = json_decode(file_get_contents($jsonFile.'.test'),true);
            if($json != $test_json){
                echo "\e[41m\e[39mERROR IN ".$jsonFile." for version ".$json["Base"]["version"];
                echo "\e[0m\n";
                dump(array_diff_recursive($json,$test_json));
            }
        }
        // the output of the command in the console
    }

}