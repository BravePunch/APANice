<?php

namespace APA\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityController extends Controller {

    public function loginAction(Request $request){

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                return $this->redirectToRoute('apa_platform_redirection');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('APASecurityBundle:Security:login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError()
            ));

    }

    // Ceci est la page / , elle ser à rediriger l'utilisateur selon son statut.
    public function redirectionAction(Request $request){

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('apa_platform_adminIndex');
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            return $this->redirectToRoute('apa_platform_userIndex');
        }

    }

}
