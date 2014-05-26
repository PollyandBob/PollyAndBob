<?php

namespace Fenchy\NoticeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class NoticeAdminType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                    'title', 
                    NULL, 
                    array('label' => 'notice.title')
                )                 
            ->add(
                    'start_date', 
                    'datetime', 
                    array(
                        'widget'  =>  'single_text',
                    )
                )    
            ->add(
                    'end_date', 
                    'datetime', 
                    array(
                        'widget'  =>  'single_text',
                    )
                )                 
            ->add(
                    'content', 
                    NULL, 
                    array('label' => 'notice.content')
                )
            ->add(
                    'link', 
                    NULL, 
                    array('label' => 'notice.link')
                );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\NoticeBundle\Entity\Notice'
        ));
    }

    public function getName()
    {
        return 'fenchy_noticebundle_noticeadmintype';
    }
    
    public function getParent () 
    { 
        return 'form'; 
    } 
}
