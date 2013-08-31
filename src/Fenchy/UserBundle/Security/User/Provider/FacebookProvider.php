<?php

namespace Fenchy\UserBundle\Security\User\Provider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\NoticeBundle\Entity\Notice;
use Fenchy\NoticeBundle\Entity\FacebookFeed;
use \BaseFacebook;
use \FacebookApiException;

class FacebookProvider implements UserProviderInterface
{
    /**
     * @var \Facebook
     */
    protected $facebook;
    /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
    protected $userManager;
    /* @var $validator \Symfony\Component\Validator\Validator */
    protected $validator;

    protected $container;
    
    public function __construct(BaseFacebook $facebook, $userManager, $validator, $container)
    {
        $this->facebook = $facebook;
        $this->userManager = $userManager;
        $this->validator = $validator;
        $this->container = $container;
    }

    public function supportsClass($class)
    {
        return $this->userManager->supportsClass($class);
    }

    /**
     *
     * @param int|string $fbId
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function findUserByFbId($fbId)
    {
        return $this->userManager->findUserBy(array('facebookId' => $fbId));
    }
    
    public function loadUserByUsername($username)
    {
        return $this->loadOrSaveUserByFbId($username);
    }

    public function loadOrSaveUserByFbId($fbId)
    {
        /* @var $user \Fenchy\UserBundle\Entity\User */
        $user = $this->findUserByFbId($fbId);

        if (null !== $user) {
            return $user;
        }
        
        // get data from facebook.com
        try {
            $fbdata = $this->facebook->api('/me');
        } catch (FacebookApiException $e) {
            $fbdata = null;
        }
        
        if (null === $fbdata) {
            return null;
        }

        // check if user is already in system, but without facebookId
        /* @var $user \Fenchy\UserBundle\Entity\User */
        $user = $this->userManager->findUserBy(array('registeredWith'=>'facebook',
                                                 'registeredWithId'=>$fbdata['id']));
    
        if (null !== $user) {
        	// user exists, but has no facebookId i.e. is disconnected
        	// mustn't get connected automatically
        	$existing =  array( 
        	    'user' => $user,
                'status' => 'exists_disconnected' );
			return $existing;
        } else { // user does not exist at all
            /* @var $user \Fenchy\UserBundle\Entity\User */
            $user = $this->userManager->createUser();
            $user->setPassword('');
            $tokenGenerator = new \FOS\UserBundle\Util\TokenGenerator();
            $user->setConfirmationToken($tokenGenerator->generateToken());
            $ru = $user->createRegular();
            
            if (isset($fbdata['id'])) {
                $user->setFacebookId($fbdata['id']);
				$user->setRegisteredWith('facebook');
				$user->setRegisteredWithId($fbdata['id']);
            }
            if (isset($fbdata['username']) && !empty($fbdata['username'])) {
                $username = $fbdata['username'];
            } else {
                //build username
                $username = uniqid();
//                $username = isset($fbdata['first_name']) ? $fbdata['first_name'] : '';
//                $username .= isset($fbdata['last_name']) ? $fbdata['last_name'] : '';
//                $username .= empty($username) ? rand() : '';
            }
            //make new username unique
            while (null !== $this->userManager->findUserByUsername($username)) {
//                $username .= rand(1, 9);
                $username = uniqid();
            }
            $user->setUsername($username);
            $user->addRole('ROLE_FULL_USER');
            
            if (isset($fbdata['email'])) {
                $user->setEmail($fbdata['email']);
            }
            
            if (isset($fbdata['first_name'])) {
                $ru->setFirstname($fbdata['first_name']);
            }
            if (isset($fbdata['last_name'])) {
                $ru->setLastname($fbdata['last_name']);
            }
            if (isset($fbdata['gender']) && ($fbdata['gender'] == 'male' || $fbdata['gender'] == 'female') ) {
                $ru->setGender('male' == $fbdata['gender'] ? UserRegular::GENDER_MALE : UserRegular::GENDER_FEMALE);
            }
            if (isset($fbdata['birthday']) && !empty($fbdata['birthday'])) {
                $ru->setBirthday(new \DateTime($fbdata['birthday']));
            }
            
            //@TODO: upload profile picture
        }
        
        if (($invalid = $this->validator->validate($user)) && count($invalid)) {
            // data not valid - do not log in
            
            foreach($invalid as $elem) {
                if($elem->getPropertyPath() == 'email') {
                    return 'Your facebook e-mail address is already registered on our platform. Please log in and connect your facebook account.';
                }
            }
            
            return 'Probably some data from Facebook is not valid for Fenchy.';
        }
        $this->container->get('fenchy.reputation_counter')->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_FB);
        $this->container->get('fenchy.reputation_counter')->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_PROFILE);
        $this->userManager->updateUser($user);

        return $user;
    }

    /*
	 * function loadUserFromFbMe
	 * Returns user data stored in Facebook profile, not in database
	 */
    public function loadUserFromFbMe() 
    {

        try {
            $fbdata = $this->facebook->api('/me');
        } catch (FacebookApiException $e) {
            $fbdata = null;
        }

        if (null === $fbdata) {
            return null;
        }

        $user = $this->userManager->createUser();
        $user->setPassword('');
        $tokenGenerator = new \FOS\UserBundle\Util\TokenGenerator();
        $user->setConfirmationToken($tokenGenerator->generateToken());
        $ru = $user->createRegular();
            
        if (isset($fbdata['id'])) {
            $user->setFacebookId($fbdata['id']);
        }
        if (isset($fbdata['username']) && !empty($fbdata['username'])) {
            $username = $fbdata['username'];
        } else {
            //build username
            $username = isset($fbdata['first_name']) ? $fbdata['first_name'] : '';
            $username .= isset($fbdata['last_name']) ? $fbdata['last_name'] : '';
            $username .= empty($username) ? rand() : '';
        }
        //make new username unique
        while (null !== $this->userManager->findUserByUsername($username)) {
            $username .= rand(1, 9);
        }
        $user->setUsername($username);
        
        if (isset($fbdata['email'])) {
            $user->setEmail($fbdata['email']);
        }
        
        if (isset($fbdata['first_name'])) {
            $ru->setFirstname($fbdata['first_name']);
        }
        if (isset($fbdata['last_name'])) {
            $ru->setLastname($fbdata['last_name']);
        }
        if (isset($fbdata['gender']) && ($fbdata['gender'] == 'male' || $fbdata['gender'] == 'female') ) {
            $ru->setGender('male' == $fbdata['gender'] ? UserRegular::GENDER_MALE : UserRegular::GENDER_FEMALE);
        }
        if (isset($fbdata['birthday']) && !empty($fbdata['birthday'])) {
            $ru->setBirthday(new \DateTime($fbdata['birthday']));
        }
        if (isset($fbdata['location']) && isset($fbdata['location']['name']) && !empty($fbdata['location']['name'])) {
            $ru->setCity($fbdata['location']['name']);
		}	

		return $user;
    }


    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user)) || !$user->getFacebookId()) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->findUserByFbId($user->getFacebookId());
    }

    public function postAsPhoto($nTitle, $nContent, $nLink, $nPhoto)
    {
        $user_id = $this->facebook->getUser();
        $this->facebook->setFileUploadSupport(true);
        $result = array();

        if ($user_id) {
            try {
                $photoPost = array();
                
                if ( !empty($nTitle) )
                    $photoPost['message'] = $nTitle."\n\n";
                
                else
                    $photoPost['message'] = '';
                
                if ( !empty($nContent) )
                    $photoPost['message'] .= $nContent;
                
                if ( !empty($nLink) )
                    $photoPost['message'] .= "\n\n".$nLink;
				
                $photoPost['source'] = '@'.$nPhoto;
                $ret_obj = $this->facebook->api('/me/photos', 'POST', $photoPost);

                $result['fbuserId'] = $user_id;
                $result['photoId'] = $ret_obj['id'];
                $result['postId'] = $ret_obj['post_id'];
            } 
            
            catch(FacebookApiException $e) {
                $result['error'] = 'FB Exception:'.
                    $e->getType().' '.$e->getMessage();
            }
        } else {
            $result['error'] = 'Failed to get user ID';
        }
        
        return $result;	
    }

	public function postAsPost($nTitle, $nContent, $nLink) 
	{
		$user_id = $this->facebook->getUser();
		$result = array();
		if ($user_id) {
		    try {
		    	$postPost = array();
				if ( !empty($nTitle) )
				    $postPost['message'] = $nTitle."\n\n";
                else
					$postPost['message'] = '';
				if ( !empty($nContent) )
				    $postPost['message'] .= $nContent;
				if ( !empty($nLink) )
				    $postPost['link'] = $nLink;
                $ret_obj = $this->facebook->api('/me/feed', 'POST', $postPost);
				
                $result['fbuserId'] = $user_id;
				$result['postId'] = $ret_obj['id'];
            } catch(FacebookApiException $e) {
                $result['error'] = 'FB Exception:'.
                    $e->getType().' '.$e->getMessage();
            }
	   } else {
	   	   $result['error'] = 'Failed to get user ID';
	   }
	   return $result;
	}
        
    /**
     * Posts Notice on facebook
     * @param \Fenchy\UserBundle\Security\User\Provider\Notice $notice
     */
    public function post(Notice $notice) {
        
        $user = $notice->getUser();
        
//        if( !$user->getUserRegular()->getFacebookPublish() || !$notice->getPutOnFb()) return NULL;

        if ( $notice->getGallery()->isEmpty() ) {
            $fb_result = $this->postAsPost(
                    $notice->getTitle(),
                    $notice->getContent(),
                    $notice->getLink() 
                );
        }

        else {
            
            $fb_result = $this->postAsPhoto(
                        $notice->getTitle(),
                        $notice->getContent(),
                        $notice->getLink(),
//                        $this->container->get('templating.helper.assets')->getUrl(
//                                $notice->getGallery()->getFBImage()
//                            )
                        $this->container->getParameter('file_uploader.file_base_path').$notice->getGallery()->getFBImage()
                    );
        }

        if ( isset($fb_result['error']) ) {

            $logger = $this->container->get('logger');
            $logger->err($fb_result['error']);
            // return error
            return FALSE;
        }

        elseif ( isset($fb_result['fbuserId']) ) {
            
            $fb_feed = new FacebookFeed();
            $fb_feed->setFbUserId($fb_result['fbuserId']);

            if ( isset($fb_result['photoId'] ))
                $fb_feed->setPhotoId($fb_result['photoId']);

            $fb_feed->setPostId($fb_result['postId']);
            $fb_feed->setNotice($notice);

            return $fb_feed;
            
        }
    }
}