<?php

namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Fenchy\UtilBundle\Form\SettingsLocationType;

class UserLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', new SettingsLocationType(), array('label' => false));                
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'fenchy_userbundle_user_locationtype';
    }
}
