<?php
namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Form\RegistrationUserRegularType as UR;
use Fenchy\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function __construct($class = 'Fenchy\UserBundle\Entity\User') {
        
        parent::__construct($class);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        parent::buildForm($builder, $options);
        $builder->add('user_regular', new UR());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Fenchy\UserBundle\Entity\User',
            'intention'         => 'Registration',
            'validation_groups' => array('Registration'),
        ));
    }

    public function getName() {      
        return 'fenchy_userbundle_registration';
    }
}
