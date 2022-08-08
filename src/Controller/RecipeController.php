<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RecipeController extends AbstractController
{
    private RecipeRepository $recipeRepository;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator, RecipeRepository $recipeRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->recipeRepository = $recipeRepository;
    }

    /**
     * @Route("/recipe/list", name="app_recipe")
     */
    public function index(): Response
    {
        $data = $this->recipeRepository->findAll();

        return $this->render('recipe/index.html.twig', [
            'recipes' => $data,
        ]);
    }

    /**
     * @Route("/recipe/create", name="app_recipe_create")
     */
    public function create(): Response
    {

        return $this->render('recipe/add.html.twig', [

        ]);
    }

    /**
     * @Route("/recipe/{id}", name="app_get_recipe")
     */
    public function getRecipe(int $id): Response
    {

        return $this->render('recipe/recipe.html.twig', [

        ]);
    }

    /**
     * @Route("/recipe/{id}/delete", name="app_delete_recipe")
     */
    public function delete(int $id): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_recipe'));
    }
}
