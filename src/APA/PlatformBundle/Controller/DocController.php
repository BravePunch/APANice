<?php

namespace APA\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use APA\SecurityBundle\Entity\User;
use APA\PlatformBundle\Entity\File;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DocController extends Controller
{

    /**
     * @Security("has_role('ROLE_DOC')")
     */
    public function menuAction()
    {
        return $this->render('APAPlatformBundle:Doc:menu.html.twig');
    }

    /**
     * @Security("has_role('ROLE_DOC')")
     */
    public function DocIndexAction()
    {
        return $this->render('APAPlatformBundle:Doc:index.html.twig');
    }

}
