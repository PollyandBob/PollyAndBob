<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\NoticeBundle\Entity\Comment;

class PostCommentType extends AbstractType 
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                    
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\NoticeBundle\Entity\Comment'
        ));
    }

    public function getName()
    {
        return 'fenchy_regularuserbundle_postcommenttype';
    }
    
}
