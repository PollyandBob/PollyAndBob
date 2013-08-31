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
            ->add('reported_only', 'checkbox', array('required' => FALSE))
            ->add('text', 'text', array('required' => FALSE))
            ->add(
                    'created_before',
                    'date',
                    array(
                        'required' => FALSE,
                        'widget' => 'single_text',
                        'format' => 'MM/dd/yyyy'
                        )
                    )
            ->add(
                    'created_after',
                    'date',
                    array(
                        'required' => FALSE,
                        'widget' => 'single_text',
                        'format' => 'MM/dd/yyyy'
                        )
                    )
            ->add(
                    'type',
                    'choice',
                    array(
                        'choices' => array(
                            '0' => 'Negative',
                            '1' => 'Positive'
                            ),
                        'required' => FALSE,
                        )
                    )
            ->add('author', 'text', array('required' => FALSE))
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
            'data_class'        => 'Fenchy\AdminBundle\Entity\ReviewsFilter'
        ));
    }

    public function getName() {      
        return 'fenchy_adminbundle_reviewsfilter';
    }
}
