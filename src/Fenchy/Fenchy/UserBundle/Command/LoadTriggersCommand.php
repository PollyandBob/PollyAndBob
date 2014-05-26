<?php
namespace Fenchy\UserBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class LoadTriggersCommand extends DoctrineCommand
{
    protected function configure()
    {
        $this->setName('fenchy:user:loadTriggers')
            ->setDescription('Loads triggers for user - notify_groups relation');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->getDoctrineConnection(NULL);

        // load functions
        $dir = $this->getContainer()->getParameter('kernel.root_dir').'/../sql/functions';
        $files = scandir($dir);
        $data = '';
        
        foreach($files as $key => $file)
        {
            if(strstr($file, '.sql'))
            {
                $data = file_get_contents($dir.'/'.$file);
                $stmt = $db->exec($data);
            }
        }
        
        // load triggers
        $dir = $this->getContainer()->getParameter('kernel.root_dir').'/../sql/triggers';
        $files = scandir($dir);
        $data = '';
        
        foreach($files as $key => $file)
        {
            if(strstr($file, '.sql'))
            {
                $data = file_get_contents($dir.'/'.$file);
                $stmt = $db->exec($data);
            }
        }
        
        
    }
}