<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

use Fenchy\UserBundle\Entity\User;
/**
 * RequestRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RequestRepository extends EntityRepository
{
	public function findByInJSON($router, array $criteria, array $orderBy, $limit, $offset) {
		$requests = $this->findBy($criteria,$orderBy,$limit,$offset);
		$requestsJSON = array();

		foreach($requests as $oneRequest) {

			$author = $oneRequest->getAuthor();
			$authorProfileUrl = $router->generate(
					'fenchy_regular_user_user_otherprofile_aboutotherchoice',
					array('userId' => $author->getId()) );

			$aboutUser = $oneRequest->getAboutUser();
			$aboutUserProfileUrl = $router->generate(
					'fenchy_regular_user_user_profilev2',
					array('userId' => $aboutUser->getId()) );

			$aboutNotice = $oneRequest->getAboutNotice();
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

			$requestsJSON[] = array(
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
					'id' => $oneRequest->getId(),
					'status' => $oneRequest->getStatus(),
					'requeststatus' => $oneRequest->getRequeststatus(),
					'text' => $oneRequest->getText(),
					'title' => $oneRequest->getTitle(),
					'createdAt'=> $oneRequest->getCreatedAt(),
					'piece_spot' => $oneRequest->getPieceSpot(),
					'price' => $oneRequest->getPrice(),
					'free' => $oneRequest->getFree(),
					'proposeprice' => $oneRequest->getProposeprice(),
					'totalprice' => $oneRequest->getTotalprice(),
					'currency' => $oneRequest->getCurrency(),
					'is_read' => $oneRequest->getIsRead(),
					'is_read_status' => $oneRequest->getIsReadStatus(),
					
			);
		}

		return $requestsJSON;
	}

	/**
	 * Set the flag `is_read` on user's requests
	 * @param boolean $is_read_state
	 * @param \Fenchy\UserBundle\Entity\User $user
	 * @author Mateusz Krowiak <mkrowiak@pgs-soft.com>
	 */
	public function updateUsersRequestsWithIsRead($is_read_state, User $user) {

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
	 * Return number of unread User's requests
	 * @return integer
	 * @param \Fenchy\UserBundle\Entity\User $user
	 */
	public function countUnreadUsersRequests(User $user) {

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

	public function countUnreadUsersStatusRequests(User $user) {
	
		$query = $this->createQueryBuilder('r')
		->select('COUNT(r.id)')
		->where('r.is_read_status = :read and r.author = :user')
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
	public function getNoticeIds(User $user)
	{
		return  $this->createQueryBuilder('r')
				->select('r')				
				->where('r.author = :author')
				->setParameter('author', $user->getId())	
				->getQuery()
				->getResult();
	}
}