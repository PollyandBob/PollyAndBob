<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\Shareholder;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormError;

class ShareholderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, array(
                'label' => 'regularuser.firstname',
                'attr' => array('placeholder' => 'regularuser.your_firstname')))
            ->add('surname', null, array(
                'label' => 'regularuser.lastname',
                'attr' => array('placeholder' => 'regularuser.your_lastname')))
            ->add('street', null, array(
            	'label' => 'regularuser.street',
                'attr' => array('placeholder' => 'regularuser.street')))                
            ->add('city', null, array(
                'label' => 'regularuser.city',
                'attr' => array('placeholder' => 'regularuser.city')))
            ->add('postalcode', null, array(
            	'label' => 'regularuser.postcode1',
                'attr' => array('placeholder' => 'regularuser.postcode1')))                		
            ->add('country', null, array(
                'label' => 'regularuser.country',
                'attr' => array('placeholder' => 'regularuser.country')))
            ->add('birthday', null, array(            	
            	'widget' => 'single_text',
            	'format' => 'd.MM.y',            	
            	'label' => 'regularuser.birthdate',
            	'required' => false,
            	'attr' => array('placeholder' => 'regularuser.birthdate')))     
            ->add('email', 'email', array(
                		'label' => 'regularuser.em',
                		'attr' => array('placeholder' => 'regularuser.em')))
          	->add('share', null)
          	->add('price', null)
          	->add('totalamount', null)
            ->add('terms', 'checkbox', array(           		
            		'required'  => true,
            ));
            $builder->addEventListener(FormEvents::POST_BIND, function (DataEvent $event) {
            	$form = $event->getForm();            	 
            	if (intval($form['share']->getData() >1000) || intval($form['share']->getData()) < 1 ) {
            		$form['share']->addError(new FormError('1 to max. 1000'));
            	}
            });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\RegularUserBundle\Entity\Shareholder'
        ));
    }

    public function getName()
    {
        return 'fenchy_regularuserbundle_sharehodertype';
    }
}
