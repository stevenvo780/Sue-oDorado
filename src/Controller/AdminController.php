<?php

namespace App\Controller;

use App\Entity\Moneda;
use App\Entity\MonedaApoyo;
use App\Entity\MonedaMoneda;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{

    public function index()
    {
        return $this->render('admin/index.html.twig',);
    }

    public function monedasUsuario(int $id, EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($id);
        if(!$user)
        {
            throw $this->createNotFoundException('Usuario no encontrado'); 
        }
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

        if(!$moneda)
        {
            throw $this->createNotFoundException('Moneda No encontrada'); 
        }

        $diamanteApoyo = $em->getRepository(MonedaApoyo::class)->findOneByMoneda($moneda);
        $cambioMoneda;
        if (!$diamanteApoyo) {
            $diamanteApoyo = $em->getRepository(Moneda::class)->findByRango(4);
            $cambioMoneda = "";
        }
        else {
            $diamanteApoyo = $diamanteApoyo->getMoneda();
            $cambioMoneda = $em->getRepository(Moneda::class)->findByRango(4);
        }

        return $this->render('admin/posicionUsuario.html.twig', [
            'moneda' => $moneda,
            'monedaDeApoyo' => $diamanteApoyo,
            'cambioMoneda' => $cambioMoneda,
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
