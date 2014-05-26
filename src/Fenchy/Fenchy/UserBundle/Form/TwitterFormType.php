<?php
namespace Fenchy\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Fenchy\RegularUserBundle\Form\UserRegularNameLocationType;
use Fenchy\UtilBundle\Form\LocationType;

/**
 * Form type for "twitter registration". Contain User data and additional hidden
 * twitter fields.
 * 
 * @uses After successfull twitter oAuth for unexisting user.
 * 
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class TwitterFormType extends BaseType
{

    /**
	 * @var boolean $require_username
	 * 
	 * A parameter passed from constructor to determine whether to display
	 * username filed in form (marked as invalid) or not. Happen when
	 * disconnected Twitter user wants to connect again.
	 */
    protected $require_username;

    public function __construct($require_username = FALSE )
    {
    	$this->require_username = $require_username;
        parent::__construct('Fenchy\UserBundle\Entity\User');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('username');

		if ( $this->require_username ) {
			$builder->add('username',NULL,array('attr'=>array('class'=>'artificial-invalid')));
		} else {
		    $builder->add('username','hidden');
		}			
				
		$builder
			->remove('plainPassword')
			->add('regular_user', new UserRegularNameLocationType(), array(
                            'label' => false
                        ))
                        ->add('location', new LocationType())
            ->add('twitterId', 'hidden')
            ->add('twitter_username', 'hidden');

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
        return 'fenchy_userbundle_twitter';
    }

}
