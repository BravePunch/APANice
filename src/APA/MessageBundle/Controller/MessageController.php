<?php

namespace APA\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APA\SecurityBundle\Entity\User;

class MessageController extends Controller
{
    public function inboxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Gets the logged in user as an object.
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $listMessage = $em->getRepository("APAMessageBundle:Message")->findBy(array(
            "user" => $currentUser->getId(),
        ));

        return $this->render('APAMessageBundle:Message:inbox.html.twig', array(
            "listMessage" => $listMessage,
        ));
    }
}