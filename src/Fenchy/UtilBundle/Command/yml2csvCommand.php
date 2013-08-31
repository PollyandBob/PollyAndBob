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
class yml2csvCommand extends ContainerAwareCommand
{
    static $languages = array('en', 'de', 'pl');
    
    protected function configure()
    {
        $this->setName('fenchy:util:trans:yml2csv')
            ->setDescription('Creates translation.csv file');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        
        $dir = __DIR__.'/../..';
        echo 'Start directory: '.$dir.PHP_EOL.PHP_EOL;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                $merged = array(array('BUNDLE' => 'BUNDLE', 'FILE' => 'FILE', 'PLACEHOLDER' => 'PLACEHOLDER'));
                foreach(self::$languages as $lang) {
                    $merged[0][$lang] = $lang;
                }
                // for each bundle folder
                while (($bundle = readdir($dh)) !== FALSE) {
                    if(is_dir($dir.'/'.$bundle) && strstr($bundle, 'Bundle')) {
                        echo PHP_EOL.'Reading bundle '.$bundle.PHP_EOL;
                        $translationFolder = $dir.'/'.$bundle.'/Resources/translations';
                        // if translations folder exists
                        if(is_dir($translationFolder) && $dh2 = opendir($translationFolder)) {
                            
                            // find all .en.yml files
                            while(($file = readdir($dh2)) !== FALSE) {
                                if(substr($file, -4) == '.yml') {
                                    echo 'Reading file '.$file.PHP_EOL;
                                    $filename = substr($file, 0, strlen($file) - 7);
                                    $content = $this->readYML($translationFolder, $file);
                                    foreach($content as $ph => &$row) {
                                        $row['BUNDLE'] = $bundle;
                                        $row['FILE'] = $filename;
                                        if(array_key_exists($ph, $merged)) {
                                            $merged[$ph] = array_merge($merged[$ph], $row);
                                        } else {
                                            $merged[$ph] = $row;
                                        }
                                    }
//                                    $merged = array_merge_recursive($merged, $content);
                                }
                            }
                            closedir($dh2);
                        }
                        
                    }
                    
                } // end of while
                
                $this->createCSV($merged);
                closedir($dh);
            }
        }
        
    }
    
    private function createCSV($array) {
        echo PHP_EOL.'Creating .csv file'.PHP_EOL;
        $fp = fopen('translations.csv', 'w');

        foreach ($array as $fields) {
            $row = array(
                'BUNDLE'        => $fields['BUNDLE'],
                'FILE'          => $fields['FILE'],
                'PLACEHOLDER'   => $fields['PLACEHOLDER']
                );
            foreach(self::$languages as $lang) {
                if(array_key_exists($lang, $fields)) {
                    $row[$lang] = $fields[$lang];
                } else {
                    $row[$lang] = NULL;
                }
                
            }
            
            fputcsv($fp, $row, ';');
        }

        fclose($fp);
        echo 'File created.'.PHP_EOL;
    }
    
    private function readYML($path, $file) {
        
        $lines = explode("\n", file_get_contents($path.'/'.$file));
        $extension = substr($file, -7);
        $lang = substr($extension, 1, 2);
        
        if(!in_array($lang, self::$languages)) {
            echo $lang.' language is not supported. found in: '.$path.'/'.$file;exit;
        }
        $prefixes = array();
        $result = array();
        $lastspaces = NULL;
        $sepLvl = NULL; // separated level
        $sepLin = ''; // separated lines;
        $sepPref = '';
        $lastLvl = 0;
        foreach($lines as $l) {
            
            // continue if comment
            if(trim($l) == '' && $sepLvl === NULL || strspn(trim($l), '#')) continue;
            
            // get number of spaces
            $spaces = strspn($l, ' ');
            // split to placeholder and label
            $splited = explode(':', $l);
            $trimed = array();
            // trim data
            foreach($splited as $k => $v) {
                $trimed[$k] = trim($v);
            }
            
            // each 2 spaces are one level down
            $clvl = $spaces/2;
            
            // if we have jumped some levels we have to remove its old placeholders
            if($clvl - $lastLvl > 1) {
                for($k = $lastLvl + 2; $k < $clvl; $k++) {
                    unset($prefixes[$k]);
                }
            }
            
            $lastLvl = $clvl;
            
            // if we are not in reading multiline translation mode
            if($sepLvl === NULL) {
                
                // something is wrong if we are not in multiline mode and there is 
                // no ':' in line.
                if(!key_exists(1, $trimed)) { var_dump($l, $trimed, $path, $file, $result);exit;}
                
                // if nothing after ':' add prefix;
                if(!$trimed[1]) {
                    $prefixes[$clvl] = $trimed[0];
                } else {
                    // if there is only '|' after ':' we should start multiline mode.
                    if($trimed[1] == '|') {
                        $sepLvl = $clvl;
                        $sepLin = '';
                        $sepPref = $trimed[0];
                        
                    } else {
                        $this->addResult($result, $trimed, $prefixes, $clvl, $lang);
                    }
                }
                
            // if we are in multiline translation mode
            } else {
                echo 'reading multiline'.PHP_EOL;
                // if we go level up it is time to add collected lines into result
                if($clvl <= $sepLvl && trim($l) != '') {
                    echo 'lvl up - multiline should be stored'.PHP_EOL;
                    // something is wrong if we are not in multiline mode and there is 
                    // no ':' in line.
                    if(!key_exists(1, $trimed)) { var_dump($l, $trimed, $path, $file, $result);exit;}
                    
                    // create prefix
                    $prefix = '';
                    for($i = 0; $i < $sepLvl; $i++) {
                        if(array_key_exists($i, $prefixes))
                            $prefix .= $prefixes[$i].'.';
                    }
                    $prefix .= $sepPref;
                    echo "\tMultiLine found: ".$sepPref.PHP_EOL;
                    echo $sepLin.PHP_EOL;
                    $result[$prefix] = array('PLACEHOLDER' => $prefix, $lang => $sepLin);
                    $sepLin = '';
                    $sepLvl = NULL;
                    if(!$trimed[1]) {
                        $prefixes[$clvl] = $trimed[0];
                    } elseif($trimed[1] == '|') { //if start of multiline
                        $sepLvl = $clvl;
                        $sepLin = '';
                        $sepPref = $trimed[0];
                    } else {
                        $this->addResult($result, $trimed, $prefixes, $clvl, $lang);
                    }
                } else {
                    $sepLin .= " ".trim($l);
                }
            }
        }
        return $result;
    }
    
    
    private function addResult(&$result, &$trimed, &$prefixes, $clvl, $lang) {
        // Join all stored prefixes and current placeholder by '.'
        $prefix = '';
        for($i = 0; $i < $clvl; $i++) {
            if(array_key_exists($i, $prefixes))
                $prefix .= $prefixes[$i].'.';
        }
        $trimed[0] = $prefix.$trimed[0];

        // if there is more than 2 elemetns (more than 2 ':' has been found in line
        // then it was just a character in translation and w should revert it.
        if(count($trimed) > 2) {
            for($j = 2; $j < count($trimed); $j++) {
                $trimed[1] .= ':'.$trimed[$j];
                unset($trimed[$j]);
            }
        }
        echo "\tLine found: ".$trimed[0].PHP_EOL;
        $result[$trimed[0]] = array('PLACEHOLDER' => $trimed[0], $lang => $trimed[1]);
    }
            
}