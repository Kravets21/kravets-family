<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Recipe;
use App\Form\RecipeFormType;
use App\Repository\NoteRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NoteController extends AbstractController
{
    private NoteRepository $noteRepository;
    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        NoteRepository $noteRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->noteRepository = $noteRepository;
    }

    /**
     * @Route("/note/list", name="app_note")
     */
    public function index(): Response
    {
        $data = $this->noteRepository->findAll();

        return $this->render('note/index.html.twig', [
            'notes' => $data,
        ]);
    }

    /**
     * @Route("/note/create", name="app_note_create")
     */
    public function create(Request $request): Response
    {
        $note = new Note();
        $form = $this->createForm(RecipeFormType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setName($form->get('name')->getData());
            $note->setDescription($form->get('description')->getData());
            $note->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($note);
            $this->entityManager->flush();

            $this->addFlash('success', 'Рецепт был успешно создан! :)');

            return new RedirectResponse($this->urlGenerator->generate('app_note'));
        }

        return $this->render('note/add.html.twig', [
            'noteForm' => $form->createView()
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
            'recipeForm' => $form->createView()
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

        $this->entityManager->remove($recipe);
        $this->entityManager->flush();

        $this->addFlash('success', 'Рецепт: "' . $name . '" был успешно удален! :)');

        return new RedirectResponse($this->urlGenerator->generate('app_recipe'));
    }
}
