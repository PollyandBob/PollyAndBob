<?php
namespace Fenchy\UserBundle\Form\EventListener;

use Fenchy\RegularUserBundle\Entity\UserRegular;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

/**
 * ProfileTypeSubscriber
 *
 * PHP version 5
 *
 * @category Fenchy
 * @package  Form
 * @author   Grzegorz Wiater <gwiater@pgs-soft.com>
 * @version  SVN: $Id$
 */

/**
 * Profile type subscriber
 *
 * @category Fenchy
 * @package  EventListener
 * @author   Grzegorz Wiater <gwiater@pgs-soft.com>
 */
class ProfileTypeSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    
    /**
     * @see EventSubscriberInterface::getSubscribedEvents()
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_BIND => 'postBind');
    }
    
    /**
     * Creates Regular User entity for User object during registration process
     * Generates row in  user__regular table
     * @param DataEvent $event 
     */
    public function postBind(DataEvent $event)
    {
        $user        = $event->getData();

    }

}