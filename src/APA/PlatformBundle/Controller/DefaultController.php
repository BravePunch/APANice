<?php

namespace APA\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('APAPlatformBundle:Default:index.html.twig');
    }
}
