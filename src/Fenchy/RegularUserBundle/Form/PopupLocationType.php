<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\UserRegular;

class PopupLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', null, array(
                'error_bubbling' => true,
                'label' => 'regularuser.country',
                'attr' => array('watermark' => 'regularuser.your_country')))        
            ->add('postcode', null, array(
                'required' => true, 
                'error_bubbling' => true, 
                'label' => 'regularuser.postcode',
                'attr' => array('watermark' => 'regularuser.your_postal_code')))
            ->add('city', null, array(
                'required' => true, 
                'error_bubbling' => true, 
                'label' => 'regularuser.city',
                'attr' => array('watermark' => 'regularuser.your_city')))
            ->add('street', null, array(
                'error_bubbling' => true,
                'label' => 'regularuser.street',
                'attr' => array('watermark' => 'regularuser.your_street')))
            ->add('latitude', 'hidden')                
            ->add('longitude', 'hidden');
                
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\RegularUserBundle\Entity\UserRegular'
        ));
    }

    public function getName()
    {
        return 'fenchy_regularuserbundle_userregular_popup_locationtype';
    }
}
