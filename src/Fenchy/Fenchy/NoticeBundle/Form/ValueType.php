<?php

namespace Fenchy\NoticeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\True;
use Doctrine\ORM\EntityRepository;

use Fenchy\NoticeBundle\Entity\PropertyType;
use Fenchy\NoticeBundle\Entity\Notice;

class ValueType extends AbstractType
{
    private $property;
    private $value;
    
    public function __construct(PropertyType $property, $value) {
        
        $this->property = $property;
        $this->value = $value;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $choices = $this->property->getOptions();
        if($choices) {
            foreach($choices as $key => &$val) {
                $val = 'notice.subcategory.' . $val;
            }
        }
        
        $property = $this->property;
        $opt = array();
        $opt['data'] = $this->value;
        $opt['label'] = $this->property->getName();
        if($this->property->getOptions()) {
            $opt['multiple'] = $this->property->getMultiple();
            $opt['expanded'] = $this->property->getExpanded();
            $opt['choices']  = $choices;
            
        }
        $builder->add(
                'value',
                'choice',
                array('choices'=>$choices,
                		'multiple'=>true,
                		'expanded'=>true
                	)
                )
            ->add(
                    'property',
                    'entity',
                    array(
                        'class' => 'FenchyNoticeBundle:PropertyType',
                        'query_builder' => function(EntityRepository $er) use($property) {
                            return $er->createQueryBuilder('pt')
                                ->where('pt = :pt')
                                ->setParameter('pt', $property);
                        },
                        'attr' => array('style' => 'display: none;')
                    ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fenchy\NoticeBundle\Entity\Value'
        ));
    }

    public function getName()
    {
        return 'fenchy_noticebundle_valuetype';
    }
}
