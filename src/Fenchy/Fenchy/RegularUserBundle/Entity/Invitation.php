<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Fenchy\RegularUserBundle\Entity\Invitation
 *
 * @ORM\Table(name="user__friends_invitations")
 * @ORM\Entity(repositoryClass="Fenchy\RegularUserBundle\Entity\InvitationRepository")
 */
class Invitation
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @var \DateTime $updated_at
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated_at;

    /**
     * @var integer $status
     *
     * @ORM\Column(name="accepted", type="boolean", nullable=true)
     */
    private $accepted;
    
    /**
     * @ORM\ManyToOne(targetEntity="UserRegular", inversedBy="myInvitations")
     * @ORM\JoinColumn(name="invitation_by", referencedColumnName="id")
     **/
    private $invitation_by;

    /**
     * @ORM\ManyToOne(targetEntity="UserRegular", inversedBy="invitationsForMe")
     * @ORM\JoinColumn(name="invitation_for", referencedColumnName="id")
     **/
    private $invitation_for;

    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Invitation
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Invitation
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set accepted
     *
     * @param bool $accepted
     * @return Invitation
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;
    
        return $this;
    }

    /**
     * Is accepted
     * Returns null in case invitation was not respond
     *
     * @return bool|null
     */
    public function isAccepted()
    {
        return $this->accepted;
    }

    /**
     * Is respond - accepted or not
     * 
     * @return bool 
     */
    public function isRespond()
    {
        return null !== $this->accepted;
    }

    /**
     * Set invitation_by
     *
     * @param Fenchy\RegularUserBundle\Entity\UserRegular $invitationBy
     * @return Invitation
     */
    public function setInvitationBy(\Fenchy\RegularUserBundle\Entity\UserRegular $invitationBy = null)
    {
        $this->invitation_by = $invitationBy;
        if (null === $invitationBy) {
            $invitationBy->removeMyInvitation($this);
        } else {
            $invitationBy->addMyInvitation($this);
        }
    
        return $this;
    }

    /**
     * Get invitation_by
     *
     * @return Fenchy\RegularUserBundle\Entity\UserRegular 
     */
    public function getInvitationBy()
    {
        return $this->invitation_by;
    }

    /**
     * Set invitation_for
     *
     * @param Fenchy\RegularUserBundle\Entity\UserRegular $invitationFor
     * @return Invitation
     */
    public function setInvitationFor(\Fenchy\RegularUserBundle\Entity\UserRegular $invitationFor = null)
    {
        $this->invitation_for = $invitationFor;
        if (null === $invitationFor) {
            $invitationFor->removeInvitationsForMe($this);
        } else {
            $invitationFor->addInvitationsForMe($this);
        }
    
        return $this;
    }

    /**
     * Get invitation_for
     *
     * @return Fenchy\RegularUserBundle\Entity\UserRegular 
     */
    public function getInvitationFor()
    {
        return $this->invitation_for;
    }
    

    /**
     * Get accepted
     *
     * @return boolean 
     */
    public function getAccepted()
    {
        return $this->accepted;
    }
}