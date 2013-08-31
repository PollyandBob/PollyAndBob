<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\UserRegular;


class UserRegularNameLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', NULL, array(
                'required'       => TRUE,
                'error_bubbling' => TRUE,
                'label'          => 'regularuser.firstname',
                'attr'           => array('watermark' => 'regularuser.your_firstname')
            ))
            ->add('lastname', NULL, array(
                'required' => false,
                'label' => 'regularuser.lastname',
            ));
        
        parent::buildForm($builder, $options);
                
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\RegularUserBundle\Entity\UserRegular'
        ));
    }

    public function getName()
    {
        return 'fenchy_regularuserbundle_userregular_namelocationtype';
    }
}
