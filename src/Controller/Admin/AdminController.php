<?php
namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Admin;
use App\Form\AdminType;
use App\Repository\AdminRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController{

    /**
     * @var Environment
     */
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function home(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $liste_utilisateur = $doctrine->getRepository(Admin::class)->findAll();

        $admin = $this->getUser()->getUsername();

        if ($this->getUser()->getRoles()[0]=="ROLE_ADMIN") {
            $utilisateur = new Admin();

            $form = $this->createForm(AdminType::class, $utilisateur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $nom_entitee = $doctrine->getManager();
                //encodage du mot de passe
                $utilisateur->setPassword(
                    // $passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword())
                    $passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword())
                );
                //définition du role
                $role = ['ROLE_USER'];
                $utilisateur->setRoles($role);
                $nom_entitee->persist($utilisateur);
                $nom_entitee->flush();
                $this->addFlash('success', 'L\'utilisateur a bien été ajouté');
                return $this->redirectToRoute('admin');
            }
        
            return new Response($this->twig->render('admin/home.html.twig', [
                'utilisateurs' => $liste_utilisateur,
                'admin' => $admin,
                'form' => $form->createView()
            ]));
        }


        return new Response($this->twig->render('admin/home.html.twig', [
            'utilisateurs' => $liste_utilisateur,
            'admin' => $admin
        ]));

    }

    public function utilisateur_delete(ManagerRegistry $doctrine, $id, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete'.$id , $request->get('_token')))
        {
            $utilisateur = $doctrine->getRepository(Admin::class)->find($id);
            $nom_entitee = $doctrine->getManager();
            $nom_entitee->remove($utilisateur);
            $nom_entitee->flush();
            $this->addFlash('success', 'L\'utilisateur a bien été supprimé');
            return $this->redirectToRoute('admin');
        }
        return $this->redirectToRoute('admin');
    }



}