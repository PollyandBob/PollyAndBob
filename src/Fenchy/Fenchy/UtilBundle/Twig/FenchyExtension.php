<?php

namespace Fenchy\UtilBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class FenchyExtension extends \Twig_Extension
{
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getLink($string, $route, $data = array(), $args = array())
    {
       
        if (array_key_exists('class', $args)) {
            
            $class = $args['class'];
            unset($args['class']);
            
        } else {
            
            $class = '';
        }
        
        $givenPath = $this->container->get('router')->generate($route, $data, TRUE);
        $uri = $this->container->get('request')->getUri();

        $a = '<a href="%s" class="%s" %s>%s</a>';
        $ar1 = explode('/', $givenPath);
        $ar2 = explode('/', $uri);
        $same = TRUE;

        if(count($ar1) > count($ar2)) {

            $same = FALSE;
            
        } else {
            foreach($ar2 as $k => $v) {
                if(array_key_exists($k, $ar1) && $ar1[$k] != $ar2[$k] && $ar1[$k] !== '0' || $string == 'All') {
                    $same = FALSE;
                    break;
                }
                if(!array_key_exists($k, $ar1) && !is_numeric($v)) {
                    $same = FALSE;
                    break;
                }
            }
        }
        
        $sArgs = '';
        foreach($args as $n => $v) {
            $sArgs .= ' '.$n.'="'.$v.'"';
        }
        
        if ($givenPath == $uri || $same) {
            
            $class .= ' current';
        }

        return sprintf($a, $givenPath, $class, $sArgs, $string);
    }

    public function absolute($url)
    {
        $request = $this->container->get('request');
        if ($request->getHost()) {
            $url = $request->getScheme().'://'.$request->getHost().$url;
        }

        return $url;
    }
    
    public function getFunctions()
    {
        return array(
            'absolute' => new \Twig_function_Method($this, 'absolute'),
            'getlink' => new \Twig_Function_Method($this, 'getLink')
        );
    }

    public function getFilters()
    {
        return array(
            'relative*' => new \Twig_Filter_Method($this, 'relativeDateTime', array('needs_environment' => true))
        );
    }
    
    public function relativeDateTime(\Twig_Environment $env, $type, $date)
    {
        $day = 'day' === strtolower($type);

        if (!($date instanceof \DateTime)) {
            $date = twig_date_converter($env, $date);
        }

        /* @var $translator \Symfony\Bundle\FrameworkBundle\Translation\Translator */
        $translator = $this->container->get('translator');
        $time2str = $this->time2str($date->getTimestamp(), $day);
        
        if (is_string($time2str)) {
            return $translator->trans('util.relative.'.$time2str);
        } elseif (is_array($time2str)) {
            return $translator->transChoice('util.relative.'.$time2str['str'], 
                                             $time2str['number'],
                                             array('%number%' => $time2str['number']));
        }
        return twig_date_format_filter($env, $date);
    }

    protected function time2str($ts, $day = false)
    {
        $diff = time() - $ts;
        
        if($diff == 0) {
            return 'now';
        } elseif($diff > 0) {
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($day) return 'today';
                if($diff < 60) return 'just_now';
                if($diff < 120) return 'a_minute_ago';
                if($diff < 3600) return array('number' => floor($diff / 60), 'str' => 'x_minutes_ago');
                if($diff < 7200) return 'an_hour_ago';
                if($diff < 86400) return array('number' => floor($diff / 3600), 'str' => 'x_hours_ago');
            }
            if($day_diff == 1) return 'yesterday';
            if($day_diff < 7) return array('number' => $day_diff, 'str' => 'x_days_ago');
            if($day_diff < 31) return array('number' => ceil($day_diff / 7), 'str' => 'x_weeks_ago');
            if($day_diff < 60) return 'last_month';
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($diff < 120) return 'in_a_minute';
                if($diff < 3600) return array('number' => floor($diff / 60), 'str' => 'in_x_minutes');
                if($diff < 7200) return 'in_an_hour';
                if($diff < 86400) return array('number' => floor($diff / 3600), 'str' => 'in_x_hours');
            }
            if($day_diff == 1) return 'tomorrow';
            if($day_diff < 4) return date('l', $ts); //day of the week
            if($day_diff < 7 + (7 - date('w'))) return 'next_week';
            if(ceil($day_diff / 7) < 4) return array('number' => ceil($day_diff / 7), 'str' => 'in_x_weeks');
            if(date('n', $ts) == date('n') + 1) return 'next_month';
        }
        return null;
    }
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'fenchy_extension';
    }
}