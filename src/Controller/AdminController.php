<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\MonedaMoneda;
use App\Entity\Moneda;

class AdminController extends AbstractController
{

    public function index()
    {
        return $this->render('admin/index.html.twig', [

        ]);
    }

    public function monedasUsuario(int $id, EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($id);
        $monedas = $em->getRepository(Moneda::class)->findByDueño($id);
        return $this->render('admin/monedasUsuario.html.twig', [
            'user' => $user,
            'monedas' => $monedas,
            'invitados' => null,
        ]);
    }

    public function monedasPosicion(int $id, EntityManagerInterface $em)
    {
        $moneda = $em->getRepository(Moneda::class)->find($id);
        return $this->render('admin/posicionUsuario.html.twig', [
            'moneda' => $moneda
        ]);
    }

    public function posiciones(EntityManagerInterface $em)
    {
        $moneda = $em->getRepository(Moneda::class)->findAll();
        $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findAll();
        return $this->render('admin/posiciones.html.twig', [
            'monedas' => $moneda,
            'monedaMonedas' => $monedaMonedas,
        ]);
    }
}
