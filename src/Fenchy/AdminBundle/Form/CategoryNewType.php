<?php
namespace Fenchy\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
     
        $builder->add('name', 'text',array('required' => TRUE))
		        ->add('sequence', 'text',array('required' => FALSE))
        		->add('locationChangeAvailable', 'hidden', array('data' => true));
    
    	
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	// Which table to add so call its Entity
        $resolver->setDefaults(array(
            'data_class'        => 'Fenchy\NoticeBundle\Entity\Type'
        ));
    }

    public function getName() {      
        return 'fenchy_adminbundle_categoryaddtype';
    }
}
