<?php

namespace Fenchy\UtilBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SimpleLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', 'hidden')
            ->add('latitude', 'hidden')                
            ->add('longitude', 'hidden');
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\UtilBundle\Entity\Location'
        ));
    }

    public function getName()
    {
        return 'fenchy_utilbundle_locationtype';
    }
}
