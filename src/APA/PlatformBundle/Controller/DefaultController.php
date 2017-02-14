<?php

namespace APA\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('APAPlatformBundle:Default:index.html.twig');
    }

    public function adminIndexAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }

        return $this->render('APAPlatformBundle:Default:adminIndex.html.twig');
    }
}
