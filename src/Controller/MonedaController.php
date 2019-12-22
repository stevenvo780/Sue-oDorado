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
use App\Entity\User;
use App\Entity\MonedaMoneda;

class MonedaController extends AbstractController
{
    public function listArbolDeMonedas(int $id, EntityManagerInterface $em)
    {
        $data = $this->findArbol($id);
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($data, 'json'));
    }

    public function findInvitados(int $id, EntityManagerInterface $em)
    {
        $invitados = $em->getRepository(MonedaMoneda::class)->findByMonedaPropietario($id);

        $data = [];
        foreach ($invitados as $key => $invitado) {
            array_push($data, $invitado->getMonedaInvitado());
        }
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($data, 'json'));
    }


    public function editInvitados(int $id, EntityManagerInterface $em, Request $request)
    {
        $moneda = $em->getRepository(Moneda::class)->find($id);
        $invitados = $em->getRepository(MonedaMoneda::class)->findByMonedaPropietario($id);
        $json = $request->request->all();
        for ($i=0; $i < count($invitados); $i++) {
                        $em->remove($invitados[$i]);
                        $em->flush();
        }
        if (array_key_exists('monedas', $json)) {
            foreach ($json['monedas'] as $key => $monedaId) {
                dump($moneda);
                $monedaInvitado = $em->getRepository(Moneda::class)->find($monedaId);
                $monedaMoneda = new MonedaMoneda();
                $monedaMoneda->setMonedaPropietario($moneda);
                $monedaMoneda->setMonedaInvitado($monedaInvitado);
                $em->persist($monedaMoneda);
                $em->flush();
            }
        }
        return new Response(0);
    }

    public function editMoneda(int $id, Request $request, EntityManagerInterface $em)
    {
        $moneda = $em->getRepository(Moneda::class)->find($id);
        $json = $request->request->all();
        $contador = 0;
        if (array_key_exists('dono', $json)) {
            $moneda->setDono(true);
            $arbol = $this->findArbol($moneda);
            $monedas = [];
            foreach ($arbol as $key => $moneda) {
                array_push($monedas, $moneda[0]['Padre']);
            }
            foreach ($monedas as $key => $moneda) {
                if ($moneda->getRango() == 0) {
                    if ($moneda->getDono() == true) {
                        $contador++;
                    }
                }
                if ($contador == 8) {
                    $this->validarRecibida($monedas);
                }
            }
        } else {
            $moneda->setDono(false);
        }
        $moneda->setRango($json['rango']);
        $em->persist($moneda);
        $em->flush();

        return new Response(0);
    }


    public function new(int $id, Request $request, EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($id);
        $moneda = new Moneda();
        $moneda->setDueño($user);
        $moneda->setVecesRecibidas(0);
        $moneda->setRango(0);
        $moneda->setDono(false);
        $em->persist($moneda);
        $em->flush();

        return $this->redirectToRoute('user_list_monedas', [
            'id' => $id
        ]);
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

        return $this->redirectToRoute('user_list_monedas', [
            'id' => $moneda->getDueño()->getId(),
        ]);
    }

    public function listAllMonedas(int $id, EntityManagerInterface $em, Request $request)
    {
        $moneda = $em->getRepository(Moneda::class)->find($id);

        if ($moneda->getRango() == 3) {
            $monedas = $em->getRepository(Moneda::class)->findByRango(2);
        } elseif ($moneda->getRango() == 2) {
            $monedas = $em->getRepository(Moneda::class)->findByRango(1);
        } elseif ($moneda->getRango() == 1) {
            $monedas = $em->getRepository(Moneda::class)->findByRango(0);
        }
        $data = [];
        foreach ($monedas as $key => $moneda) {
            $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaInvitado($moneda);
            if ($monedaMonedas == null) {
                array_push($data, $moneda);
            }
        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($data, 'json'));
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
        $monedaNuevaInvitada->setDono(false);
        $monedaMoneda = new MonedaMoneda();

        $monedaMoneda->setMonedaPropietario($monedaRuby);
        $monedaMoneda->setMonedaInvitado($monedaNuevaInvitada);
        $em->persist($monedaMoneda);
        $em->persist($monedaNuevaInvitada);
        $em->flush();

        return $this->redirectToRoute('dasboard_user');
    }

    public function posiciones(int $id, EntityManagerInterface $em)
    {
        $data = $this->findArbol($id);

        $monedas = [];
        $relaciones = [];
        foreach ($data as $key => $monedaa) {
            array_push($monedas, $monedaa[0]['Padre']);
            $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaPropietario($monedaa[0]['Padre']);
            foreach ($monedaMonedas as $key => $monedaMoneda) {
                array_push($relaciones, $monedaMoneda);
            }
        }
        $rol = $this->getUser()->getRoles();
        $moneda = $em->getRepository(Moneda::class)->find($id);
        if ($rol[0] == "ROLE_ADMIN") {
            return $this->render('admin/posicionesUser.html.twig', [
                'moneda' => $moneda,
                'monedas' => $monedas,
                'monedaMonedas' => $relaciones,
            ]);
        } elseif ($rol[0] == "ROLE_USER") {
            return $this->render('user/posiciones.html.twig', [
                'moneda' => $moneda,
                'monedas' => $monedas,
                'monedaMonedas' => $relaciones,
            ]);
        }
    }

    public function validarRecibida($arbol)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($arbol as $key => $moneda) {
            if ($moneda->getRango() == 3) {
                $user = $em->getRepository(User::class)->find($moneda->getDueño()->getId());
                $monedaNew = new Moneda();
                $monedaNew->setDueño($user);
                $monedaNew->setVecesRecibidas(0);
                $monedaNew->setRango(0);
                $monedaNew->setDono(false);
                $em->persist($monedaNew);
                $em->flush();
                $monedaSave = $em->getRepository(Moneda::class)->find($moneda->getId());
                $monedaSave->setVecesRecibidas(1);
                $monedaSave->setRango(4);
            }
            if ($moneda->getRango() == 2) {
                $monedaSave = $em->getRepository(Moneda::class)->find($moneda->getId());
                $monedaSave->setRango(3);
            }
            if ($moneda->getRango() == 1) {
                $monedaSave = $em->getRepository(Moneda::class)->find($moneda->getId());
                $monedaSave->setRango(2);
            }
            if ($moneda->getRango() == 0) {
                $monedaSave = $em->getRepository(Moneda::class)->find($moneda->getId());
                $monedaSave->setRango(1);
            }
            dump($monedaSave);
            $em->persist($monedaSave);
            $em->flush();
        }
    }

    private function findArbol($id)
    {
        $em = $this->getDoctrine()->getManager();
        $moneda = $em->getRepository(Moneda::class)->find($id);
        $invitado = $em->getRepository(MonedaMoneda::class)->findByMonedaInvitado($id);

        $data = [];
        if ($invitado | $moneda->getRango() == 3) {
            switch ($moneda->getRango()) {
                case 0:
                    $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaInvitado($moneda);
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
        }

        return $data;
    }

    public function arbolDeMoneda($diamanteMoneda)
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
