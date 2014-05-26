<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Fenchy\UtilBundle\Form\SettingsLocationType;

class UserGroupLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', new SettingsLocationType(), array('label' => false));             
                
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\RegularUserBundle\Entity\UserGroup'
        ));
    }

    public function getName()
    {
        return 'fenchy_regularuserbundle_usergroup_locationtype';
    }
}
