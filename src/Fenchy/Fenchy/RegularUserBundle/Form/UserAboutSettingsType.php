<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\UserBundle\Form\ProfileFormType as UserType;

class UserAboutSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, array(
                'label' => 'regularuser.firstname',
                'attr' => array('placeholder' => 'regularuser.your_first_name')))
            ->add('lastname', null, array(
                'label' => 'regularuser.lastname',
                'attr' => array('placeholder' => 'regularuser.lastname')
                ))
            ->add('gender', 'choice', array(
                'label' => 'regularuser.gender',
                'choices' => UserRegular::$genderMap, 
                'expanded' => true,
            	'required' => false))
            ->add('age', null, array(
                'label' => 'regularuser.age',
                'required' => false,
                'attr' => array('placeholder' => 'regularuser.your_age')))
            ->add('languages', 'text', array(
                'label' => 'regularuser.your_languages',
                'required' => false,
                'attr' => array('placeholder' => 'regularuser.your_languages')))
            ->add('birthday', null, array(
            	
            	'widget' => 'single_text',
            	'format' => 'd.MM.y',            	
            	'label' => 'regularuser.birthdate',
            	'required' => false,
            	'attr' => array('placeholder' => 'regularuser.birthdate')))                
            ->add('mylike', 'text', array(
            	'label' => ' ',
            	'required' => false,
            	'attr' => array('placeholder' => 'regularuser.i_like')))
           	->add('hotplaces', 'text', array(
         		'label' => ' ',
         		'required' => false,
          		'attr' => array('placeholder' => 'regularuser.hot_places')))
          	->add('promotedinitiatives', 'text', array(
          		'label' => ' ',
          		'required' => false,
          		'attr' => array('placeholder' => 'regularuser.promoted_initiaives')))
          	->add('notshowninfo', 'text', array(
          		'label' => ' ',
          		'required' => false,
          		'attr' => array('placeholder' => 'regularuser.not_shown_information')))
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
        return 'fenchy_regularuserbundle_useraboutsettingstype';
    }
    
    public function getParent ()
    { 
        return 'form'; 
    }    
}
