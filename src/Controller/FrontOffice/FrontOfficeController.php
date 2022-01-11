<?php

namespace App\Controller\FrontOffice;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

class FrontOfficeController extends AbstractController
{
    /**
     * @Route("/{page}", name="index", requirements={"page"="\d+"})
     */
    public function index($page = 1, AnnonceRepository $repo)
    {
        $nbAnnonce = $repo->createQueryBuilder('a')
                          ->select('count(a.id)')
                          ->getQuery()
                          ->getSingleScalarResult();

        $nbResult = 10;

        $annonce = $repo->findNAnnonce($nbResult, ($page-1)*$nbResult);

        return $this->render('front_office/index.html.twig', [
            'annonces' => $annonce,
            'page' => $page,
            'nbAnnonce' => $nbAnnonce,
            'nbResult' => $nbResult
        ]);
    }

    /**
     * @Route("/annonce/new", name="new")
     * @Route("/annonce/{id}/edit", name="edit")
     */
    public function form(Annonce $annonce = null, Request $request, EntityManagerInterface $manager)
    {
        // Si l'annonce n'existe pas, on en créer une
        if(!$annonce)
        {
            $annonce = new Annonce();
        }

        // Création du formulaire depuis le form AnnonceType
        $form = $this->createForm(AnnonceType::class, $annonce);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!$annonce->getId())
            {
                $annonce->setCreatedAt(new \DateTime());
                $annonce->setUser($this->getUser());
            }

            $manager->persist($annonce);
            $manager->flush();

            return $this->redirectToRoute('show', ['id' => $annonce->getId()]);
        }

        return $this->render('front_office/new.html.twig', [
            'formAnnonce' => $form->createView(),
            'editMode' => $annonce->getId() !== null
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search()
    {
        return $this->render('front_office/search.html.twig');
    }

    /**
     * @Route("/annonce/{id}", name="show")
     */
    public function show(Annonce $annonce)
    {
        return $this->render('front_office/show.html.twig', [
            'annonce' => $annonce
        ]);
    }
}
