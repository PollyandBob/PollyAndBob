<?php
namespace Fenchy\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;



class NotificationsCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {
        $this
        ->setName('fenchy:user:notifications') // determines, among others, 
                                               // in which tree section a commandw shows up
                                               // in help after running
                                               // app/console without arguments
        ->setDescription('Pocess pending user notifications');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $queue = $em->getRepository('UserBundle:NotificationQueue')->findAll();
                
        $mailer = $this->getContainer()->get('mailer');
        $now = new \DateTime;
        
        foreach( $queue as $one ) {
            if ( $one->getSendAfter() < $now ) {
                $emailMessage = \Swift_Message::newInstance()
                    ->setFrom( $one->getFromAddress(), $one->getFromName() )
                    ->setTo( $one->getToAddress() )
                    ->setSubject( $one->getSubject() )
                    ->setBody( $one->getBodyHtml(), 'text/html')
                    ->addPart( $one->getBodyPlain(), 'text/plain');
                $mailer->send($emailMessage);
                $em->remove($one);
            }
        }
        $em->flush();
    }
    
}
