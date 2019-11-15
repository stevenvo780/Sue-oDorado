<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ComunUserController extends AbstractController
{

    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
