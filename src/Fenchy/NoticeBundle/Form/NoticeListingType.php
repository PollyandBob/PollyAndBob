<?php

namespace Fenchy\NoticeBundle\Form;

use Fenchy\RegularUserBundle\Form\UserGroupType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Fenchy\NoticeBundle\Entity\Type;
use Fenchy\NoticeBundle\Entity\Notice;
use Fenchy\UtilBundle\Form\SimpleLocationType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormError;

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
            ->add('title', NULL, array('label' => 'notice.title', 'max_length'=>85 ))
            ->add('content', NULL, array('label' => 'notice.content'))    
                               
            ->add('currency', 'choice', array(
                    		'choices'   => array(
					        	'Euro'   => 'Euro',
                    			'Dollar' => 'Dollar',
					    ),
                    ))            
            ->add(
                    'type',
                    new TypeType($this->type, $this->notice)
                )
            ->add('tags', NULL, array('label' => 'notice.tags1'));
            
        
        if($this->notice->getUser()->getRegularUser()->getFacebookPublish()) {
            $builder->add('put_on_fb', 'checkbox', array(
                            'label'     => 'regularuser.put_on_fb', 
                            'required'  => false,
                            'data'      => true
                    ));
        }
        
        if($this->type->isDateAvailable() && ($this->type == 'offerevents' || $this->type == 'events')) {
        	$builder->add('start_date', 'date', array(
		        			'widget' => 'single_text',
		        			'format' => 'd.MM.y',
		        			'data' => isset($options['data']) ? $options['data']->getStartDate() : '',
		        			'label' => 'listing.create.start_date',
		        			'required' => true,
        			))
		        	->add('end_date', 'date', array(
		        			'widget' => 'single_text',
		        			'format' => 'd.MM.y',
		        			'data' => isset($options['data']) ? $options['data']->getEndDate() : '',
		        			'label' => 'listing.create.end_date',
		        			'required' => true,
		        	))
		        	->add('start_time', 'time', array(
				            'widget' => 'single_text',
		                    'label' => 'listing.create.start_time',
		            		'required' => false,
		                    ))                    
		           	->add('end_time', 'time', array(
		                    //'input'  => 'datetime',
		                    'widget' => 'single_text',                    		
		                    'label' => 'listing.create.end_time',
		           			'required' => false,
		                    ));
        }
        if($this->type->isDateAvailable() && $this->type == 'events') {
            $builder->add('start_time_arrange', 'checkbox', array(                    		
		                    		'label' => 'listing.create.start_time_arrange',
		            				'required'  => false,
		                    ));
        }
        if($this->type->isDateAvailable() && $this->type != 'offerevents' && $this->type != 'events') {
        	$builder->add('start_date', 'date', array(
                            'widget' => 'single_text',
                            'format' => 'd.MM.y',
                            'data' => isset($options['data']) ? $options['data']->getStartDate() : '',
                            'label' => 'listing.create.start_date',
            				'required' => false,
                    ))           
		            ->add('end_date', 'date', array(
				            		'widget' => 'single_text',
		                            'format' => 'd.MM.y',
		                            'data' => isset($options['data']) ? $options['data']->getEndDate() : '',
		                            'label' => 'listing.create.end_date',
		            				'required' => false,
		                    ))
		            ->add('start_time', 'time', array(
		                    		'widget' => 'single_text',
		                            
		                    		'label' => 'listing.create.start_time',
		            				'required' => false,
		                    ))                    
		           	->add('end_time', 'time', array(
		                    		//'input'  => 'datetime',
		                    		'widget' => 'single_text',                    		
		                    		'label' => 'listing.create.end_time',
		           					'required' => false,
		                    ))
		            ->add('date_arrange', 'checkbox', array(
		                    		'label' => 'listing.create.date_arrange',
		            				'required'  => false,
		                    ))
		            ->add('end_date_arrange', 'checkbox', array(
		                    		'label' => 'listing.create.date_arrange',
		                    		'required'  => false,
		                    ))
		            ->add('start_time_arrange', 'checkbox', array(                    		
		                    		'label' => 'listing.create.start_time_arrange',
		            				'required'  => false,
		                    ))
		            ->add('end_time_arrange', 'checkbox', array(
		                    		'label' => 'listing.create.end_time_arrange',
		            				'required'  => false,
		                    ));
        }
        
        if($this->type->isLocationChangeAvailable()) {
            $builder->add('location', new SimpleLocationType(),array(
            		'required'  => false))
           			->add('location_arrange', 'checkbox', array(
                    		'label' => 'listing.create.location_arrange',
            				'required'  => false,
                    ));
        }
        
        if($this->type->isSpotAvailable()) {
        	$builder->add('spot', 'text', array(
                    		'label' => 'listing.create.spot',
                    		'required'  => false,
                    ))
        			->add('unlimited', 'checkbox', array(
                    		'label' => 'listing.create.unlimited',
                    		'required'  => false,
                    )) ;
        }
        
        if($this->type->isPriceAvailable()) {
        	$builder->add('price', 'text', array(
                    		'label' => 'listing.create.price',
                    		'required'  => false,
                    ))
        			->add('free', 'checkbox', array(
                    		'label' => 'listing.create.free',
                    		'required'  => false,
                    ))
        			->add('default_setting', 'checkbox', array(
        					'label' => 'listing.create.default_setting',
        					'required'  => false,
        			));
        }
        
        if($this->type->isPieceAvailable()) {
        	$builder->add('pieces', 'text', array(
                    		'label' => 'listing.create.pieces',
                    		'required'  => false,
                    ))
        			->add('one_piece', 'checkbox', array(
                    		'label' => 'listing.create.one_piece',
                    		'required'  => false,
                  	));
        }
        
        $builder->addEventListener(FormEvents::POST_BIND, function (DataEvent $event) {
        	$form = $event->getForm();
        	if($this->type->isDateAvailable() && $this->type != 'offerevents' && $this->type != 'events')
        	{        	
// 	        	if (!$form['start_date']->getData() && !$form['end_date']->getData() && !$form['date_arrange']->getData()) {
// 	        		$form['date_arrange']->addError(new FormError('Enter either date or check checkbox'));
// 	        	}
// 	        	if ($form['start_date']->getData() && $form['date_arrange']->getData() && $form['end_date']->getData()) {
// 	        		$form['date_arrange']->addError(new FormError('Enter date or check checkbox'));
// 	        	}
	        	
// 	        	if ($form['start_date']->getData() && !$form['end_date']->getData()) {
// 	        		$form['date_arrange']->addError(new FormError('Enter both date or check checkbox'));
// 	        	}
// 	        	if (!$form['start_date']->getData() && $form['end_date']->getData()) {
// 	        		$form['date_arrange']->addError(new FormError('Enter both date or check checkbox'));
// 	        		$form['start_date']->addError(new FormError('required'));	        	
// 	        	}
	        	
// 	        	if (!$form['start_time']->getData() && !$form['end_time']->getData() && !$form['start_time_arrange']->getData()) {
// 	        		$form['start_time_arrange']->addError(new FormError('Enter either time or check checkbox'));
	        	
// 	        	}
// 	        	if ($form['start_time']->getData() && $form['end_time']->getData() && $form['start_time_arrange']->getData()) {
// 	        		$form['start_time_arrange']->addError(new FormError('Enter either time or check checkbox'));
// 	        	}
	        	
//         		if ($form['start_time']->getData() && !$form['end_time']->getData()) {
// 	        		$form['start_time_arrange']->addError(new FormError('Enter both time or check checkbox'));

// 	        	}
// 	        	if (!$form['start_time']->getData() && $form['end_time']->getData()) {
// 	        		$form['start_time_arrange']->addError(new FormError('Enter both time or check checkbox'));
        	
// 	        	}
	        	
// 	        	if ($form['start_date']->getData()!= "" && !$form['date_arrange']->getData())
// 	        	{
// 	        		$todays_date = date("d-m-Y");
	        		
// 	        		$date = $form['start_date']->getData()->format('d-m-Y');
	        		
// 	        		if(strtotime($date) < strtotime($todays_date))
// 	        		{
// 	        			$form['start_date']->addError(new FormError('Must greater then today'));
// 	        		}
// 	        	}
// 	        	if ($form['start_date']->getData() != "" && $form['end_date']->getData()!= "" && !$form['end_date_arrange']->getData())
// 	        	{
// 	        		$start_date = $form['start_date']->getData()->format('d-m-Y');
// 	        		$end_date = $form['end_date']->getData()->format('d-m-Y');
	        	
// 	        		if(strtotime($start_date) > strtotime($end_date))
// 	        		{
// 	        			$form['end_date']->addError(new FormError('Must greater then start date'));
// 	        		}
// 	        	}
// 	        	if($form['start_date']->getData() == "" && $form['end_date']->getData()!="")
// 	        	{
// 	        		$form['end_date']->addError(new FormError('Must enter start date then you can enter end date'));
// 	        	}
	        	
// 	        	if(($form['start_date']->getData() == "" || $form['start_time']->getData() == "" || $form['end_date']->getData() == "") && $form['end_time']->getData() != "")
// 	        	{
// 	        		$form['end_time']->addError(new FormError('Must enter start date and time then you can enter end date and time'));
// 	        	}

// 	        	if($form['start_date']->getData() == "" && $form['start_time']->getData() != "")
// 	        	{
// 	        		$form['start_date']->addError(new FormError('Must enter date then you can enter time'));
// 	        	}
        	} 
        	
//         	if($this->type->isDateAvailable() && ($this->type == 'offerevents' || $this->type == 'events'))
//         	{
//         		if ($form['start_date']->getData()!= "")
//         		{
//         			$todays_date = date("d-m-Y");
        			 
//         			$date = $form['start_date']->getData()->format('d-m-Y');
        			 
//         			if(strtotime($date) < strtotime($todays_date))
//         			{
//         				$form['start_date']->addError(new FormError('Must greater then today'));
//         			}
//         		}
//         		if ($form['start_date']->getData() != "" )
//         		{
//         			$start_date = $form['start_date']->getData()->format('d-m-Y');
//         			$end_date = $form['end_date']->getData()->format('d-m-Y');
        		
//         			if(strtotime($start_date) > strtotime($end_date))
//         			{
//         				$form['end_date']->addError(new FormError('Must greater then start date'));
//         			}
//         		}
        		
//         	}
                
                if($this->type->isDateAvailable() && ($this->type == 'offerevents' || $this->type == 'events'))
         	{
         		if ($form['start_time']->getData()!= "" && $form['end_time']->getData()!="")
         		{
         			$todays_date = date("H:m");
        			
         			$startTime = $form['start_time']->getData()->format('H:i');
                                $endTime= $form['end_time']->getData()->format('H:i');
        			 
         			if(strtotime($startTime) > strtotime($endTime))
         			{
         				$form['start_date']->addError(new FormError('createlisting.end_time'));
         			}
         		}
//         		if ($form['start_date']->getData() != "" )
//         		{
//         			$start_date = $form['start_date']->getData()->format('d-m-Y');
//         			$end_date = $form['end_date']->getData()->format('d-m-Y');
//        		
//         			if(strtotime($start_date) > strtotime($end_date))
//         			{
//         				$form['end_date']->addError(new FormError('Must greater then start date'));
//         			}
//         		}
        		
         	}
        	
        	if($this->type=='events' || $this->type=='help' || $this->type=='offerhelp' || $this->type=='others' || $this->type=='offerothers' ||  $this->type=='neighbours' ||  $this->type=='groups')
        	{
        		if(!$form['type']['value_'.$this->type->getSubcategoryId()]['value']->getData())
        		{
        			$form['type']->addError(new FormError('createlisting.please_check'));
        		}
        	}  
        	if($this->type=='offerservice')
        	{
        		if(!$form['type']['value_'.$this->type->getSubcategoryId()]['value']->getData())
        		{
        			$form['type']->addError(new FormError('createlisting.please_check_one'));
        		}
        	}
        	if($this->type=='service')
        	{
        		if(!$form['type']['value_'.$this->type->getSubcategoryId()]['value']->getData())
        		{
        				$form['type']->addError(new FormError('createlisting.please_check_one'));
        			
        		}
        	}
        	if($this->type=='offerevents')
        	{
        		if(!$form['type']['value_'.$this->type->getSubcategoryId()]['value']->getData())
        		{
        			$form['type']->addError(new FormError('createlisting.please_check_one'));
        			 
        		}
        	}
        	if($this->type=='goods')
        	{
        		if(!$form['type']['value_'.$this->type->getSubcategoryId()]['value']->getData())
        		{
        			$form['type']->addError(new FormError('createlisting.please_check_one'));
        		}
        	}
        	if($this->type=='offergoods')
        	{
        		if($form['type']['value_'.$this->type->getSubcategoryId()]['value']->getData())
	        	{
	        		$array_values = $form['type']['value_'.$this->type->getSubcategoryId()]['value']->getData();
	        		if(count($array_values) < 1 )
	        		{
	        			$form['type']->addError(new FormError('createlisting.please_check_one'));
	        		}
	        		elseif (count($array_values) > 2 )
	        		{
	        			$form['type']->addError(new FormError('createlisting.max_two'));
	        		}
	        	}
	        	elseif (!$form['type']['value_'.$this->type->getSubcategoryId()]['value']->getData())
	        	{
	        		$form['type']->addError(new FormError('createlisting.please_check_one'));
	        	}
        
        	}
        	if($this->type->isPieceAvailable())
        	{
	        	if (!$form['pieces']->getData() && !$form['one_piece']->getData()) {
	        		$form['pieces']->addError(new FormError('createlisting.enter_piece'));
	        		
	        	}
	        	if ($form['pieces']->getData() && $form['one_piece']->getData()) {
	        		$form['pieces']->addError(new FormError('createlisting.enter_piece'));
	        		 
	        	}
        	}
        	if($this->type->isSpotAvailable())
        	{
        		if (!$form['spot']->getData() && !$form['unlimited']->getData()) 
        		{
        			$form['spot']->addError(new FormError('createlisting.enter_spot'));
        		}
        		if ($form['spot']->getData() && $form['unlimited']->getData())
        		{
        			$form['spot']->addError(new FormError('createlisting.enter_spot'));
        		}
        	}
        	
        	if($this->type->isPriceAvailable())
        	{
                    $array_values = $form['type']['value_'.$this->type->getSubcategoryId()]['value']->getData();
                    
                        if($this->type=='offerservice')
                        {
                            $flag=TRUE;
                            if ((in_array("1", $array_values) || in_array("2", $array_values) || in_array("3", $array_values)) && in_array("4", $array_values))
                            {                                
                                         $form['type']->addError(new FormError('createlisting.createnote5'));
                                         $flag=FALSE;
                            }
                            if ($flag && !$form['default_setting']->getData() && ($form['price']->getData() || in_array("1", $array_values) || in_array("2", $array_values) || in_array("3", $array_values)))
                            {                                
                                        $form->get('default_setting')->addError(new FormError('createlisting.createnote1'));
                            }
                            if ((in_array("1", $array_values) || in_array("2", $array_values) || in_array("3", $array_values)) && $form['free']->getData())
                            {                                
                                        $form->get('free')->addError(new FormError('createlisting.createnote2'));
                            }
                            if ((in_array("1", $array_values) || in_array("2", $array_values) || in_array("3", $array_values) || in_array("4", $array_values)) && in_array("5", $array_values))
                            {                                
                                        $form['type']->addError(new FormError('createlisting.createnote6'));
                            }
                            
                        }
                        if($this->type=='goods')
                        {
                            if ($form['price']->getData() && (in_array("5", $array_values) || $form['free']->getData() ))
                            {                                
                                        $form->get('free')->addError(new FormError('createlisting.createnote3'));
                            }
                            if (in_array("5", $array_values)&& (in_array("1", $array_values)|| in_array("2", $array_values) ||  in_array("6", $array_values)) )
                            {                                
                                        $form['type']->addError(new FormError('createlisting.createnote4'));
                            }
                           
                        }
        		
                        if($this->type=='service')
                        {
//                            if (!$form['default_setting']->getData() && ($form['price']->getData() || in_array("1", $array_values) || in_array("2", $array_values)))
//                            {                                
//                                        $form->get('default_setting')->addError(new FormError('createlisting.createnote1'));
//                            }
//                            if ((in_array("1", $array_values) || in_array("2", $array_values)) && $form['free']->getData())
//                            {                                
//                                        $form->get('free')->addError(new FormError('createlisting.createnote2'));
//                            }
        		}
        		if($this->type=='offergoods')
                        {
                            if (!$form['default_setting']->getData() && ($form['price']->getData() || in_array("1", $array_values) || in_array("2", $array_values)))
                            {                                
                                        $form->get('default_setting')->addError(new FormError('createlisting.createnote1'));
                            }
                            if (!$form['price']->getData() && in_array("1", $array_values))
                            {                                
                                        $form->get('free')->addError(new FormError('createlisting.enter_price'));
                            }
                            if (in_array("2", $array_values) && $form['free']->getData())
                            {                                
                                        $form->get('free')->addError(new FormError('createlisting.set_price'));
                            }
        		}
                        
                        if($this->type=='offerevents')
                        {
                            if (!$form['default_setting']->getData() && ($form['price']->getData() || in_array("1", $array_values) || in_array("2", $array_values)))
                            {                                
                                        $form->get('default_setting')->addError(new FormError('createlisting.createnote1'));
                            }
                            if (!$form['price']->getData() && in_array("1", $array_values))
                            {                                
                                        $form->get('free')->addError(new FormError('createlisting.enter_price'));
                            }
                            if (in_array("2", $array_values) && $form['free']->getData())
                            {                                
                                        $form->get('free')->addError(new FormError('createlisting.createnote2'));
                            }
        		}
                        
//        		if (!$form['default_setting']->getData() && !$form['free']->getData() && (!in_array("6", $array_values) || !in_array("3", $array_values)))
//        		{
//        			$form->get('default_setting')->addError(new FormError('Either check free or check default setting'));
//        		}
//        		if ($form['default_setting']->getData() && ($form['free']->getData() || (in_array("6", $array_values) || in_array("3", $array_values))))
//        		{
//        			$form->get('default_setting')->addError(new FormError('Either check free or check default setting'));
//        		}
        	}
        	
//         	if($this->type->isLocationChangeAvailable())
//         	{
//         		if (!$form['location']->getData() && !$form['location_arrange']->getData())
//         		{
//         			$form['location']->addError(new FormError('Either define location or select checkbox'));
//         		}
//         		if ($form['location']->getData() && $form['location_arrange']->getData())
//         		{
//         			$form['location']->addError(new FormError('Either define location or select checkbox'));
//         		}
//         	}
        	
        });
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
