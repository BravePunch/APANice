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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APA\MessageBundle\Entity\Message;

class MessageController extends Controller
{
    public function inboxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Gets the logged in user as an object.
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $query = $em->createQueryBuilder()
                ->select('a')
                ->from('APAMessageBundle:Message', 'a')
                ->where('a.user = :user')
                ->orderBy('a.date', 'DESC')
                ->setMaxResults(5)
                ->setParameter('user', $currentUser->getId());

        // Gets messages as objects by their user_id, DESC order
        $listMessage = $query->getQuery()->getResult();

        return $this->render('APAMessageBundle:Message:inbox.html.twig', array(
            "listMessage" => $listMessage
        ));
    }

    public function listUsersAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        // Gets the logged in user as an object.
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $listUsers = $em->getRepository("APASecurityBundle:User")->findBy(array(
            "groupe" => $currentUser->getGroupe()
        ));

        return $this->render("APAMessageBundle:Message:listUsers.html.twig", array(
            "currentUser" => $currentUser,
            "listUsers"   => $listUsers
        ));

    }

    public function sendMessageAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        // Gets the logged in user as an object
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        // Gets the target of the message as an object
        $targetUser = $em->getRepository("APASecurityBundle:User")->find($id);

        // Gets the 5 last messages between current user and target user
        $query = $em->createQueryBuilder()
                ->select('a')
                ->from('APAMessageBundle:Message', 'a')
                ->where('a.sender = :sender AND a.user = :user')
                ->orWhere('a.sender = :user AND a.user = :sender')
                ->orderBy('a.date', 'DESC')
                ->setMaxResults(5)
                ->setParameter('sender', $targetUser->getId())
                ->setParameter('user'  , $currentUser->getId());

        $convo = $query->getQuery()->getResult();

        $message = new Message();

        $form = $this->get('form.factory')->createBuilder(FormType::class, $message)
                ->add('content', TextareaType::class, array('label'=>false))
                ->add('Submit',  SubmitType::class)
                ->getForm();

        if ($request->isMethod("POST")){

            $form->handleRequest($request);

            if ($form->isValid()){

                $message->setSender($currentUser);

                $message->setUser($targetUser);

                $message->setAuthor($currentUser->getnom() . " " . $currentUser->getPrenom());

                $em->persist($message);

                $em->flush();

                return $this->redirectToRoute('apa_message_sendMessage', array("id"=>$id));

            }

        }

        return $this->render("APAMessageBundle:Message:sendMessage.html.twig", array(
           "targetUser" => $targetUser,
           "form"       => $form->createView(),
           "convo"      => array_reverse($convo)
        ));

    }
}
