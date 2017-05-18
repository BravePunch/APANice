<?php

namespace APA\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
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



class UserController extends Controller
{
  /**
   * @Security("has_role('ROLE_USER')")
   */
    public function userIndexAction(Request $request){

        return $this->render('APAPlatformBundle:User:index.html.twig');

    }

    // This action is called by the twig. It displays the content of the sidebar.
    public function MenuAction()
    {
        return $this->render('APAPlatformBundle:User:menu.html.twig');
    }

    public function userFichiersAction()
    {
        return $this->render('APAPlatformBundle:User:userFichiers.html.twig');
    }

    public function staffAction()
    {
        return $this->render('::chantier.html.twig');
    }

    public function userFicheAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $file = new File();

        $form = $this->get('form.factory')->createBuilder(Formtype::class, $file)
                ->add('file',     FileType::class, array("label" => "Choisissez le fichier à partager"))
                ->add('public',   CheckboxType::class, array("required"=>false, "label"=>"Fichier public"))
                ->add('category', ChoiceType::class, array("choices"=>array(
                    "Sans catégorie"=>null,
                    "Info Santé"=>"sante",
                    "Info Sport"=>"sport",
                    "Info Autre"=>"autre"
                )))
                ->add('save',     SubmitType::class)
                ->getForm();

        if ($request->isMethod("POST")){

            $form->handleRequest($request);

            if ($form->isValid()){

                $file->setName("testfile.pdf");

                $currentUser = $this->get('security.token_storage')->getToken()->getUser();
                $file->setUploader($currentUser->getUsername());

                $fileName = md5(uniqid()).'.'.$file->getFile()->guessExtension();

                $file->getFile()->move(
                        $this->getParameter('files_directory'),
                        $fileName
                        );

                $file->setFile($fileName);

                $em->persist($file);
                $em->flush();

                return $this->redirectToRoute('apa_platform_userIndex');

            }

        }

        return $this->render('APAPlatformBundle:User:userFiche.html.twig', array(
            'form' => $form->createView()
        ));

    }

    public function settingsAction()
    {
        return $this->render('::chantier.html.twig');
    }
}

