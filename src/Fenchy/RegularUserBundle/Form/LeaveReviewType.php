<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\NoticeBundle\Entity\Review;

class LeaveReviewType extends AbstractType 
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'textarea', array(
                'max_length'=>1000,
                'constraints'=>array(
                    new NotBlank(),
                    new Length(array('min'=>2,'max'=>1000)),
                ),
            ))
            ->add('type', 'choice', array(
                'choices' => array(
                    '0' => 'negative',
                    '1' => 'positive'
                    ),
                'multiple' => false,
                'label' => 'review_type'
                ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\NoticeBundle\Entity\Review'
        ));
    }

    public function getName()
    {
        return 'fenchy_regularuserbundle_leavereviewtype';
    }
    
}
