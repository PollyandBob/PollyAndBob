<?php

namespace Fenchy\NoticeBundle\Form;

use Doctrine\Common\Annotations\Annotation\Required;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Fenchy\NoticeBundle\Entity\Type;
use Fenchy\NoticeBundle\Entity\Notice;
use Fenchy\NoticeBundle\Entity\Value;

class CategoryType extends AbstractType
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
            'data_class' => 'Fenchy\NoticeBundle\Entity\Type',
        ));
    }

    public function getName() {      
        return 'fenchy_noticebundle_categorytype';
    }
}
