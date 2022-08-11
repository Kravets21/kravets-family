<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteFormType;
use App\Repository\NoteRepository;
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
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->noteRepository = $noteRepository;
    }

    /**
     * @Route("/note/list", name="app_note")
     */
    public function index(): Response
    {
        $data = $this->noteRepository->findBy(['userId' => $this->getUser()->eraseCredentials()['id']]);

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
        $form = $this->createForm(NoteFormType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setTitle($form->get('title')->getData());
            $note->setDescription($form->get('description')->getData());
            $note->setUser($this->getUser());
            $note->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($note);
            $this->entityManager->flush();

            $this->addFlash('success', 'Заметка была успешно создана! :)');

            return new RedirectResponse($this->urlGenerator->generate('app_note'));
        }

        return $this->render('note/add.html.twig', [
            'noteForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/note/{id}", name="app_get_note")
     */
    public function update(Request $request): Response
    {
        $params = $request->attributes->get('_route_params');

        $note = $this->noteRepository->findOneBy(['id' => $params['id']]);

        if (!$note) {
            $this->addFlash('Error', 'Заметка не была найдена');

            return new RedirectResponse($this->urlGenerator->generate('app_note'));
        }

        $form = $this->createForm(NoteFormType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setTitle($form->get('title')->getData());
            $note->setDescription($form->get('description')->getData());

            $this->entityManager->persist($note);
            $this->entityManager->flush();

            $this->addFlash('success_update', 'Замтека была успешно обновлена! :)');

            return new RedirectResponse($this->urlGenerator->generate('app_get_note', ['id' => $note->getId()]));
        }

        return $this->render('note/update.html.twig', [
            'noteForm' => $form->createView(),
            'createdAt' => $note->getCreatedAt(),
        ]);
    }

    /**
     * @Route("/note/{id}/delete", name="app_delete_note")
     * @throws \Exception
     */
    public function delete(Request $request): Response
    {
        $params = $request->attributes->get('_route_params');
        $id = $params['id'];

        $note = $this->noteRepository->findOneBy(['id' => $id]);

        if (!$note) {
            $this->addFlash('Error', 'Заметка с айди: ' . $id . ' не была найдена');

            return new RedirectResponse($this->urlGenerator->generate('app_note'));
        }

        $name = $note->getTitle();

        $this->entityManager->remove($note);
        $this->entityManager->flush();

        $this->addFlash('success', 'Заметка: "' . $name . '" была успешно удалена! :)');

        return new RedirectResponse($this->urlGenerator->generate('app_note'));
    }
}
