<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Form\QuoteFormType;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class QuoteController extends AbstractController
{
    private QuoteRepository $quoteRepository;
    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        QuoteRepository $quoteRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @Route("/quote/list", name="app_quote")
     */
    public function index(): Response
    {
        $quotes = $this->quoteRepository->findAll();

        return $this->render('quote/index.html.twig', [
            'quotes' => $quotes,
            'username' => $this->getUser()->getUsername(),
        ]);
    }

    /**
     * @Route("/quote/{id}/delete", name="app_quote")
     */
    public function delete(Request $request): Response
    {
        $params = $request->attributes->get('_route_params');
        $id = $params['id'];

        $quote = $this->quoteRepository->findOneBy(['id' => $id]);

        if (!$quote) {
            $this->addFlash('Error', 'Цитата с айди: ' . $id . ' не была найдена');

            return new RedirectResponse($this->urlGenerator->generate('app_quote'));
        }

        $name = $quote->getTitle();

        $this->entityManager->remove($quote);
        $this->entityManager->flush();

        $this->addFlash('success', 'Цитата: "' . $name . '" была успешно удалена! :)');

        return new RedirectResponse($this->urlGenerator->generate('app_note'));
    }

    /**
     * @Route("/quote/add", name="app_quote")
     */
    public function create(Request $request): Response
    {
        $quote = new Quote();
        $form = $this->createForm(QuoteFormType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quote->setTitle($form->get('title')->getData());
            $quote->setAuthor($form->get('author')->getData());

            $this->entityManager->persist($quote);
            $this->entityManager->flush();

            $this->addFlash('success', 'Цитата была успешно создана! :)');

            return new RedirectResponse($this->urlGenerator->generate('app_quote'));
        }

        return $this->render('quote/add.html.twig', [
            'quoteForm' => $form->createView()
        ]);
    }
}
