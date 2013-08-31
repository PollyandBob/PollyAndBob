<?php

namespace Fenchy\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FenchyFrontendBundle:Default:index.html.twig');
    }
}
