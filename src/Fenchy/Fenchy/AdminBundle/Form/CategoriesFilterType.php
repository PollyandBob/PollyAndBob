<?php
namespace Fenchy\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoriesFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
     
        $builder->add('name', 'text',array('required' => FALSE))
		        ->add(
		        		'sort',
		        		'choice',
		        		array(
		        				'choices' => array(
		        						'id'                => 'ID',
		        						'name'        => 'Name',
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
            'data_class'        => 'Fenchy\AdminBundle\Entity\CategoriesFilter'
        ));
    }

    public function getName() {      
        return 'fenchy_adminbundle_categoriesfiltertype';
    }
}
