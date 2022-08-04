<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecieptController extends AbstractController
{
    /**
     * @Route("/reciept/list", name="app_reciept")
     */
    public function index(): Response
    {
        return $this->render('reciept/index.html.twig', [
            'controller_name' => 'RecieptController',
        ]);
    }
}
