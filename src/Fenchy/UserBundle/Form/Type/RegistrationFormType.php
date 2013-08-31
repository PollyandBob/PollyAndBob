<?php
namespace Fenchy\UserBundle\Form\Type;

use Fenchy\UserBundle\Form\EventListener\ProfileTypeSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Fenchy\RegularUserBundle\Form\RegistrationUserRegularType as UR;

/**
 * RegistrationFormType
 *
 * PHP version 5
 *
 * @category Fenchy
 * @package  Form
 * @author   Grzegorz Wiater <gwiater@pgs-soft.com>
 * @version  SVN: $Id$
 */
class RegistrationFormType extends BaseType
{
    
    public function __construct($class= 'Fenchy\UserBundle\Entity\User') {
        
        parent::__construct($class);
    }    
    /**
     * @see FormBuilderInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new ProfileTypeSubscriber($builder->getFormFactory());

        $builder->add('user_regular', new UR())
            ->add('email', NULL, array(
			    'label' => 'user.email',
                            'attr' => array(
                                'placeholder' => 'form.email'
                            ),
                'invalid_message' => 'form.validation.invalid_email'
			))
            ->add('plainPassword', 'repeated', array(
                'type'           => 'password',
                'options'        => array('translation_domain' => 'FOSUserBundle'),
                'first_options'  => array('label' => 'form.password', 'attr' => array('placeholder' => 'form.password'), 'invalid_message' => 'form.validation.invalid_password'),
                'second_options' => array('label' => 'form.password_confirmation', 'attr' => array('placeholder' => 'form.password_confirmation'), 'invalid_message' => 'form.validation.invalid_password2'),
                'attr' => array(
                    'placeholder' => 'form.password'
                ),
                'invalid_message' => 'form.validation.invalid_pass_match'
            ))
            ->addEventSubscriber($subscriber)
        ;
    }

    /**
     * Get registration form name
     * @return string 
     */
    public function getName()
    {
        return 'fenchy_userbundle_registration';
    }
}