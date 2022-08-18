<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use App\Repository\QuoteRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(
        QuoteRepository $quoteRepository,
        NoteRepository $noteRepository,
        RecipeRepository $recipeRepository
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('homepage/homepage.html.twig', [
            'user' => $this->getUser()->getUsername(),
            'quotes' => $quoteRepository->findAll(),
            'notes' => $noteRepository->findAll(),
            'recipes' => $recipeRepository->findAll(),
        ]);
    }
}
