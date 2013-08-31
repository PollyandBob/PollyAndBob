<?php

namespace Fenchy\UtilBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\UtilBundle\Form\LocationType as base;

class SettingsLocationType extends base
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('printable', null, array(
                'error_bubbling' => true,
                'label' => 'regularuser.printable_location',
                'attr' => array(
                    'placeholder' => 'regularuser.printable_location'
                )));  
        ;
    }
}
