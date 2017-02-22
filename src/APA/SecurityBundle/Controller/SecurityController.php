<?php

namespace APA\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityController extends Controller {

    // The "/login" page's controller.
    public function loginAction(Request $request){

        // If the user is already logged in, he is redirected to "/"
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                return $this->redirectToRoute('apa_platform_redirection');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('APASecurityBundle:Security:login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError()
            ));

    }

<<<<<<< HEAD
    // Ceci est la page / , elle sert à rediriger l'utilisateur selon son statut après un login.
=======
    // This is the "/" page. The login page redirects to it upon successful login.
    // It doesn't display anything and is only used for redirection.
    // For example, the admin is redirected to "/admin".
>>>>>>> master
    public function redirectionAction(Request $request){

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('apa_platform_adminIndex');
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            return $this->redirectToRoute('apa_platform_userIndex');
        }

    }

}
