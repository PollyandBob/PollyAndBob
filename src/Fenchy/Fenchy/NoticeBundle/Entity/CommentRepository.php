<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;


use Fenchy\UserBundle\Entity\User;

/**
 * CommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends EntityRepository
{
        public function getFullDetailedList ($filter = NULL) {

            if(!($filter instanceof \Fenchy\AdminBundle\Entity\CommentsFilter)) {

                return $this->createQueryBuilder('r')
                        ->select('r, a, n, u, s, su')
                        ->join('r.author', 'a')
                        ->leftJoin('r.aboutNotice', 'n')
                        ->leftJoin('r.aboutUser', 'u')
                        ->leftJoin('r.aboutUserGroup', 'ug')
                        ->leftJoin('n.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                        ->leftJoin('s.reported_by', 'su')
                        ->getQuery()
                        ->getResult();
            } 

            $sub = $this->getEntityManager()->createQuery("
                            SELECT count(s2.id) FROM FenchyUtilBundle:Sticker s2
                            WHERE s2.review = r AND s2.discarded_at IS NULL"
                        );

            $query = $this->createQueryBuilder('r')
                        ->select('r, a, s, su, ('.$sub->getDQL().') as HIDDEN stickersQ')
                        ->join('r.author', 'a');
            $query->join('r.aboutUser', 'u');


            $query->leftJoin('r.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                    ->leftJoin('s.reported_by', 'su');

             if($filter->text) {
                $query->where('LOWER(r.text) like :text')
                        ->setParameter('text', strtolower('%'.$filter->text.'%'));
            }

            if($filter->author) {
                $query->andWhere('LOWER(a.email) like :email')
                        ->setParameter('email', strtolower('%'.$filter->author.'%'));
            }

            if($filter->receiver) {               
                $query->andWhere('LOWER(u.email) like :email1')
                        ->setParameter('email1', strtolower('%'.$filter->receiver.'%'));
            }

            if($filter->target === 'user') {
                $query->addSelect('u');

            } elseif($filter->target === 'notice') {
                $query->join('r.aboutNotice', 'n')
                        ->addSelect('n');
            }




            if($filter->sort === 'stickersQ') {
                return $query
                        ->orderBy($filter->sort, $filter->order)
                        ->getQuery()
                        ->getResult();
            }
            if($filter->sort === 'receiver') {
                return $query
                        ->orderBy('r.aboutUser', $filter->order)
                        ->getQuery()
                        ->getResult();
            }

            return $query
                        ->orderBy('r.'.$filter->sort, $filter->order)
                        ->getQuery()
                        ->getResult();
        }
        
	public function findByInJSON($router, array $criteria, array $orderBy, $limit, $offset) {
		$comments = $this->findBy($criteria,$orderBy,$limit,$offset);
		$commentsJSON = array();

		foreach($comments as $oneComment) {

			$author = $oneComment->getAuthor();
			$authorProfileUrl = $router->generate(
					'fenchy_regular_user_user_otherprofile_aboutotherchoice',
					array('userId' => $author->getId()) );

			$aboutUser = $oneComment->getAboutUser();
			$aboutUserProfileUrl = $router->generate(
					'fenchy_regular_user_user_profilev2',
					array('userId' => $aboutUser->getId()) );

			$aboutNotice = $oneComment->getAboutNotice();
			//echo "<pre>"; print_r($aboutNotice);exit;

			$hasNotice = ( $aboutNotice && $aboutNotice->getId() );
			if ( $hasNotice ) {
				$aboutNoticeUrl = $router
				->generate('fenchy_notice_show_slug', array(
						'slug' => $aboutNotice->getSlug(),
						'year' => $aboutNotice->getCreatedAt()->format('Y'),
						'month' => $aboutNotice->getCreatedAt()->format('m'),
						'day' => $aboutNotice->getCreatedAt()->format('d') ));
			}

			$commentsJSON[] = array(
					'author'=>array(
							'id' => $author->getId(),
							'name' => $author->getUserRegular()->getFirstname(),
							'image' =>  $author->getUserRegular()->getAvatar(),
							'profileUrl' => $authorProfileUrl,
							'activity' => $author->getActivity()
					),
					'aboutuser' => array(
							'id' => $aboutUser->getId(),
							'name' => $aboutUser->getUserRegular()->getFirstname(),
							'image' => $aboutUser->getUserRegular()->getAvatar(),
							'profileUrl' => $aboutUserProfileUrl
					),
					'aboutnotice' => $hasNotice ? array(
							'id' => $aboutNotice->getId(),
							'title' => $aboutNotice->getTitle(),
							'image' => '',
							'noticeUrl' => $aboutNoticeUrl,
					) : null,
					'id' => $oneComment->getId(),
					'type' => $oneComment->getType(),
					'text' => $oneComment->getText(),
					'title' => $oneComment->getTitle()
			);
		}

		return $commentsJSON;
	}
	
	public function findGroupCommentsByInJSON($router, array $criteria, array $orderBy, $limit, $offset) {
		$comments = $this->findBy($criteria,$orderBy,$limit,$offset);
		$commentsJSON = array();
	
		foreach($comments as $oneComment) {
	
			$author = $oneComment->getAuthor();
			$authorProfileUrl = $router->generate(
					'fenchy_regular_user_user_otherprofile_aboutotherchoice',
					array('userId' => $author->getId()) );
	
			$aboutUser = $oneComment->getAboutUser();
			$aboutUserProfileUrl = $router->generate(
					'fenchy_regular_user_user_profilev2',
					array('userId' => $aboutUser->getId()) );
	
			$aboutUserGroup = $oneComment->getAboutUserGroup();
			//echo "<pre>"; print_r($aboutUserGroup);exit;
	
			$hasNotice = ( $aboutUserGroup && $aboutUserGroup->getId() );
			
			$commentsJSON[] = array(
					'author'=>array(
							'id' => $author->getId(),
							'name' => $author->getUserRegular()->getFirstname(),
							'image' =>  $author->getUserRegular()->getAvatar(),
							'profileUrl' => $authorProfileUrl
					),
					'aboutuser' => array(
							'id' => $aboutUser->getId(),
							'name' => $aboutUser->getUserRegular()->getFirstname(),
							'image' => $aboutUser->getUserRegular()->getAvatar(),
							'profileUrl' => $aboutUserProfileUrl
					),
					'aboutusergroup' => $hasNotice ? array(
							'id' => $aboutUserGroup->getId(),
							'title' => '',
							'image' => '',
							'noticeUrl' => '',
					) : null,
					'id' => $oneComment->getId(),
					'type' => $oneComment->getType(),
					'text' => $oneComment->getText(),
					'title' => $oneComment->getTitle(),
					'createdAt' => $oneComment->getCreatedAt(),
			);
		}
	
		return $commentsJSON;
	}

	/**
	 * Set the flag `is_read` on user's comments
	 * @param boolean $is_read_state
	 * @param \Fenchy\UserBundle\Entity\User $user
	 * 
	 */
	public function updateUsersCommentsWithIsRead($is_read_state, User $user) {

		$is_read_state = $is_read_state?'true':'false';

		$query = $this->createQueryBuilder('r')
		->update()
		->set('r.is_read', $is_read_state)
		->where('r.aboutUser = :user')
		->getQuery();

		$query->setParameter('user', $user);

		return $query->execute();

	}

	/**
	 *
	 * Return number of unread User's comments
	 * @return integer
	 * @param \Fenchy\UserBundle\Entity\User $user
	 */
	public function countUnreadUsersComments(User $user) {

		$query = $this->createQueryBuilder('r')
		->select('COUNT(r.id)')
		->where('r.is_read = :read and r.aboutUser = :user')
		->setParameters(array(
				'read' => 'false',
				'user' => $user
		))
		->getQuery();

		$total = $query->getSingleScalarResult();
		return $total;

	}

	public function findCount( $criteria ) {
		$query =  $this->createQueryBuilder('r')
		->select('COUNT(r.id)');

		if ( array_key_exists('aboutUser', $criteria) ) {
			$query->andWhere('r.aboutUser = '.$criteria['aboutUser']);
		}

		if ( array_key_exists('author', $criteria) ) {
			$query->andWhere('r.author = '.$criteria['author']);
		}

		if ( array_key_exists('aboutNotice', $criteria) ) {
			$query->andWhere('r.aboutNotice = '.$criteria['aboutNotice']);
		}

		if ( array_key_exists('type', $criteria) ) {
			$query->andWhere('r.type = '.$criteria['type']);
		}



		return $query->getQuery()->getSingleScalarResult();
	}
}