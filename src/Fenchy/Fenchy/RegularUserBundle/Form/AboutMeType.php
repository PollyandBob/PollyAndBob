<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\UserRegular;

class AboutMeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                    'aboutMe', 
                    NULL,
                    array(
                        'attr' => array(
                            'watermark' => 'regularuser.about_you', 

                            ),
                        'label' => ' '
                        )
                    )
            ->add(
                    'link',
                    NULL,
                    array(
                        'attr' => array(
                            'watermark' => 'regularuser.link_to_your_page'
                            
                            ),
                        'label' => ' '
                        )
                    )
        ;
                
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\RegularUserBundle\Entity\UserRegular'
        ));
    }

    public function getName()
    {
        return 'fenchy_about_me';
    }
}
