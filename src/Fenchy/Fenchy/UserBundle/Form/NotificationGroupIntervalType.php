<?php

namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\UserBundle\Entity\NotificationGroupInterval;

class NotificationGroupIntervalType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                    'interval',
                    'choice',
                    array(
                        'choices' => NotificationGroupInterval::$intervalsMap,
                        'required' => TRUE,
                        'expanded' => TRUE,
                        
            ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\UserBundle\Entity\NotificationGroupInterval'
        ));
    }

    public function getName()
    {
        return 'fenchy_userbundle_usernotificationstype';
    }
}

