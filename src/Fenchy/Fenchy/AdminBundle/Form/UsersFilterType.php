<?php
namespace Fenchy\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsersFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reported_only', 'checkbox', array('required' => FALSE))
            ->add('first_name', 'text', array('required' => FALSE))
            ->add('last_name', 'text', array('required' => FALSE))
            ->add('location', 'text', array('required' => FALSE))
            ->add('postcode', 'text', array('required' => FALSE))
            ->add(
                    'sort', 
                    'choice', 
                    array(
                        'choices' => array(
                            'id'            => 'ID',
                            'firstname'      => 'First Name',
                            'lastname'      => 'Last Name',
                            'email'         => 'E-Mail',
                            'lastLogin'    => 'Last Login',
                            'stickersQ'     => 'Stickers',
                        )
                    )
                )
            ->add(
                    'order',
                    'choice',
                    array(
                        'choices' => array(
                            'asc' => 'ASC',
                            'desc' => 'DESC'
                        )
                    )
                );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Fenchy\AdminBundle\Entity\UsersFilter'
        ));
    }

    public function getName() {      
        return 'fenchy_adminbundle_userfilter';
    }
}
