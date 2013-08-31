<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\NoticeBundle\Entity\Review;

class LeaveReviewProfileType extends LeaveReviewType 
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'max_length'=>255,
                'label' => 'regularuser.reviews.form.title',
                'constraints'=>array(
                    new NotBlank(),
                    new Length(array('min'=>2,'max'=>255)),
                ),
            ));
        parent::buildForm($builder, $options);
    }

    
}
