<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr;

use Fenchy\AdminBundle\Entity\UsersFilter;

class UserRepository extends EntityRepository
{
    
    
    public function findByEmailChangeConfirmationToken($token)
    {
        return $this->createQueryBuilder('u')
            ->join('u.email_change_request','r')
            ->where('r.confirmation_token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    /**
     * 
     * @param UsersFilter $filter
     * @return array
     */
    public function findAllWithStickers($filter = NULL) {
        
        
        if($filter instanceof \Fenchy\AdminBundle\Entity\UsersFilter) {
            
            if($filter->sort === 'stickersQ') {
                
                $sub = $this->getEntityManager()->createQuery("
                        SELECT count(s2.id) FROM FenchyUtilBundle:Sticker s2
                        WHERE s2.user = u AND s2.discarded_at IS NULL"
                    );
                
                $query = $this->createQueryBuilder('u')
                    ->select('u, ru, s, su, ('.$sub->getDQL().') as HIDDEN stickersQ')
                    ->join('u.user_regular', 'ru')
                    ;
                
            } else {
                $query = $this->createQueryBuilder('u')
                    ->select('u, ru, s, su')
                    ->join('u.user_regular', 'ru')
                    ;
            }
            
            if($filter->reported_only) {
                $query->join('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                    ->leftJoin('s.reported_by', 'su');
            } else {
                $query->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                    ->leftJoin('s.reported_by', 'su');
            }
            
            if($filter->name) {
                $query->andWhere('ConcatSB(ru.firstname, ru.lastname) like :name')
                        ->setParameter('name', '%'.$filter->name.'%');
            }
            if($filter->created_after) {
                $query->andWhere('u.created_at > :after')
                        ->setParameter('after', $filter->created_after);
            }
            if($filter->created_before) {
                $query->andWhere('u.created_at < :before')
                        ->setParameter('before', $filter->created_before);
            }

            if($filter->sort === 'stickersQ') {
                $query->orderBy($filter->sort, $filter->order);
            } elseif($filter->sort === 'lastname') {
                $query->orderBy('ru.'.$filter->sort, $filter->order);
            } else {
                $query->orderBy('u.'.$filter->sort, $filter->order);
            }
        } else {
            $query = $this->createQueryBuilder('u')
                ->select('u, ru, s, su')
                ->join('u.user_regular', 'ru')
                ->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                ->leftJoin('s.reported_by', 'su');
        }
        
        return $query->orderBy('u.id','DESC')
                ->getQuery()
                ->getResult();
    }
    
    public function getAllData($id) {
        
        return $this->createQueryBuilder('u')
                ->select('u, ru, rug, rugi, n, r, owr, c')
                ->join('u.user_regular', 'ru')
                ->join('ru.gallery', 'rug')
                ->leftJoin('rug.images' , 'rugi')
                ->leftJoin('u.notices', 'n')
                ->leftJoin('u.reviews', 'r')
                ->leftJoin('u.ownReviews', 'owr')
                ->leftJoin('ru.myFriends', 'c')
                ->where('u = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    public function getFindAllQuery() {
        
        return $this->createQueryBuilder('u')
                    ->select('u, ru, og, ogi, l')
                    ->join('u.user_regular', 'ru')
                    ->join('ru.gallery', 'og')
                    ->leftJoin('og.images', 'ogi')
                    ->join('u.location', 'l');
    }
}