<?php
namespace Fenchy\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IdentityVerificationFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('username', 'text', array('required' => FALSE))
            ->add('status', 'text', array('required' => FALSE))            
            ->add(
                    'sort', 
                    'choice', 
                    array(
                        'choices' => array(
                            'id'            => 'ID',
                            'username'      => 'User Name',
                            'status'         => 'Status',
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
            'data_class'        => 'Fenchy\AdminBundle\Entity\IdentityVerificationFilter'
        ));
    }

    public function getName() {      
        return 'fenchy_adminbundle_identityverificationfilter';
    }
}
