<?php
namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('login')
            ->add('password')
        ;
    }

    public function getName()
    {
        return 'fenchy_userbundle_login';
    }
}