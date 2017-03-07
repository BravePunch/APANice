<?php

namespace APA\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use APA\SecurityBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class UserController extends Controller
{
  /**
   * @Security("has_role('ROLE_USER')")
   */
    public function userIndexAction(Request $request){

        return $this->render('APAPlatformBundle:User:index.html.twig');

    }

    // This action is called by the twig. It displays the content of the sidebar.
    public function MenuAction()
    {
        return $this->render('APAPlatformBundle:User:menu.html.twig');
    }

    // Same as MenuAction for the index's body.
    public function tableauDeBordAction()
    {
        return $this->render('APAPlatformBundle:User:tableauDeBord.html.twig');
    }

    public function dossierAction()
    {
        return $this->render('::chantier.html.twig');
    }

    public function staffAction()
    {
        return $this->render('::chantier.html.twig');
    }

    public function ficheAction()
    {
        return $this->render('::chantier.html.twig');
    }

    public function settingsAction()
    {
        return $this->render('::chantier.html.twig');
    }
}

