<?php
namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotNull;

class EmailInvitationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('email1', 'text', array(
                'constraints' => array(new Email(), new NotNull()),
                'attr' => array(
                    'watermark' => 'user.email_1'
                    ),
                'label' => ' ')
                  )
            ->add('email2', 'text', array(
                'constraints' => array(new Email()),
                'required' => false,
                'attr' => array(
                    'watermark' => 'user.email_2'
                    ),
                'label' => ' ')
                  )
            ->add('email3', 'text', array(
                'constraints' => array(new Email()),
                'required' => false,
                'attr' => array(
                    'watermark' => 'user.email_3'
                    ),
                'label' => ' ')
                  ) 
            ->add('note', 'textarea', array(
                'required' => false,
                'attr' => array(
                    'watermark' => 'user.personal_note_optional'
                    ),
                'label' => ' ')
                  );
    }

    public function getName()
    {
        return 'fenchy_userbundle_email';
    }
}