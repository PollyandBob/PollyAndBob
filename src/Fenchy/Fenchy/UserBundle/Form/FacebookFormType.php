<?php
namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Fenchy\RegularUserBundle\Form\UserRegularNameLocationType;

/**
 * Form type for "facebook registration". Created to handle situation, 
 *  when some previously disconnected Facebook user tries to sign in
 *  with Facebook
 * 
 * 
 * @author Józef Tokarski <jtokarski@pgs-soft.com>
 */
class FacebookFormType extends BaseType
{

    /**
	 * @var boolean $require_username
	 * 
	 * A parameter passed from constructor to determine whether to display
	 * username filed in form (marked as invalid) or not. Happen when
	 * disconnected Facebook user wants to connect again.
	 */
    protected $require_username_email;

    public function __construct($require_username_email = FALSE )
    {
    	$this->require_username_email = $require_username_email;
        parent::__construct('Fenchy\UserBundle\Entity\User');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('username');
		$builder->remove('email');

		if ( $this->require_username_email ) {
			$builder->add('email', NULL, array('attr'=>array('class'=>'artificial-invalid')) );
			$builder->add('username',NULL, array('attr'=>array('class'=>'artificial-invalid')));
		} else {
		    $builder->add('username','hidden');
		}			
				
		$builder
			->remove('plainPassword')
			->add('regular_user', new UserRegularNameLocationType);
			
		$builder 
		    ->remove('facebookId')
		    ->add('facebookId', 'hidden');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\UserBundle\Entity\User',
            'intention'  => 'registration',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'fenchy_userbundle_facebook';
    }

}
