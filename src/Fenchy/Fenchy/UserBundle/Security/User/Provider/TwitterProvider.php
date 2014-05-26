<?php
// src/Fenchy/UserBundle/Security/User/Provider/TwitterProvider.php

namespace Fenchy\UserBundle\Security\User\Provider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session;
use \TwitterOAuth;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Validator\Validator;
use Fenchy\UserBundle\Entity\User;
use FOS\UserBundle\Model\User as UserModel;

/**
 * Provider for handling twitter oauth.
 * 
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class TwitterProvider extends UserManager
{
    /** 
     * @var \Twitter $twitter_oauth
     */
    protected $twitter_oauth;
    
    /**
     * @var \FOS\UserBundle\Model\UserManager $userManager;
     */
//    protected $userManager;
    
    /**
     * @var Validator $validator
     */
    protected $validator;
    
    /**
     * @var Symfony\Component\HttpFoundation\Session\Session $session
     */
    protected $session;
    
    /**
     * Will be set to true if given credentials can be match with existing user.
     * 
     * @var bool $authorized
     */
    protected $authorized = FALSE;
    
    public function __construct(
            TwitterOAuth $twitter_oauth, 
            Validator $validator,  
            $session,
            $logger,
            // parent
            $encoder,
            $username_canonicalizer,
            $email_canonicalizer,
            $em,
            $user
            )
    {   
        $this->twitter_oauth    = $twitter_oauth;
        $this->validator        = $validator;
        $this->session          = $session;
        $this->logger           = $logger;
        
        parent::__construct($encoder, $username_canonicalizer, $email_canonicalizer, $em, $user);
    }

    /**
     * Return user entity with given twitter ID or NULL if not found.
     * 
     * @param integer $twitterID
     * @return mixed
     */
    public function findUserByTwitterId($twitterID)
    {
        return $this->findUserBy(array('twitterID' => $twitterID));
    }   

    /**
     * Return user entity with given twitter username or NULL if not found.
     * 
     * @param string $twitterUser
     * @return mixed
     */
    public function findUserByTwitterUser($twitterUser)
    {
        return $this->findUserBy(array('twitter_username' => $twitterUser));
    }
    
    /**
     * Logged in user by oAuth without store his data in DB. Useless in Fenchy.
     * 
     * @param integer $username
     * @return User
     */
    public function loadUserByUsername($username)
    {
         $user = $this->findUserByTwitterId($username);

         $this->twitter_oauth->setOAuthToken( $this->session->get('access_token') , $this->session->get('access_token_secret'));

        try {
             $info = $this->twitter_oauth->get('account/verify_credentials');
        } catch (Exception $e) {
             $info = null;
        }

        if (!empty($info)) {
            if (empty($user)) {
                $user = $this->createUser();
                $user->setEnabled(true);
                $user->setPassword('');
                $user->setAlgorithm('');
            }

            $username = $info->screen_name;

            $user->setTwitterID($info->id);
            $user->setTwitterUsername($username);
            $user->setEmail('');
            $user->setFirstname($info->name);

            $this->updateUser($user);
        }

        if (empty($user)) {
            throw new UsernameNotFoundException('The user is not authenticated on twitter');
        }

        return $user;

    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user)) || !$user->getTwitterID()) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getTwitterID());
    }
    
    /**
     * Do twitter authorization. Return existing user with responded twitterId
     * or new user with filled twitter data.
     * 
     * @param UserModel $sessionUser
     * 
     * @return User 
     */
    public function authorize($sessionUser)
    {
        !($sessionUser instanceof UserModel) && $sessionUser = NULL;
        
        // Service is created without access token, so we have to set it here.
        $this->twitter_oauth->setOAuthToken(
                $this->session->get('access_token'), 
                $this->session->get('access_token_secret')
                );

        // Try to get user data from twitter
        try {
            
             $info = $this->twitter_oauth->get('account/verify_credentials');
             
        } catch (Exception $e) {
            
             return FALSE;
        }

        if(!isset($info->id) || !isset($info->screen_name)) {
            
            $this->logger->err('TwitterProvider: Missing data in Twitter respons: '.print_r($info, TRUE));
            return FALSE;
        }

        // Try to find user with ID or name from twitter
        $user = $this->findUserByTwitterId($info->id);
            
        // Check if user is logged in
        // If user is logged connect his account to twitter
        if(!empty($sessionUser)) {
            
            // Don't update if id has not been changed
            if($sessionUser->getTwitterID() != $info->id) {

                // If twitter id is in use by other user return FALSE;
                if(!empty($user) && $sessionUser->getId() != $user->getId()) {
                    
                    return FALSE;
                }
                
                $sessionUser->setTwitterID($info->id);
                $sessionUser->setLastLogin(new \DateTime());
                
                if(!$sessionUser->getUsername()) {
                    
                    $sessionUser->setTwitterUsername($info->screen_name);
                }
                
                $this->updateUser($sessionUser);
            }
            
            $this->authorized = TRUE;
            
            return $sessionUser;
            
        } else {

            // If user is emty, then it is new user so we want to create account for
            // him. Otherwise we can say that he is authorized.
            if(empty($user)) {

                $user = new User();
                $user->setUsername($info->screen_name);
                $user->setTwitterID($info->id);
                $user->setTwitterUsername($info->screen_name);
                $user->setEmail("");
                $user->setPassword('');
                $user->setLastLogin(new \DateTime());
                $user->setRegularUser(new \Fenchy\RegularUserBundle\Entity\UserRegular());
                if(isset($info->name)) {
                    $name = explode(' ', $info->name);
                    $user->getRegularUser()->setFirstname($name[0]);
                    if(count($name) == 2) {
                        $user->getRegularUser()->setLastname($name[1]);
                    }
                }

            } else {

                $this->authorized = TRUE;
            }

        }
        
        return $user;
    }
    
    /**
     * 1st twitter response contain oauth_token and oauth_verifier only. To get
     * user data we need access token. This method will get it and store in session.
     * 
     * Return accessToken array or NULL on fail.
     * 
     * @param string $oauthToken
     * @param string $oauthVerifier
     * @return mixed 
     */
    public function getAccessToken($oauthToken, $oauthVerifier)
    {
        //set OAuth token in the API
        $this->twitter_oauth->setOAuthToken($oauthToken, $this->session->get('oauth_token_secret'));

        /* Check if the oauth_token is old */
        if ($this->session->has('oauth_token')) {
            if ($this->session->get('oauth_token') && ($this->session->get('oauth_token') !== $oauthToken)) {
                $this->session->remove('oauth_token');
                return NULL;
            }
        }

        /* Request access tokens from twitter */
        $accessToken = $this->twitter_oauth->getAccessToken($oauthVerifier);
        
        // Failure when oauth_token or oauth_token_secret does not exists
        if(!array_key_exists('oauth_token', $accessToken) || !array_key_exists('oauth_token_secret', $accessToken)) {
            
            return NULL;
        }
        
        /* Save the access tokens. Normally these would be saved in a database for future use. */
        $this->session->set('access_token', $accessToken['oauth_token']);
        $this->session->set('access_token_secret', $accessToken['oauth_token_secret']);

        /* Remove no longer needed request tokens */
        !$this->session->has('oauth_token') ?: $this->session->remove('oauth_token', null);
        !$this->session->has('oauth_token_secret') ?: $this->session->remove('oauth_token_secret', null);

        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $this->twitter_oauth->http_code) {
            /* The user has been verified and the access tokens can be saved for future use */
            return $accessToken;
        }

        /* Return null for failure */
        return NULL;
    }
    
    /**
     * Checks if given user have the same twitter credentials as this stored in
     * session.
     * 
     * @uses To avoid changing twitter data between oAuth and filling twitter
     *      registration form.
     * 
     * @param User $user
     * @return bool
     */
    public function isAuthorizedUser(User $user) {
        
        // set token
        $this->twitter_oauth->setOAuthToken( $this->session->get('access_token') , $this->session->get('access_token_secret'));
        
        // try to get user data from twitter. For security return FALSE on fail
        try {
            
             $info = $this->twitter_oauth->get('account/verify_credentials');
             
        } catch (Exception $e) {
            
             return FALSE;
        }
        
        if(!isset($info->id) || !isset($info->screen_name)) {
            
            $this->logger->err('TwitterProvider: Missing data in Twitter respons: '.print_r($info, TRUE));
            return FALSE;
        }
        
        // Return TRUE only if both responded ID an NAME IS same as User equivalents.
        return $user->getTwitterID() == $info->id && $user->getTwitterUsername() == $info->screen_name;
    }
    
    /**
     * Returns flag set by authorize() method.
     * It will be TRUE only if responded data is equal to existing user.
     * 
     * @return bool
     */
    public function isAuthorized() {
        
        return $this->authorized;
    }
}