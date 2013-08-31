<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\UserBundle\Form\UserAdminType as UserType;

class UserRegularAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', new UserType())
            ->add('firstname', null, array(
                'label' => 'regularuser.firstname',
                'attr' => array('watermark' => 'regularuser.your_firstname')))
            ->add('lastname', null, array(
                'label' => 'regularuser.lastname',
                'attr' => array('watermark' => 'regularuser.your_lastname')))
            ->add('aboutMe')
            ->add('link');
                
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\RegularUserBundle\Entity\UserRegular'
        ));
    }

    public function getName()
    {
        return 'fenchy_regularuserbundle_userregulartype';
    }
}
