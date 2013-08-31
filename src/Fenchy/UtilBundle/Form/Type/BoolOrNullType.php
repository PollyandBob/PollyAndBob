<?php

namespace Fenchy\UtilBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BoolOrNullType extends AbstractType
{
    protected $choices = array();
    
    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'choices' => $this->choices
        );
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'boolornull';
    }
}
