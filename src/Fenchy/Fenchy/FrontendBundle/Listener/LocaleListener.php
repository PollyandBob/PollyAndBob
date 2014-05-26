<?php

namespace Fenchy\FrontendBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class LocaleListener {

    public function __construct(ContainerInterface $container) {
        $this -> container = $container;
    }

    public function onKernelRequest(FilterControllerEvent $e) {

        if (HttpKernel::MASTER_REQUEST != $e->getRequestType()) {
            return;
        }

        $controller = $e->getController();
        if ('toolbarAction' == $controller[1]) {
            return;
        }

        $context = $this->container->get('security.context');

        $this -> useBrowserLocale($e);
        // Re-changed By Jignesh
        $cookie_locale = $e->getRequest()->cookies->get('locale');
        if ( NULL != $cookie_locale &&  in_array($cookie_locale, array('pl', 'en', 'de'))) {
            $e->getRequest()->setLocale($cookie_locale);
        }

        if ($this->container->has('session')) {
            $sess_locale = $this->container->get('session')->get('locale');
            if (!empty($sess_locale)) {
                $e->getRequest()->setLocale($sess_locale);
            }
        }

        if ($context -> isGranted('IS_AUTHENTICATED_FULLY')) {
            $pref_locale = $context->getToken()->getUser()->getUserRegular()->getPrefLocale();
            if (!empty($pref_locale))
                $e->getRequest()->setLocale($pref_locale);
        }
    }

    private function useBrowserLocale($e) {
        $request = $e->getRequest();
        $preffered = strtolower(substr($request->getPreferredLanguage(), 0, 2));
        if (in_array($preffered, array('pl', 'en', 'de')))
            $request->setLocale($preffered);
    }

}
