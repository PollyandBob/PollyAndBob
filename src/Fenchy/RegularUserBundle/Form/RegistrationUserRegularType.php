<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\UserBundle\Form\ProfileFormType as UserType;

class RegistrationUserRegularType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', NULL, array('label' => 'regularuser.firstname', 'attr' => array('placeholder' => 'regularuser.firstname'), 'invalid_message' => 'regularuser.validation.invalid__firstname'))
        ;
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
