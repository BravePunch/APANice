<?php

namespace Platform\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PlatformAdminBundle:Default:index.html.twig');
    }
}
