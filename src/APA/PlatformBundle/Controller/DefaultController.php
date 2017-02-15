<?php

namespace APA\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use APA\SecurityBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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

        return $this->render('APAPlatformBundle:Admin:adminIndex.html.twig');


        $em = $this->getDoctrine()->getManager();

        $listUser =  $em->getRepository('APASecurityBundle:User')->findBy(array(), array('nom' => 'desc'), 10, null);

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }

        return $this->render('APAPlatformBundle:Admin:adminIndex.html.twig' , array('listUser' => $listUser));

    }

    public function addUserAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }

        $em = $this->getDoctrine()->getManager();

        $user = new User();

        $form = $this->get('form.factory')->createBuilder(Formtype::class, $user)
                ->add('username',      TextType::class)
                ->add('plainPassword', RepeatedType::class, array(
                        'type'           => PasswordType::class,
                        'first_options'  => array('label' => 'Mot de passe'),
                        'second_options' => array('label' => 'Confirmer mot de passe'),))
                ->add('nom',           TextType::class)
                ->add('prenom',        TextType::class)
                ->add('save',          SubmitType::class)
                ->getForm()
                ;

        if ($request->isMethod('POST')){

            $form->handleRequest($request);

            if ($form->isValid()){

                $password = $this->get('security.password_encoder')
                        ->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);

                $user->setSalt('');
                $user->setRoles(array('ROLE_USER'));

                $userCheck = $em->getRepository('APASecurityBundle:User')
                        ->findBy(array('username'=>$user->getUsername()));

                if ($userCheck){
                    throw new AccessDeniedException('Username déjà utilisé.');
                }

                $user->setSalt('salt');
                $user->setRoles(array('ROLE_USER'));

                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('apa_platform_adminIndex');

            }

        }

        return $this->render('APAPlatformBundle:Admin:addUser.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function deleteUserAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('APASecurityBundle:User');

        $user = $repository->find($id);

        if ($user == null){
            throw new AccessDeniedException("L'utilisateur " . $id . " n'existe pas.");
        } else if($user->getRoles() == array("ROLE_ADMIN")){
            throw new AccessDeniedException("Opération impossible.");
        } else {
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('apa_platform_adminIndex');

    }

}
