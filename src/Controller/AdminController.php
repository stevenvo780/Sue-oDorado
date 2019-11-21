<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\UserUser;

class AdminController extends AbstractController
{

    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    public function posiciones(EntityManagerInterface $em)
    {
        $usuarios = $em->getRepository(User::class)->findAll();
        $userUsers = $em->getRepository(UserUser::class)->findAll();
        return $this->render('admin/posiciones.html.twig', [
            'usuarios' => $usuarios,
            'userUsers' => $userUsers,
        ]);
    }
}
