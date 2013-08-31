<?php

namespace Fenchy\NoticeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Fenchy\NoticeBundle\Entity\Type;
use Fenchy\NoticeBundle\Entity\Notice;
use Fenchy\NoticeBundle\Entity\Value;

class TypeType extends AbstractType
{
    private $type;
    private $notice;
    
    private $option;
    private $value;
    
    public function __construct(Type $type, Notice $notice, $option = NULL, $value = NULL) {
        
        $this->type     = $type;
        $this->notice   = $notice;
        
        $this->option = $option;
        $this->value  = $value;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        foreach($this->type->getProperties() as $property) {
            
            if(!($value = $this->notice->getValueByProperty($property))) {
                $value = new Value();
                $value->setNotice($this->notice);
                $value->setProperty($property);
                $value->setValue($this->value);
            }
            
            $opt = array(
                        'property_path' => false, 
                        'data'          => $value
                        );
            
            if($property->getName() === Type::$direction) {
                $opt['attr'] = array('style' => 'display: none;');
            }
            
            $builder->add(
                    'value_'.$property->getId(), 
                    new ValueType($property, $this->value), 
                    $opt
                    );
        }                
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\NoticeBundle\Entity\Type'
        ));
    }

    public function getName()
    {
        return 'fenchy_noticebundle_typetype';
    }
}
