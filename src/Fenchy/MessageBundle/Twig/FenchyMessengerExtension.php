<?php

namespace Fenchy\MessageBundle\Twig;

class FenchyMessengerExtension extends \Twig_Extension
{
    private $messenger;
    
    public function __construct($messenger)
    {
        $this->messenger = $messenger;
    }
    
    public function getFunctions()
    {
        return array(
            'countUnread' => new \Twig_Function_Method($this, 'countUnread')
        );
    }
    
    public function countUnread()
    {
        return $this->messenger->countUnread();
    }

    public function getName()
    {
        return 'fenchy_messenger_extension';
    }
}