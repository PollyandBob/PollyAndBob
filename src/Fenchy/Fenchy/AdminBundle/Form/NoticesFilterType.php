<?php
namespace Fenchy\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NoticesFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reported_only', 'checkbox', array('required' => FALSE))
            ->add('title', 'text', array('required' => FALSE))
            ->add('type', 'text', array('required' => FALSE))
            ->add(
                    'sort', 
                    'choice', 
                    array(
                        'choices' => array(
                            'id'                => 'ID',
                            'title'             => 'Title',
                            'type'           => 'Type',
                            'user'           => 'Owner',
                            'stickersQ'         => 'Stickers',
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
            'data_class'        => 'Fenchy\AdminBundle\Entity\NoticesFilter'
        ));
    }

    public function getName() {      
        return 'fenchy_adminbundle_noticefilter';
    }
}
