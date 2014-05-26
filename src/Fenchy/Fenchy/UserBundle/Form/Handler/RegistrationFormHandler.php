<?php

namespace Fenchy\UserBundle\Form\Handler;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;

/**
 * Registration Form handler
 *
 * @category Fenchy
 * @package  Handler
 * @author   Grzegorz Wiater <gwiater@pgs-soft.com>
 */
class RegistrationFormHandler extends BaseHandler
{

    /**
     * @param boolean $confirmation
     */
    public function process($confirmation = false)
    {
        $user = $this->createUser();
        $username = uniqid();
        while (null !== $this->userManager->findUserByUsername($username)) {
            $username = uniqid();
        }
        $user->setUsername($username);
        $this->form->setData($user);

        if ('POST' === $this->request->getMethod()) {
            $this->form->bind($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($user, $confirmation);

                return true;
            }
        }

        return false;
    }	
	
	
	
	
	
	
	
    protected function onSuccess(UserInterface $user, $confirmation) {
        
        if ($confirmation) {
            $user->setEnabled(TRUE);
            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $this->mailer->sendConfirmationEmailMessage($user);
        } else {
            $user->setEnabled(true);
        }
		
        // write down how has the user registered
        $user->setRegisteredWith('email');
        $user->setRegisteredWithId($user->getEmail());
        $this->userManager->updateUser($user);

    }

}