<?php

namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\UserBundle\Entity\NotificationGroupInterval;
class UserNotificationsType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

                ->add('notification_group_intervals', 'collection', array(
                        'type' => new NotificationGroupIntervalType(),
                        'allow_add' => FALSE,
                        'by_reference' => FALSE,
                  ))

            ->add(
                    'notifications',
                    'entity', 
                    array(
                        'class' => 'UserBundle:Notification',
                        'property' => 'description',
                        'multiple' => TRUE,
                        'expanded' => TRUE,
            ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'fenchy_userbundle_usernotificationstype';
    }
}
