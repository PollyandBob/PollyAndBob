<?php

namespace Fenchy\UtilBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Fenchy\UtilBundle\Entity\Sticker;

class StickerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                    'reason',
                    'choice',
                    array(
                        'choices'   => Sticker::$reasonMap,
                        'required'  => TRUE,
                        'multiple'  => TRUE,
                        'expanded'  => TRUE
                    )
            )
            ->add('description')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\UtilBundle\Entity\Sticker'
        ));
    }

    public function getName()
    {
        return 'fenchy_utilbundle_stickertype';
    }
}
