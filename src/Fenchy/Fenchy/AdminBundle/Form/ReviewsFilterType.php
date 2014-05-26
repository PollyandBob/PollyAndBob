<?php
namespace Fenchy\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReviewsFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'text', array('required' => FALSE))
            ->add('author', 'text', array('required' => FALSE))
            ->add('receiver', 'text', array('required' => FALSE))
            ->add(
                    'target',
                    'choice',
                    array(
                        'choices' => array(
                                'user'   => 'User',
                                'notice' => 'Notice'
                            ),
                        'required' => FALSE,
                        )
                    )
            ->add(
                    'sort', 
                    'choice', 
                    array(
                        'choices' => array(
                            'id'                => 'ID',
                            'created_at'        => 'Created at',
                            'author'            => 'Author',
                            'receiver'          => 'Target User'
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
            'data_class'        => 'Fenchy\AdminBundle\Entity\ReviewsFilter'
        ));
    }

    public function getName() {      
        return 'fenchy_adminbundle_reviewsfilter';
    }
}
