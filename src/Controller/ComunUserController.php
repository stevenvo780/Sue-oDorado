<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\UserType;
use App\Entity\User;
use App\Controller\MonedaController;
use App\Entity\Moneda;
use App\Entity\MonedaMoneda;

class ComunUserController extends AbstractController
{
    public function index(EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $monedas = $em->getRepository(Moneda::class)->findByDueño($user);
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'monedas' => $monedas,
            'invitados' => null,
        ]);
    }

    public function indexMoneda(int $id, EntityManagerInterface $em)
    {
        $moneda = $em->getRepository(Moneda::class)->find($id);
        return $this->render('user/moneda/index.html.twig', [
            'moneda' => $moneda
        ]);
    }

}
