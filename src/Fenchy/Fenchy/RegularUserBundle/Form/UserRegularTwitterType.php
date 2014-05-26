<?php

namespace Fenchy\RegularUserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\RegularUserBundle\Form\UserRegularLocationType;
use Fenchy\UserBundle\Form\TwitterFormType as UserType;

/**
 * Extension of UserRegularLocationType. Adds userTwitter form to base version.
 * Contain all fields needed for create User and RegularUser.
 * 
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class UserRegularTwitterType extends UserRegularLocationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', new UserType());
        
        parent::buildForm($builder, $options);
    }

}
