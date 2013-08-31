<?php
namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
//use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
//use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Fenchy\RegularUserBundle\Form\UserRegularType as UR;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    public function __construct($class= 'Fenchy\UserBundle\Entity\User') {
        
        parent::__construct($class);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildUserForm($builder, $options);
        $builder
            ->remove('username')
            ->add('user_regular', new UR());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Fenchy\UserBundle\Entity\User',
            'intention'         => 'Profile',
            'validation_groups' => array('Profile'),
        ));
    }

    public function getName() {      
        return 'fenchy_userbundle_profile';
    }
}
