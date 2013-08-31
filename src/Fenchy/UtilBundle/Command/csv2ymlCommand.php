<?php
namespace Fenchy\UtilBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class csv2ymlCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {
        $this->setName('fenchy:util:trans:csv2yml')
            ->setDescription('Creates translation yml files from translation.csv file');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        
        $dir = __DIR__.'/../..';
        $fileLocation = $dir.'/../..';
        echo 'Start directory: '.$dir.PHP_EOL.PHP_EOL;
        
        $structure = array();
        
        if (($handle = fopen($fileLocation.'/'.'translations.csv', "r")) !== FALSE) {
            $headers = fgetcsv($handle, 0, ";");
            while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
                $asoc = array_combine($headers, $data);
                $bundle = $asoc['BUNDLE'];
                $file = $asoc['FILE'];
                $ph = $asoc['PLACEHOLDER'];
                unset($asoc['BUNDLE']);
                unset($asoc['FILE']);
                unset($asoc['PLACEHOLDER']);
                foreach($asoc as $lang => $label) {
                    $structure[$bundle][$file][$lang][$ph] = $label;
                }
            }
            fclose($handle);
        }
        
        $this->saveYML($structure, $dir);
    }
    
    private function saveYML($structure, $dir) {
        
        foreach($structure as $bundle => $files) {
            if(!is_dir($dir.'/'.$bundle.'/Resources/translations')) {
                echo 'directory '.$dir.'/'.$bundle.'/Resources/translations doesnt exists'.PHP_EOL;
            } else {
                foreach($files as $file => $langs) {
                    foreach($langs as $lang => $phs) {
                        $fp = fopen("$dir/$bundle/Resources/translations/{$file}.$lang.yml", 'w');
                        fwrite($fp, $this->arrayToYmlString($phs, $lang));
                        fclose($fp);
                    }
                }
            }
            
            
        }
    }
    
    private function arrayToYmlString($phs, $lang) {
        $structure = array();
        foreach($phs as $ph => $label) {
            $map = explode('.', $ph);
            echo $ph.PHP_EOL;
            $element = &$structure;
            foreach($map as $prefix) {

                if(!array_key_exists($prefix, $element)) {
                    $element[$prefix] = array();
                }

                $element = &$element[$prefix];

            }
            $element = $label;

        }
        
        $string = '';
        foreach($structure as $prefix => $_element) {
            $string .= $prefix.':'.$this->getLine($_element, '', $lang);
        }
        
        return $string;
    }
    
    private function getLine(&$structure, $spaces, $lang) {
        if(is_string($structure)) return ' '.$this->iconv($structure, $lang)."\n";
        $return = '';
        foreach($structure as $prefix => $_element) {
            $return .= "\n".$spaces.'  '.$prefix.':'.$this->getLine($_element, $spaces.'  ', $lang);
        }
        
        return $return;
    }
    
    protected function iconv($str, $lang) {
        $charsets = array(
            'pl' => NULL,
            'de' => 'ISO-8859-1',
            'en' => 'ISO-8859-1'
        );
        if(!array_key_exists($lang, $charsets) || $charsets[$lang] == NULL) return $str;
        
        return iconv($charsets[$lang], 'UTF-8', $str);
    }
}