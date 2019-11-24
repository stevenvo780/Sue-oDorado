<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProyectoController extends AbstractController
{
    public function index()
    {
        return $this->render('proyecto/index.html.twig', [
            'controller_name' => 'ProyectoController',
        ]);
    }
}
