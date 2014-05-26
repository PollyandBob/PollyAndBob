<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\RegularUserBundle\Entity\UserGroup;
use Fenchy\UserBundle\Form\ProfileFormType as UserType;

class UserGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groupname', null, array(
                'attr' => array('watermark' => 'regularuser.your_groupname')))
            ->add('aboutGroup', 'textarea', array(
                'attr' => array('watermark' => 'regularuser.your_aboutgroup')))
            ->add('status', 'choice', array(
                'label' => 'regularuser.status',
                'choices' => UserGroup::$statusMap))
            ->add('file',null,array('label' => 'settings.general.profile_photo'))
            ->add('file2',null,array('label' => 'settings.general.cover_photo'));
                
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\RegularUserBundle\Entity\UserGroup'
        ));
    }

    public function getName()
    {
        return 'fenchy_regularuserbundle_usergroup';
    }
}
