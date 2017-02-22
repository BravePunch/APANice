<?php

namespace APA\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
<<<<<<< HEAD
=======
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
>>>>>>> master
use APA\SecurityBundle\Entity\User;

class AdminController extends Controller
{
<<<<<<< HEAD
=======
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
>>>>>>> master
    public function adminIndexAction()
    {
        return $this->render('APAPlatformBundle:Admin:index.html.twig');
    }

<<<<<<< HEAD
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
=======
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
    public function dynamicSearchAction(Request $request)
    {

            // Gets the entity manager.
            $em = $this->getDoctrine()->getManager();
            // $motcle is initialized as an empty string.
            $motcle = '';
            // Sets $motcle as the search attribute (the search field)
            $motcle = $request->attributes->get('search');

            // If $motcle is not empty
            if ($motcle != '')
            {
                // We look for $motcle inside the "user" table.
                $qb = $em->createQueryBuilder()
                        ->select('u')
                        ->from('APASecurityBundle:User' , 'u')
                        ->where('u.nom LIKE :motcle OR u.prenom LIKE :motcle')
                        ->orderBy('u.nom' , 'ASC')
                        ->setParameter('motcle' , '%'.$motcle.'%');

                // Gets the results.
                $listUser = $qb->getQuery()->getResult();
>>>>>>> master

            }
            else
            {
<<<<<<< HEAD
                $listUser = $em->getRepository('APASecurityBundle:User')->findAll(); //Si le $motcle est toujours = a un string vide , on fait tout affiché
            }


        $isAdmin = array('ROLE_ADMIN');//Permet de vérifier le role des USER pour ne pas affiché l'admin dans adminIndex.html.twig , je n'est trouvé que cette méthode ...
=======
                // If $motcle is empty, display all users.
                $listUser = $em->getRepository('APASecurityBundle:User')->findAll();
            }


        // $isAdmin is used to check if the user is admin, in order to keep it hidden.
        $isAdmin = array('ROLE_ADMIN');
>>>>>>> master
        return $this->render('APAPlatformBundle:Admin:listuser.html.twig' , array('listUsers' => $listUser , 'isAdmin' => $isAdmin)); //On fait afficher le template listuser.html.twig

    }

<<<<<<< HEAD
    public function adminListeAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }

        $em = $this->getDoctrine()->getManager();



        if ($request->isMethod('POST'))
        {

            $search = $request->request->get('search'); //Filtre les noms de l'entitée USER avec le POST 'search'
=======
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
    public function adminListeAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        // If the the user has pressed the search button
        if ($request->isMethod('POST'))
        {

            // Looks for the search input value in the "user" table
            $search = $request->request->get('search');
>>>>>>> master

            $search1 = explode(' '  , $search);

            $listUser = $em->getRepository('APASecurityBundle:User')->findUser($search1);

        }
        else
        {
            $listUser =  $em->getRepository('APASecurityBundle:User')->findBy(array(), array('nom' => 'desc'), 10);
        }

<<<<<<< HEAD
        $isAdmin = array('ROLE_ADMIN'); //Permet de vérifier le role des USER pour ne pas affiché l'admin dans adminIndex.html.twig , je n'est trouvé que cette méthode ...

        if ($request->isXmlHttpRequest())  //Si la requete est de type AJAX
        {
            unset($listUser);   //On supprime la liste actuelle pour le besoin des conditions dans la vue

            $motcle = $request->request->get('motcle');  //On récupere le motclé envoyer par AJAX

            return $this->redirectToRoute('apa_platform_listUser' , array('search' => $motcle));  //On redirige vers la route  "apa_platform_listUser"
=======
        // Used to check for admin rights
        $isAdmin = array('ROLE_ADMIN');

        // If request is AJAX
        if ($request->isXmlHttpRequest())
        {
            // Unsets the current user list
            unset($listUser);

            // Gets $motcle in the search input
            $motcle = $request->request->get('motcle');

            // Redirects to apa_platform_listuser with $motcle as argument
            return $this->redirectToRoute('apa_platform_listUser' , array('search' => $motcle));
>>>>>>> master
        }

        return $this->render('APAPlatformBundle:Admin:adminListe.html.twig' , array('listUser' => $listUser , 'isAdmin' => $isAdmin));

    }

<<<<<<< HEAD
    public function addUserAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }
=======
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
    public function addUserAction(Request $request)
    {
>>>>>>> master

        $em = $this->getDoctrine()->getManager();

        $user = new User();

<<<<<<< HEAD
=======
        // Creates a form for a new User
>>>>>>> master
        $form = $this->get('form.factory')->createBuilder(Formtype::class, $user)
                ->add('username',      TextType::class)
//                ->add('plainPassword', RepeatedType::class, array(
//                        'type'           =>  PasswordType::class,
//                        'first_options'  =>  array('label' => 'Mot de passe'),
//                        'second_options' =>  array('label' => 'Confirmer mot de passe'),))
                ->add('nom',           TextType::class)
                ->add('prenom',        TextType::class)
                ->add('groupe' ,       ChoiceType::class, array('choices' => array('' => '','Groupe 1' => null , 'Groupe 2' => false , 'Groupe 3' => true)))
                ->add('isAdmin',       CheckboxType::class, array('required' => false))
                ->add('save',          SubmitType::class)
                ->getForm()
                ;

<<<<<<< HEAD
=======
        // If the form has been submitted
>>>>>>> master
        if ($request->isMethod('POST')){

            $form->handleRequest($request);

<<<<<<< HEAD
            if ($form->isValid()){

=======
            // If the form has been filled properly
            if ($form->isValid()){

                // Checks if the username isn't already in use
>>>>>>> master
                $userCheck = $em->getRepository('APASecurityBundle:User')
                    ->findBy(array('username'=>$user->getUsername()));

                if ($userCheck){
                    throw new AccessDeniedException('Username déjà utilisé.');
                }

<<<<<<< HEAD
=======
                // Sets username as password and encodes it
>>>>>>> master
                $password = $this->get('security.password_encoder')
                        ->encodePassword($user, $user->getUserName());
                $user->setPassword($password);

<<<<<<< HEAD
=======
                // Sets roles depending on the value of the checkbox
>>>>>>> master
                if ($user->getIsAdmin() === true){
                    $user->setRoles(array("ROLE_ADMIN"));
                } else{
                    $user->setRoles(array("ROLE_USER"));
                }
                
<<<<<<< HEAD
                  if ($user->getGroupe() === null){
=======
                // Sets groupe depending on the value of the choice list
                if ($user->getGroupe() === null){
>>>>>>> master
                    $user->setGroupe("Groupe 1");
                } elseif ($user->getGroupe() === false){
                    $user->setGroupe("Groupe 2");
                } elseif ($user->getGroupe() === true){
                    $user->setGroupe("Groupe 3");
                }
                else {
                    $user->setGroupe(null);
                }

<<<<<<< HEAD
                $user->setSalt('salt');

                $em->persist($user);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notif', "Utilisateur enregistré.");

=======
                // Salt is unused
                $user->setSalt('salt');

                // Creates the new User
                $em->persist($user);
                $em->flush();

                // Creates a flash message
                $request->getSession()->getFlashBag()->add('notif', "Utilisateur enregistré.");

                // Redirects to the user list
>>>>>>> master
                return $this->redirectToRoute('apa_platform_adminListe');

            }

        }

<<<<<<< HEAD
=======
        // If the method isn't POST, displays the form
>>>>>>> master
        return $this->render('APAPlatformBundle:Admin:addUser.html.twig', array(
            'form' => $form->createView()
        ));
    }

<<<<<<< HEAD
    public function deleteUserAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }
=======

    // Deletes the user with the id given in the url
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
    public function deleteUserAction(Request $request, $id)
    {
>>>>>>> master

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('APASecurityBundle:User');

        $user = $repository->find($id);

<<<<<<< HEAD
=======
        // Checks if the user we want to delete exists and is not admin
>>>>>>> master
        if ($user === null){
            throw new NotFoundHttpException("L'utilisateur " . $id . " n'existe pas.");
        } else if($user->getRoles() == array("ROLE_ADMIN")){
            throw new AccessDeniedException("Opération impossible.");
        } else {
            $em->remove($user);
            $em->flush();
        }

<<<<<<< HEAD
        $request->getSession()->getFlashBag()->add('notif', "Utilisateur supprimé.");

=======
        // Creates a flash message
        $request->getSession()->getFlashBag()->add('notif', "Utilisateur supprimé.");

        // Redirects to the user list
>>>>>>> master
        return $this->redirectToRoute('apa_platform_adminListe');

    }

<<<<<<< HEAD
    public function editUserAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé.');
        }
=======
    // Edit the user with the id given in the url
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
    public function editUserAction(Request $request, $id)
    {
>>>>>>> master

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('APASecurityBundle:User');

        $user = $repository->find($id);

<<<<<<< HEAD
=======
        // Checks if the user we want to edit exists and is not admin
>>>>>>> master
        if ($user === null){
            throw new NotFoundHttpException("Cet utilisateur n'existe pas.");
        } else if ($user->getRoles() == array("ROLE_ADMIN")){
            throw new AccessDeniedException("Opération impossible.");
        }

<<<<<<< HEAD
=======
        // Creates a form with the existing user as argument
>>>>>>> master
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

<<<<<<< HEAD
=======
        // If the form is submitted
>>>>>>> master
        if ($request->isMethod('POST')){

            $form->handleRequest($request);

<<<<<<< HEAD
            if ($form->isValid()){

=======
            // If the form is valid
            if ($form->isValid()){

                // Encodes the plain password
>>>>>>> master
                $password = $this->get('security.password_encoder')
                        ->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);

<<<<<<< HEAD
                $em->flush();

                $request->getSession()->getFlashBag()->add('notif', "Informations modifiées.");

=======
                // Updates the user
                $em->flush();

                // Creates a flash message
                $request->getSession()->getFlashBag()->add('notif', "Informations modifiées.");

                // Redirects to the user list
>>>>>>> master
                return $this->redirectToRoute('apa_platform_adminListe');

            }

        }

<<<<<<< HEAD
=======
        // If the method is not POST, displays the form
>>>>>>> master
        return $this->render('APAPlatformBundle:Admin:editUser.html.twig', array(
            "form"=>$form->createView(),
            "id"=>$id));

    }

<<<<<<< HEAD
=======
    // This action is called in the twig. It displays the content of the sidebar.
>>>>>>> master
    public function MenuAction()
    {
        return $this->render('APAPlatformBundle:Admin:menu.html.twig');
    }

<<<<<<< HEAD
=======
    // Same as MenuAction. Displays the index's body.
>>>>>>> master
    public function tableauDeBordAction()
    {
        return $this->render('APAPlatformBundle:Admin:tableauDeBord.html.twig');
    }

}
