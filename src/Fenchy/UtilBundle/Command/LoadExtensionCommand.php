<?php
namespace Fenchy\UtilBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class LoadExtensionCommand extends DoctrineCommand
{
    protected function configure()
    {
        $this->setName('fenchy:util:loadExtension')
            ->setDescription('Loads sql extensions');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->getDoctrineConnection(NULL);

        $db->exec("CREATE EXTENSION pg_trgm;");
        $db->exec("CREATE EXTENSION postgis;");
        $db->exec("CREATE EXTENSION postgis_topology;");

    }
}