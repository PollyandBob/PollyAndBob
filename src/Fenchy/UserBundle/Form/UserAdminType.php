<?php
namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locked', NULL, array('required' => FALSE))
            ->add('email')
       		->add('managertype', 'checkbox', array('required' => FALSE));
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
