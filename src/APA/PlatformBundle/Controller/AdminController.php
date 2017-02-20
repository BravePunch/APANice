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

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('APAPlatformBundle:Admin:index.html.twig');
    }

    public function dynamicSearchAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }

            $em = $this->getDoctrine()->getManager();  //On récuprer l'entity manager
            $motcle = '';                              //la Variable $motcle est initialisé de type string vide
            $motcle = $request->attributes->get('search');  //la Variable $motcle contient maintenant l'attribut 'search' , l'argument de la route menant a cette fonction

            if ($motcle != '') //Si la variable $motcle n'est plus un string vide
            {
                $qb = $em->createQueryBuilder()->select('u')->from('APASecurityBundle:User' , 'u')->where('u.nom LIKE :motcle OR u.prenom LIKE :motcle')
                                                            ->orderBy('u.nom' , 'ASC')->setParameter('motcle' , '%'.$motcle.'%'); //On éféctue la recherche dans la base de donnée avec le $motcle
                $listUser = $qb->getQuery()->getResult(); //on fait afficher le résultat

            }
            else
            {
                $listUser = $em->getRepository('APASecurityBundle:User')->findAll(); //Si le $motcle est toujours = a un string vide , on fait tout affiché
            }


        $isAdmin = array('ROLE_ADMIN');//Permet de vérifier le role des USER pour ne pas affiché l'admin dans adminIndex.html.twig , je n'est trouvé que cette méthode ...
        return $this->render('APAPlatformBundle:Admin:listuser.html.twig' , array('listUsers' => $listUser , 'isAdmin' => $isAdmin)); //On fait afficher le template listuser.html.twig

    }

    public function adminIndexAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }

        $em = $this->getDoctrine()->getManager();



        if ($request->isMethod('POST'))
        {

            $search = $request->request->get('search'); //Filtre les noms de l'entitée USER avec le POST 'search'

            $search1 = explode(' '  , $search);

            $listUser = $em->getRepository('APASecurityBundle:User')->findUser($search1);

        }
        else
        {
            $listUser =  $em->getRepository('APASecurityBundle:User')->findBy(array(), array('nom' => 'desc'), 10);
        }





        $isAdmin = array('ROLE_ADMIN'); //Permet de vérifier le role des USER pour ne pas affiché l'admin dans adminIndex.html.twig , je n'est trouvé que cette méthode ...

        if ($request->isXmlHttpRequest())  //Si la requete est de type AJAX
        {
            unset($listUser);   //On supprime la liste actuelle pour le besoin des conditions dans la vue

            $motcle = $request->request->get('motcle');  //On récupere le motclé envoyer par AJAX

            return $this->redirectToRoute('apa_platform_listUser' , array('search' => $motcle));  //On redirige vers la route  "apa_platform_listUser"
        }

        return $this->render('APAPlatformBundle:Admin:adminIndex.html.twig' , array('listUser' => $listUser , 'isAdmin' => $isAdmin));

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
//                ->add('plainPassword', RepeatedType::class, array(
//                        'type'           =>  PasswordType::class,
//                        'first_options'  =>  array('label' => 'Mot de passe'),
//                        'second_options' =>  array('label' => 'Confirmer mot de passe'),))
                ->add('nom',           TextType::class)
                ->add('prenom',        TextType::class)
                ->add('isAdmin',       CheckboxType::class, array('required' => false))
                ->add('save',          SubmitType::class)
                ->getForm()
                ;

        if ($request->isMethod('POST')){

            $form->handleRequest($request);

            if ($form->isValid()){

                $userCheck = $em->getRepository('APASecurityBundle:User')
                    ->findBy(array('username'=>$user->getUsername()));

                if ($userCheck){
                    throw new AccessDeniedException('Username déjà utilisé.');
                }

                $password = $this->get('security.password_encoder')
                        ->encodePassword($user, $user->getUserName());
                $user->setPassword($password);

                if ($user->getIsAdmin() === true){
                    $user->setRoles(array("ROLE_ADMIN"));
                } else{
                    $user->setRoles(array("ROLE_USER"));
                }

                $user->setSalt('salt');

                $em->persist($user);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notif', "Utilisateur enregistré.");

                return $this->redirectToRoute('apa_platform_adminIndex');

            }

        }

        return $this->render('APAPlatformBundle:Admin:addUser.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function deleteUserAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('APASecurityBundle:User');

        $user = $repository->find($id);

        if ($user === null){
            throw new NotFoundHttpException("L'utilisateur " . $id . " n'existe pas.");
        } else if($user->getRoles() == array("ROLE_ADMIN")){
            throw new AccessDeniedException("Opération impossible.");
        } else {
            $em->remove($user);
            $em->flush();
        }

        $request->getSession()->getFlashBag()->add('notif', "Utilisateur supprimé.");

        return $this->redirectToRoute('apa_platform_adminIndex');

    }

    public function editUserAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('APASecurityBundle:User');

        $user = $repository->find($id);

        if ($user === null){
            throw new NotFoundHttpException("Cet utilisateur n'existe pas.");
        } else if ($user->getRoles() == array("ROLE_ADMIN")){
            throw new AccessDeniedException("Opération impossible.");
        }

        $form = $this->get('form.factory')->createBuilder(Formtype::class, $user)
//                ->add('username',      TextType::class)
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

                $em->flush();

                $request->getSession()->getFlashBag()->add('notif', "Informations modifiées.");

                return $this->redirectToRoute('apa_platform_adminIndex');

            }

        }

        return $this->render('APAPlatformBundle:Admin:editUser.html.twig', array(
            "form"=>$form->createView(),
            "id"=>$id));

    }

    public function MenuAction()
    {
        return $this->render('APAPlatformBundle:Admin:menu.html.twig');
    }

    public function tableauDeBordAction()
    {
        return $this->render('APAPlatformBundle:Admin:tableauDeBord.html.twig');
    }

}
