<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingFormType;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @Route("/calendar", name="app_booking_calendar", methods={"GET"})
     */
    public function calendar(): Response
    {
        return $this->render('calendar/index.html.twig');
    }

    /**
     * @Route("/calendar/add", name="app_booking_new")
     */
    public function add(Request $request, BookingRepository $bookingRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $booking = new Booking();
        $form = $this->createForm(BookingFormType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setTitle($form->get('title')->getData());
            $booking->setBeginAt($form->get('beginAt')->getData());
            $booking->setEndAt($form->get('endAt')->getData());

            $bookingRepository->add($booking);

            $this->addFlash('success', 'Событие было успешно создано! :)');

            return new RedirectResponse('/calendar');
        }

        return $this->render('calendar/add.html.twig', [
            'bookingForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/calendar/event", name="app_booking_show")
     */
    public function show(): Response
    {
        return $this->render('calendar/index.html.twig');
    }

}