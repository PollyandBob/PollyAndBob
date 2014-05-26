<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\UserBundle\Form\ProfileFormType as UserType;

class UserBasicSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, array(
                'label' => 'regularuser.firstname',
                'attr' => array('placeholder' => 'regularuser.your_first_name')))
            ->add('lastname', null, array(
                'label' => 'regularuser.lastname',
                'attr' => array('placeholder' => 'regularuser.your_last_name')
                ))
            ->add('gender', 'choice', array(
                'label' => 'regularuser.gender',
                'choices' => UserRegular::$genderMap, 
                'expanded' => true))
            ->add('age', null, array(
                'label' => 'regularuser.age',
                'required' => false,
                'attr' => array('placeholder' => 'regularuser.your_age')))
            ->add('languages', 'text', array(
                'label' => 'regularuser.languages',
                'required' => false,
                'attr' => array('placeholder' => 'regularuser.your_languages')))
            ->add('aboutMe', 'textarea', array(
                'label' => 'regularuser.about_me',
                'required' => false,
                'max_length' => 1000,
                'attr' => array('placeholder' => 'regularuser.about_me_placeholder')));
                
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\RegularUserBundle\Entity\UserRegular'
        ));
    }

    public function getName()
    {
        return 'fenchy_regularuserbundle_userbasicsettingstype';
    }
    
    public function getParent ()
    { 
        return 'form'; 
    }    
}
