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
                )))
                ->add('street_number', 'hidden')              
                ->add('route', 'hidden')
                ->add('locality', 'hidden')
                ->add('sublocality', 'hidden')
                ->add('administrative_area_level_1', 'hidden')
                ->add('administrative_area_level_2', 'hidden')
                ->add('postal_code', 'hidden')
                ->add('country', 'hidden')
                ->add('street_address', 'hidden');
        ;
    }
}
