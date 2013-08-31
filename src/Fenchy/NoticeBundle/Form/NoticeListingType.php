<?php

namespace Fenchy\NoticeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Fenchy\NoticeBundle\Entity\Type;
use Fenchy\NoticeBundle\Entity\Notice;
use Fenchy\UtilBundle\Form\SimpleLocationType;

class NoticeListingType extends AbstractType
{
    protected $notice;
    protected $type;
    
    public function __construct(Type $type, Notice $notice) {
        
        $this->type = $type;
        $this->notice = $notice;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', NULL, array('label' => 'notice.title'))
            ->add('content', NULL, array('label' => 'notice.content'))    
            ->add('start_date', 'datetime', array(
                            'widget'  =>  'single_text',
                            'format' => 'd.MM.y HH:mm',
                            'data' => isset($options['data']) ? $options['data']->getStartDate() : '',
                            'label' => 'listing.create.start_date'
                    ))
            ->add('end_date', 'datetime', array(
                            'widget' => 'single_text',
                            'format' => 'd.MM.y HH:mm',
                            'data' => isset($options['data']) ? $options['data']->getEndDate() : '',
                            'label' => 'listing.create.end_date'
                    ))
            ->add(
                    'type',
                    new TypeType($this->type, $this->notice)
                )
            ->add('tags', NULL, array('label' => 'notice.tags'));
        
        if($this->notice->getUser()->getRegularUser()->getFacebookPublish()) {
            $builder->add('put_on_fb', 'checkbox', array(
                            'label'     => 'regularuser.put_on_fb', 
                            'required'  => false,
                            'data'      => true
                    ));
        }
        if($this->type->isLocationChangeAvailable()) {
            $builder->add('location', new SimpleLocationType());
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\NoticeBundle\Entity\Notice'
        ));
    }

    public function getName()
    {
        return 'fenchy_noticebundle_noticetype';
    }
    
    public function getParent () 
    { 
        return 'form'; 
    } 
}
