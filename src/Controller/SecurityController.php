<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Category;
use App\Form\RegistrationType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscritpion", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'formRegistration' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(Request $request) {
        return $this->render('security/login.html.twig', [
            'target' => $request->headers->get('referer')
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout() {

    }

    /**
     * @Route("/profile", name="security_profile")
     */
    public function profile(AnnonceRepository $repo) {

        $nbAnnonce = $repo->createQueryBuilder('a')
                          ->where('a.user = :user')
                          ->setParameter('user', $this->getUser())
                          ->select('count(a.id)')
                          ->getQuery()
                          ->getSingleScalarResult();
        
        $annonce = $repo->findBy(["user" => $this->getUser()]);

        return $this->render('security/profile.html.twig', [
            'nbAnnonce' => $nbAnnonce,
            'annonces' => $annonce
        ]);
    }

    /**
     * @Route("/annonce/delete/{id}", name="security_delete")
     */
    public function delete(Request $request, Annonce $annonce): Response
    {
        if ($this->getUser() == $annonce->getUser()) {
            if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($annonce);
                $entityManager->flush();
            }
            return $this->redirectToRoute('security_profile');
        } else {
            return $this->redirectToRoute('security_login');
        }
    }
}
