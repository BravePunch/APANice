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

        // Gets the list of relevant users
        $userQuery = $em->createQueryBuilder()
                ->select("a")
                ->from("APASecurityBundle:User", "a")
                ->where("a.groupe = :groupe")
                ->orWhere("a.roles = :roles")
                ->orderBy("a.id", "DESC")
                ->setParameter("groupe", $currentUser->getGroupe())
                ->setParameter("roles", array("ROLE_PROF"));

        $listUsers = $userQuery->getQuery()->getResult();

        // Gets the latest messages

        $latestMessages = $em->getRepository("APAMessageBundle:Message")->findBy(
                array("user" => $currentUser->getId()),
                array("id" => 'desc'),
                50
                );

        return $this->render("APAMessageBundle:Message:inbox.html.twig", array(
            "currentUser" => $currentUser,
            "listUsers"   => $listUsers,
            "latestMessages" => $latestMessages
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
