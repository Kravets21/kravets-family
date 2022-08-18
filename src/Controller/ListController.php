<?php

namespace App\Controller;

use App\Entity\ListItem;
use App\Enum\ListItemTypeEnum;
use App\Form\ListItemFormType;
use App\Repository\ListItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ListController extends AbstractController
{
    private ListItemRepository $listRepository;
    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ListItemRepository $listRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->listRepository = $listRepository;
    }

    /**
     * @Route("/anime/list", name="app_anime_list")
     */
    public function indexAnime(): Response
    {
        $animeList = $this->listRepository->findBy(['userId' => $this->getUser(),'type' => ListItemTypeEnum::ANIME]);

        return $this->render('list/index.html.twig', [
            'pageName' => 'Список Аниме',
            'animeList' => $animeList
        ]);
    }

    /**
     * @Route("/movie/list", name="app_movie_list")
     */
    public function indexMovies(): Response
    {
        $moviesList = $this->listRepository->findBy(['type' => ListItemTypeEnum::MOVIE]);
        $seriesList = $this->listRepository->findBy(['type' => ListItemTypeEnum::SERIES]);

        return $this->render('list/movies.html.twig', [
            'pageName' => 'Список Фильмов',
            'moviesList' => $moviesList,
            'seriesList' => $seriesList,
        ]);
    }

    /**
     * @Route("/book/list", name="app_book_list")
     */
    public function indexBooks(): Response
    {

        return $this->render('list/movies.html.twig', [
        ]);
    }

    /**
     * @Route("/add/anime", name="app_add_anime")
     */
    public function addAnime(Request $request): Response
    {
        $note = new ListItem();
        $form = $this->createForm(ListItemFormType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setTitle($form->get('title')->getData());
            $note->setStatus($form->get('status')->getData());
            $note->setUser($this->getUser());
            $note->setType(ListItemTypeEnum::ANIME);

            $this->entityManager->persist($note);
            $this->entityManager->flush();

            $this->addFlash('success', 'Аниме было успешно добавлено в список! :)');

            return new RedirectResponse($this->urlGenerator->generate('app_anime_list'));
        }

        return $this->render('list/add.html.twig', [
            'listItemForm' => $form->createView()
        ]);
    }
}
