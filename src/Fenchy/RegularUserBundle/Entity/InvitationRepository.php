<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * InvitationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvitationRepository extends EntityRepository
{
    /**
     * Counts invitations
     * 
     * @param \Fenchy\RegularUserBundle\Entity\UserRegular $regularUser
     * @return integer 
     */
    public function count(\Fenchy\RegularUserBundle\Entity\UserRegular $regularUser)
    {
        return $this->createQueryBuilder('i')
                    ->select('count(i.id)')
                    ->where('i.invitation_for = :user')
                    ->andWhere('i.accepted IS NULL')
                    ->setParameter(':user', $regularUser)
                    ->getQuery()
                    ->getSingleScalarResult();
    }
}