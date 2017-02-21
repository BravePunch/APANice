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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APA\SecurityBundle\Entity\User;

class AdminController extends Controller
{
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
    public function adminIndexAction()
    {
        return $this->render('APAPlatformBundle:Admin:index.html.twig');
    }

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

            }
            else
            {
                // If $motcle is empty, display all users.
                $listUser = $em->getRepository('APASecurityBundle:User')->findAll();
            }


        // $isAdmin is used to check if the user is admin, in order to keep it hidden.
        $isAdmin = array('ROLE_ADMIN');
        return $this->render('APAPlatformBundle:Admin:listuser.html.twig' , array('listUsers' => $listUser , 'isAdmin' => $isAdmin)); //On fait afficher le template listuser.html.twig

    }

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

            $search1 = explode(' '  , $search);

            $listUser = $em->getRepository('APASecurityBundle:User')->findUser($search1);

        }
        else
        {
            $listUser =  $em->getRepository('APASecurityBundle:User')->findBy(array(), array('nom' => 'desc'), 10);
        }

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
        }

        return $this->render('APAPlatformBundle:Admin:adminListe.html.twig' , array('listUser' => $listUser , 'isAdmin' => $isAdmin));

    }

  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
    public function addUserAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $user = new User();

        // Creates a form for a new User
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

        // If the form has been submitted
        if ($request->isMethod('POST')){

            $form->handleRequest($request);

            // If the form has been filled properly
            if ($form->isValid()){

                // Checks if the username isn't already in use
                $userCheck = $em->getRepository('APASecurityBundle:User')
                    ->findBy(array('username'=>$user->getUsername()));

                if ($userCheck){
                    throw new AccessDeniedException('Username déjà utilisé.');
                }

                // Sets username as password and encodes it
                $password = $this->get('security.password_encoder')
                        ->encodePassword($user, $user->getUserName());
                $user->setPassword($password);

                // Sets roles depending on the value of the checkbox
                if ($user->getIsAdmin() === true){
                    $user->setRoles(array("ROLE_ADMIN"));
                } else{
                    $user->setRoles(array("ROLE_USER"));
                }
                
                // Sets groupe depending on the value of the choice list
                if ($user->getGroupe() === null){
                    $user->setGroupe("Groupe 1");
                } elseif ($user->getGroupe() === false){
                    $user->setGroupe("Groupe 2");
                } elseif ($user->getGroupe() === true){
                    $user->setGroupe("Groupe 3");
                }
                else {
                    $user->setGroupe(null);
                }

                // Salt is unused
                $user->setSalt('salt');

                // Creates the new User
                $em->persist($user);
                $em->flush();

                // Creates a flash message
                $request->getSession()->getFlashBag()->add('notif', "Utilisateur enregistré.");

                // Redirects to the user list
                return $this->redirectToRoute('apa_platform_adminListe');

            }

        }

        // If the method isn't POST, displays the form
        return $this->render('APAPlatformBundle:Admin:addUser.html.twig', array(
            'form' => $form->createView()
        ));
    }


    // Deletes the user with the id given in the url
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
    public function deleteUserAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('APASecurityBundle:User');

        $user = $repository->find($id);

        // Checks if the user we want to delete exists and is not admin
        if ($user === null){
            throw new NotFoundHttpException("L'utilisateur " . $id . " n'existe pas.");
        } else if($user->getRoles() == array("ROLE_ADMIN")){
            throw new AccessDeniedException("Opération impossible.");
        } else {
            $em->remove($user);
            $em->flush();
        }

        // Creates a flash message
        $request->getSession()->getFlashBag()->add('notif', "Utilisateur supprimé.");

        // Redirects to the user list
        return $this->redirectToRoute('apa_platform_adminListe');

    }

    // Edit the user with the id given in the url
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
    public function editUserAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('APASecurityBundle:User');

        $user = $repository->find($id);

        // Checks if the user we want to edit exists and is not admin
        if ($user === null){
            throw new NotFoundHttpException("Cet utilisateur n'existe pas.");
        } else if ($user->getRoles() == array("ROLE_ADMIN")){
            throw new AccessDeniedException("Opération impossible.");
        }

        // Creates a form with the existing user as argument
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

        // If the form is submitted
        if ($request->isMethod('POST')){

            $form->handleRequest($request);

            // If the form is valid
            if ($form->isValid()){

                // Encodes the plain password
                $password = $this->get('security.password_encoder')
                        ->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);

                // Updates the user
                $em->flush();

                // Creates a flash message
                $request->getSession()->getFlashBag()->add('notif', "Informations modifiées.");

                // Redirects to the user list
                return $this->redirectToRoute('apa_platform_adminListe');

            }

        }

        // If the method is not POST, displays the form
        return $this->render('APAPlatformBundle:Admin:editUser.html.twig', array(
            "form"=>$form->createView(),
            "id"=>$id));

    }

    // This action is called in the twig. It displays the content of the sidebar.
    public function MenuAction()
    {
        return $this->render('APAPlatformBundle:Admin:menu.html.twig');
    }

    // Same as MenuAction. Displays the index's body.
    public function tableauDeBordAction()
    {
        return $this->render('APAPlatformBundle:Admin:tableauDeBord.html.twig');
    }

}
