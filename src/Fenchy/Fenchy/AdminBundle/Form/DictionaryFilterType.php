<?php
namespace Fenchy\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DictionaryFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'text', array('required' => FALSE))
            ->add('search_quantity_less', 'text', array('required' => FALSE, 'label' => 'Searched less'))
            ->add('search_quantity_more', 'text', array('required' => FALSE, 'label' => 'Searched more'))
            ->add('tag_quantity_less', 'text', array('required' => FALSE, 'label' => 'Tagged less'))
            ->add('tag_quantity_more', 'text', array('required' => FALSE, 'label' => 'Tagged more'))
            ->add('tag', 'boolornull', array('required' => FALSE, 'label' => 'Is Tag'))
            ->add('search_activated', 'boolornull', array('required' => FALSE, 'label' => 'Search'))
            ->add('tag_activated', 'boolornull', array('required' => FALSE, 'label' => 'Tag'))
            ->add('disabled', 'boolornull', array('required' => FALSE))
            ->add(
                    'sort', 
                    'choice', 
                    array(
                        'choices' => array(
                            'id'                => 'ID',
                            'text'              => 'Text',
                            'tag'               => 'Is Tag',
                            'quantitySearch'    => 'Search Quantity',
                            'quantityTag'       => 'Tag Quantity',
                            'search_activated'  => 'Search Activated',
                            'tagActivated'     => 'Tag Activated',
                            'disabled'          => 'Disabled'
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
            'data_class'        => 'Fenchy\AdminBundle\Entity\DictionaryFilter'
        ));
    }

    public function getName() {      
        return 'fenchy_adminbundle_dictionaryfilter';
    }
}
