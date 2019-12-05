<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Moneda;
use App\Entity\MonedaMoneda;

class MonedaController extends AbstractController
{

    public function index(int $id, EntityManagerInterface $em)
    {
        $moneda = $em->getRepository(Moneda::class)->find($id);
        return $this->render('user/moneda/index.html.twig', [
            'moneda' => $moneda
        ]);
    }

    public function listArbolDeMonedas(int $id, EntityManagerInterface $em)
    {
        $moneda = $em->getRepository(Moneda::class)->find($id);
        $monedasAllArbol = [];
        switch ($moneda->getRango()) {
            case 0:
                $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaInvitado($moneda);
                dump($monedaMonedas);
                dump($moneda);
                if ($monedaMonedas[0]->getMonedaPropietario()->getRango() == 1) {
                    $rubi = $monedaMonedas[0]->getMonedaPropietario();
                }
                $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaInvitado($rubi);
                if ($monedaMonedas[0]->getMonedaPropietario()->getRango() == 2) {
                    $esmeralda = $monedaMonedas[0]->getMonedaPropietario();
                }
                $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
                findByMonedaInvitado($esmeralda);
                if ($monedaMonedas[0]->getMonedaPropietario()->getRango() == 3) {
                    $diamante = $monedaMonedas[0]->getMonedaPropietario();
                }

                $data = $this->arbolDeMoneda($diamante);
                break;
            case 1:
                $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaInvitado($moneda);
                if ($monedaMonedas[0]->getMonedaPropietario()->getRango() == 2) {
                    $esmeralda = $monedaMonedas[0]->getMonedaPropietario();
                }
                $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaInvitado($esmeralda);
                if ($monedaMonedas[0]->getMonedaPropietario()->getRango() == 3) {
                    $diamante = $monedaMonedas[0]->getMonedaPropietario();
                }

                $data = $this->arbolDeMoneda($diamante);
                break;
            case 2:
                $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaInvitado($moneda);
                if ($monedaMonedas[0]->getMonedaPropietario()->getRango() == 3) {
                    $diamante = $monedaMonedas[0]->getMonedaPropietario();
                }

                $data = $this->arbolDeMoneda($diamante);
                break;
            case 3:
                $data = $this->arbolDeMoneda($moneda);
                break;
        }
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($data, 'json'));
    }

    public function new(Request $request, EntityManagerInterface $em)
    {
        $moneda = new Moneda();
        $moneda->setDueño($this->getUser());
        $moneda->setVecesRecibidas(0);
        $moneda->setRango(0);
        $em->persist($moneda);
        $em->flush();

        return $this->redirectToRoute('dasboard_user');
    }

    public function delete($id, EntityManagerInterface $em, Request $request)
    {
        $moneda = $em->getRepository(Moneda::class)->find($id);
        $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaPropietario($moneda);
        for ($i=0; $i < count($monedaMonedas); $i++) {
            if ($monedaMonedas) {
                $em->remove($monedaMonedas[i]);
                $em->flush();
            }
        }
        $em->remove($moneda);
        $em->flush();
        return $this->redirectToRoute('dasboard_user');
    }

    public function listAllMonedas(EntityManagerInterface $em, Request $request)
    {
        $monedas = $em->getRepository(Moneda::class)->findAll();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($monedas, 'json'));
    }

    public function invitar(int $idRuby, EntityManagerInterface $em, Request $request)
    {
        $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaPropietario($idRuby);
        if (count($monedaMonedas) >= 2) {
            $invitados = [];
            foreach ($monedaMonedas as $key => $monedaMoneda) {
                array_push($invitados, $monedaMoneda->getMonedaInvitado()->getDueño());
            }
            $user = $this->getUser();
            $monedas = $em->getRepository(Moneda::class)->findByDueño($user);
            return $this->render('user/index.html.twig', [
                'user' => $user,
                'monedas' => $monedas,
                'invitados' => $invitados,
            ]);
        }
        $monedaRuby = $em->getRepository(Moneda::class)->find($idRuby);
        $monedaNuevaInvitada = new Moneda();
        $monedaNuevaInvitada->setDueño($this->getUser());
        $monedaNuevaInvitada->setVecesRecibidas(0);
        $monedaNuevaInvitada->setRango(0);
        $monedaMoneda = new MonedaMoneda();

        $monedaMoneda->setMonedaPropietario($monedaRuby);
        $monedaMoneda->setMonedaInvitado($monedaNuevaInvitada);
        $em->persist($monedaMoneda);
        $em->persist($monedaNuevaInvitada);
        $em->flush();
        dump(count($monedaMonedas));



        return $this->redirectToRoute('dasboard_user');
    }

    private function arbolDeMoneda($diamanteMoneda)
    {
        $em = $this->getDoctrine()->getManager();
        $monedaDiamante[0] = [];
        array_push($monedaDiamante[0], ["Padre" => $diamanteMoneda]);
        $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
        findByMonedaPropietario($diamanteMoneda);
        foreach ($monedaMonedas as $key => $hijo) {
            array_push($monedaDiamante[0], $hijo->getMonedaInvitado());
        }
        $monedaEsmeralda = [];
        $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
        findByMonedaPropietario($diamanteMoneda);
        // buscando esmeraldas y sus hijos
        foreach ($monedaMonedas as $keym => $monedaMoneda) {
            $monedaEsmeralda[$keym] = [];
            array_push($monedaEsmeralda[$keym], ["Padre" => $monedaMoneda->getMonedaInvitado()]);
            foreach ($monedaEsmeralda[$keym] as $key => $monedaPadre) {
                $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
                findByMonedaPropietario($monedaPadre);
                foreach ($monedaMonedas as $key => $hijo) {
                    array_push($monedaEsmeralda[$keym], $hijo->getMonedaInvitado());
                }
            }
        }
        $monedasRubies = [];
        $cont = 0;
        //buscando los rubies
        foreach ($monedaEsmeralda as & $esmeralda) {
            $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
            findByMonedaPropietario($esmeralda[0]['Padre']);
            foreach ($monedaMonedas as $keym => $monedaMoneda) {
                $monedasRubies[$cont] = [];
                array_push($monedasRubies[$cont], ["Padre" => $monedaMoneda->getMonedaInvitado()]);
                $cont ++;
            }
        }
        //buscando hijos de los rubies
        foreach ($monedasRubies as $keyp => $monedaPadre) {
            $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
            findByMonedaPropietario($monedaPadre[0]['Padre']);
            foreach ($monedaMonedas as $key => $hijo) {
                array_push($monedasRubies[$keyp], $hijo->getMonedaInvitado());
            }
        }
        $monedasOros = [];
        $cont = 0;
        //buscando los oros
        foreach ($monedasRubies as & $rubi) {
            $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
            findByMonedaPropietario($rubi[0]['Padre']);
            foreach ($monedaMonedas as $keym => $monedaMoneda) {
                $monedasOros[$cont] = [];
                array_push($monedasOros[$cont], ["Padre" => $monedaMoneda->getMonedaInvitado()]);
                $cont ++;
            }
        }
        $DE = array_merge($monedaDiamante, $monedaEsmeralda);
        $RO = array_merge($monedasRubies, $monedasOros);
        $data = array_merge($DE, $RO);

        return $data;
    }
}
