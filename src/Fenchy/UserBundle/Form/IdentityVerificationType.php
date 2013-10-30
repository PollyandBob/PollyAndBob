<?php

namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\UserBundle\Form\ProfileFormType as UserType;

class IdentityVerificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'label' => 'Username',
                ))
            ->add('status', null, array(
                'label' => 'Status',                
                )) 
            ->add('created_at', null, array(
            		'label' => 'Create at',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\UserBundle\Entity\IdentityVerification'
        ));
    }

    public function getName()
    {
        return 'fenchy_regularuserbundle_identityverificationtype';
    }
    
    public function getParent ()
    { 
        return 'form'; 
    }    
}
