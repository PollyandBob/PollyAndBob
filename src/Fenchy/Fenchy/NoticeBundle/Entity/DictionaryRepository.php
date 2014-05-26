<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Fenchy\AdminBundle\Entity\DictionaryFilter;
/**
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class DictionaryRepository extends EntityRepository
{
    public function filterBy(DictionaryFilter $filter) {

        $query = $this->createQueryBuilder('d')
                ->select('d');

        if ( !is_null($filter->search_activated) ) {
            if ( $filter->search_activated ) {
                $query->andWhere('d.searchActivated = TRUE');
            } else {
                $query->andWhere('d.searchActivated = FALSE');
            }
        }
        
        if ( !is_null($filter->tag_activated) ) {
            if($filter->tag_activated) {
                $query->andWhere('d.tagActivated = TRUE');
            } else {
                $query->andWhere('d.tagActivated = FALSE');
            }
        }
        
        if ( !is_null($filter->disabled) ) {
            if($filter->disabled) {
                $query->andWhere('d.disabled = TRUE');
            } else {
                $query->andWhere('d.disabled = FALSE');
            }
        }
        
        if(!empty($filter->text)) {
            $query->andWhere('lower(d.text) like lower(:text)')
                    ->setParameter('text', '%'.$filter->text.'%');
        }

        if($filter->search_quantity_less) {
            $query->andWhere('d.quantitySearch < :less')
                    ->setParameter('less', $filter->search_quantity_less);
        }
        
        if($filter->search_quantity_more) {
            $query->andWhere('d.quantitySearch > :more')
                    ->setParameter('more', $filter->search_quantity_more);
        }
        
        if($filter->tag_quantity_less) {
            $query->andWhere('d.quantityTag < :lessT')
                    ->setParameter('lessT', $filter->tag_quantity_less);
        }
        
        if($filter->tag_quantity_more) {
            $query->andWhere('d.quantitySearch > :moreT')
                    ->setParameter('moreT', $filter->tag_quantity_more);
        }
        
        if ( !is_null($filter->tag) ) {
            if($filter->tag) {
                $query->andWhere('d.tag = TRUE');
            } else {
                $query->andWhere('d.tag = FALSE');
            }
        }

        return $query
                ->orderBy('d.'.$filter->sort, $filter->order)
                ->getQuery()
                ->getResult();
    }
    
    public function getSearch($text) {
        
        return $this->createQueryBuilder('d')
                ->select('d')
                ->where('d.searchActivated = true')
                ->andWhere('d.disabled = false')
                ->andWhere('d.text like :text')
                ->setParameter('text', $text.'%')
                ->getQuery()
                ->getResult();
    }
    
    public function getTags($text) {
        
        return $this->createQueryBuilder('d')
                ->select('d')
                ->where('d.tagActivated = true')
                ->andWhere('d.disabled = false')
                ->andWhere('d.text like :text')
                ->setParameter('text', $text.'%')
                ->getQuery()
                ->getResult();
    }
    
    public function getAllListingTags() {
        
        return $this->createQueryBuilder('d')
                ->select('d.text')
                ->where('d.tagActivated = true')
                ->andWhere('d.disabled = false')
                ->getQuery()
                ->getResult();
    }
    
    public function getOneByText($text) {
        
        return $this->createQueryBuilder('d')
                ->select('d')
                ->where('lower(d.text) like lower(:text)')
                ->setParameter('text', $text)
                ->getQuery()
                ->getOneOrNullResult();
    }
}