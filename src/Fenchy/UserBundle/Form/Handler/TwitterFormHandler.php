<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fenchy\UserBundle\Form\Handler;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\UserBundle\Entity\User;

class TwitterFormHandler
{
    /**
     * @var Request $request
     */
    protected $request;
    
    /**
     * @var TwitterProvider $userManager;
     */
    protected $userManager;
    
    /**
     * @var UserRegularTwitterType $form
     */
    protected $form;
    
    protected $mailer;
    
    /**
     * @var TokenGenerator $tokenGenerator
     */
    protected $tokenGenerator;
    
	protected $user;

    public function __construct(UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $regularUser = new UserRegular();
        $this->user = $this->createUser();
        $this->user->setRegularUser($regularUser);
    }
    
    public function setForm(FormInterface $form) {
        
        $this->form = $form;
    }
    
    public function setRequest($request) {
        $this->request = $request;
    }
    
    public function getUser() {
        
        return $this->user;
    }

    public function process($confirmation = false)
    {
        
        if ('POST' === $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

		   /*
            * If user doesn't exist 
            */
           if(null == $this->getUser()->getTwitterId())
		      return false;

            // Check by TwitterProvider if given data is equal to data from twitter
            // response.
            if(!$this->userManager->isAuthorizedUser($this->getUser())) {

                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
            }

            if ($this->form->isValid()) {
                $this->onSuccess($this->getUser(), $confirmation);

                return true;
            }
        }

        return false;
    }

    protected function onSuccess(UserInterface $user, $confirmation)
    {
        if ($confirmation) {
            
            $user->setEnabled(false);
            if (null === $user->getConfirmationToken()) {
                
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $this->mailer->sendConfirmationEmailMessage($user);
            
        } else {
            
            $user->setEnabled(true);
        }
        
        #$user->setUsername($user->getTwitterUsername());
        // Assume that this happens ONLY on new user creation, not on update
		$user->setRegisteredWith('twitter');
		$user->setRegisteredWithId($user->getTwitterId());
        $user->setPassword(sha1(uniqid()));
        $this->userManager->updateUser($user);
    }

    protected function createUser()
    {
        return $this->userManager->createUser();
    }
}
