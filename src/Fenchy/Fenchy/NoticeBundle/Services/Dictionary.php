<?php

namespace Fenchy\NoticeBundle\Services;
use Fenchy\AdminBundle\Entity\DictionaryFilter;
/**
 * Dictionary Service
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class Dictionary {

    private $store = TRUE;
    private $text  = '';
    private $tag   = FALSE;
    private $nonTagDelimeter = ' ';
    private $tagDelimeter = ',';
    private $em;
    private $repo  = 'FenchyNoticeBundle:Dictionary';
    
    public function __construct($store, $nonTagDelimeter, $tagDelimeter, $doctrine)
    {
        $this->em               = $doctrine->getManager();
        $this->tagDelimeter     = $tagDelimeter;
        $this->nonTagDelimeter  = $nonTagDelimeter;
        $this->store            = $store;
    }
    
    /**
     * Stores dictionaries created from given $text by spliting it by ','.
     * 
     * @param string $text
     * @param boolean $tag
     * @return array of Dictionary (tags);
     */
    public function store($text, $tag = FALSE) {

        //If store parameter is set to FALSE we will not add this element to dictionary.
        if(!$this->store || empty($text)) return;
        
        $array = $this->split($text, $tag);

        $repo = $this->em->getRepository($this->repo);
        $tags = array();
        foreach($array as $word) {
            $word = ucfirst($word);
            $dict = $repo->getOneByText($word);

            // If dictionary element has not been found
            if(NULL === $dict) {
                $dict = new \Fenchy\NoticeBundle\Entity\Dictionary();
                $dict->setText($word);
                if($tag) {
                    $dict->setTag(TRUE);
                }
            }
            if($tag) {
                $dict->incQuantityTag();
            } else {
                $dict->incQuantitySearch();
            }
            $this->em->persist($dict);
            $tags[] = $dict;
        }
        
        $this->em->flush();
        
        return $tags;
    }
    
    /**
     * This methot splits given string into array of words/tags.
     * $text will be splited by rules defined by $tagDelimeter and $nonTagDelimeter
     * parameters passed to constructor.
     * Also calls trim on each element.
     * 
     * @param String $text
     * @param Boolean $tag
     * @return Array
     */
    protected function split($text, $tag) {

        if($tag) {
            if(!$this->tagDelimeter){
                $array = array($text);
            } else {
                $array = preg_split($this->tagDelimeter, $text);
            }
        } else {
            if(!$this->nonTagDelimeter) {
                $array = array($text);
            } else {
                $array = preg_split($this->nonTagDelimeter, $text);
            }
        }
        
        // Trim elements;
        array_walk($array, 'trim');
        
        // Remove empty elements;
        foreach($array as $k => $v) {
            if(!is_string($v) || '' === $v) unset($array[$k]);
        }
        
        return $array;
    }
    
    /**
     * Method returning matching available text suggestions for phrase
     * currently typed by user.
     * 
     * @param type $phrase
     * @return array of objects
     */
    public function whatAutocompleteSuggestions($phrase) {
        $repo = $this->em->getRepository($this->repo);
        $dFilter = new \Fenchy\AdminBundle\Entity\DictionaryFilter();
        $dFilter->text = $phrase;
        $dFilter->search_activated = TRUE;
        $dFilter->tag_activated = null;
        $dFilter->disabled = FALSE;
        $dFilter->tag = null;
        $dict = $repo->filterBy($dFilter);
        return $dict;
    }
    
    public function getAllListingTags() {
        
        $tags = $this->em->getRepository($this->repo)->getAllListingTags();
        foreach($tags as $i => $tag) $tags[$i] = $tag['text'];
        return $tags;
    }
    
}

?>
