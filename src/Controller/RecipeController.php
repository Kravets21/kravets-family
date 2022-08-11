<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeFormType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RecipeController extends AbstractController
{
    private RecipeRepository $recipeRepository;
    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        RecipeRepository $recipeRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->recipeRepository = $recipeRepository;
    }

    /**
     * @Route("/recipe/list", name="app_recipe")
     */
    public function index(): Response
    {
        $data = $this->recipeRepository->findBy(['userId' => $this->getUser()->eraseCredentials()['id']]);

        return $this->render('recipe/index.html.twig', [
            'recipes' => $data,
        ]);
    }

    /**
     * @Route("/recipe/create", name="app_recipe_create")
     */
    public function create(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeFormType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setName($form->get('name')->getData());
            $recipe->setDescription($form->get('description')->getData());
            $recipe->setUser($this->getUser());
            $recipe->setCreatedAt(new \DateTimeImmutable());

            $this->recipeRepository->add($recipe);

            $this->addFlash('success', 'Рецепт был успешно создан! :)');

            return new RedirectResponse($this->urlGenerator->generate('app_recipe'));
        }

        return $this->render('recipe/add.html.twig', [
            'recipeForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/recipe/{id}", name="app_get_recipe")
     */
    public function update(Request $request): Response
    {
        $params = $request->attributes->get('_route_params');

        $recipe = $this->recipeRepository->findOneBy(['id' => $params['id']]);

        if (!$recipe) {
            $this->addFlash('Error', 'Рецепт не был найден');

            return new RedirectResponse($this->urlGenerator->generate('app_recipe'));
        }

        $form = $this->createForm(RecipeFormType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setName($form->get('name')->getData());
            $recipe->setDescription($form->get('description')->getData());

            $this->entityManager->persist($recipe);
            $this->entityManager->flush();

            $this->addFlash('success_update', 'Рецепт был успешно обновлен! :)');

            return new RedirectResponse($this->urlGenerator->generate('app_get_recipe', ['id' => $recipe->getId()]));
        }

        return $this->render('recipe/update.html.twig', [
            'recipeForm' => $form->createView(),
            'createdAt' => $recipe->getCreatedAt(),
        ]);
    }

    /**
     * @Route("/recipe/{id}/delete", name="app_delete_recipe")
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $params = $request->attributes->get('_route_params');
        $id = $params['id'];

        $recipe = $this->recipeRepository->findOneBy(['id' => $id]);

        if (!$recipe) {
            $this->addFlash('Error', 'Рецепт с айди: ' . $id . ' не был найден');

            return new RedirectResponse($this->urlGenerator->generate('app_recipe'));
        }

        $name = $recipe->getName();

        $this->recipeRepository->remove($recipe);

        $this->addFlash('success', 'Рецепт: "' . $name . '" был успешно удален! :)');

        return new RedirectResponse($this->urlGenerator->generate('app_recipe'));
    }
}
