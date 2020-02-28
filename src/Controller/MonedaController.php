<?php

namespace App\Controller;

use App\Entity\Moneda;
use App\Entity\MonedaApoyo;
use App\Entity\MonedaMoneda;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MonedaController extends AbstractController
{
    /**
     * Devuelve la extructura completa de una moneda
     *
     * @param int $id
     * @return Response $data
     */
    public function listArbolDeMonedas(int $id, EntityManagerInterface $em)
    {
        $data = $this->findArbol($id);
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($data, 'json'));
    }

    /**
     * Devuelve los invitados de una moneda
     *
     * @param int $id
     * @return Response $data
     */
    public function findInvitados(int $id, EntityManagerInterface $em)
    {
        $invitados = $em->getRepository(MonedaMoneda::class)->
            findByMonedaPropietario($id);

        $data = [];
        foreach ($invitados as $key => $invitado) {
            array_push($data, $invitado->getMonedaInvitado());
        }
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($data, 'json'));
    }

    /**
     * Busca todos los oros de un diamante de apoyo
     *
     * @param int $id
     * @return Response $oros
     */
    public function findOrosDApoyo(int $id, EntityManagerInterface $em)
    {
        $findOros = $em->getRepository(MonedaApoyo::class)->
            findByMonedaDApoyo($id);

        $oros = [];
        foreach ($findOros as $key => $oro) {
            array_push($oros, $oro->getMoneda());
        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        return new Response($serializer->serialize($oros, 'json'));
    }

    /**
     * Edita la asociacion entre una moneda y otra
     *
     * @param int $id
     */
    public function editInvitados(
        int $id,
        EntityManagerInterface $em,
        Request $request) {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $moneda = $em->getRepository(Moneda::class)->find($id);
        if (!$moneda) {
            throw $this->createNotFoundException('Moneda no encontrada');
        }
        $invitados = $em->getRepository(MonedaMoneda::class)->
            findByMonedaPropietario($id);

        $json = $request->request->all();

        for ($i = 0; $i < count($invitados); $i++) {
            if (array_key_exists('monedas', $json)) {
                $indice = in_array($invitados[$i]->getMonedaInvitado()->getId(), $json['monedas']);
            } else {
                $indice = false;
            }

            if ($indice == false) {
                $invitadosinvitado = $em->getRepository(MonedaMoneda::class)->
                    findByMonedaPropietario($invitados[$i]->
                        getMonedaInvitado()->getId());

                for ($o = 0; $o < count($invitadosinvitado); $o++) {
                    $em->remove($invitadosinvitado[$o]);
                    $em->flush();
                }
            }

            $em->remove($invitados[$i]);
            $em->flush();
        }
        if (array_key_exists('monedas', $json)) {
            foreach ($json['monedas'] as $key => $monedaId) {

                $monedaInvitado = $em->getRepository(Moneda::class)->find($monedaId);
                $monedaMoneda = new MonedaMoneda();
                $monedaMoneda->setMonedaPropietario($moneda);
                $monedaMoneda->setMonedaInvitado($monedaInvitado);

                $em->persist($monedaMoneda);
                $em->flush();

                if ($monedaInvitado->getRango() == 0) {
                    $arbol = $this->findArbol($monedaId);
                    $diamante;
                    foreach ($arbol as $key => $monedaD) {
                        if ($monedaD[0]['Padre']->getRango() == 3) {
                            $diamante = $monedaD[0]['Padre'];
                        }
                    }
                    $monedaDApoyo = $em->getRepository(MonedaApoyo::class)->
                        findOneByMoneda($monedaId);
                    if (!$monedaDApoyo) {
                        $monedaDApoyo = new MonedaApoyo;
                    }

                    $monedaDApoyo->setMoneda($monedaInvitado);
                    $monedaDApoyo->setMonedaDApoyo($diamante);
                    $em->persist($monedaDApoyo);
                    $em->flush();
                }
            }
        }
        return new Response(0);
    }

    /**
     * Edita los datos de una moneda y cuando completa 8 divide la extructura
     *
     * @param int $id
     */
    public function editMoneda(int $id, Request $request, EntityManagerInterface $em)
    {
        $monedaSave = $em->getRepository(Moneda::class)->find($id);
        $json = $request->request->all();
        $contador = 0;
        if (array_key_exists('dono', $json)) {
            $monedaSave->setDono(true);
            $arbol = $this->findArbol($monedaSave);
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
                    return new Response(0);
                }
            }
        } else {
            $monedaSave->setDono(false);
        }
        $monedaSave->setRango($json['rango']);
        $monedaSave->setVecesRecibidas($json['vecesRecividas']);
        $em->persist($monedaSave);
        $em->flush();

        return new Response(0);
    }

    function new (int $id, Request $request, EntityManagerInterface $em) {
        $user = $em->getRepository(User::class)->find($id);
        $moneda = new Moneda();
        $moneda->setDueño($user);
        $moneda->setVecesRecibidas(0);
        $moneda->setRango(0);
        $moneda->setDono(false);
        $em->persist($moneda);
        $em->flush();

        return $this->redirectToRoute('user_list_monedas', [
            'id' => $id,
        ]);
    }

    public function delete($id, EntityManagerInterface $em, Request $request)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $moneda = $em->getRepository(Moneda::class)->find($id);

        $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
            findByMonedaPropietario($moneda);

        $monedaMonedasRelExtructura = $em->getRepository(MonedaMoneda::class)->
            findByMonedaInvitado($moneda);

        if ($monedaMonedasRelExtructura) {
            $error = 'ERROR ESTA MONEDA ESTA RELACIONADA EN UNA EXTRUCTURA';
            return new Response($serializer->serialize($error, 'json'));
        }

        for ($i = 0; $i < count($monedaMonedas); $i++) {
            if ($monedaMonedas) {
                $em->remove($monedaMonedas[i]);
                $em->flush();
            }
        }
        $monedaApoyo = $em->getRepository(MonedaApoyo::class)->
            findByMoneda($moneda);

        if ($monedaApoyo) {
            $em->remove($monedaApoyo[0]);
        }
        $em->remove($moneda);

        $em->flush();

        return new Response(0);
    }

    /**
     * Devuelve todas las monedas
     *
     * @param int $id
     * @return Response $data
     */
    public function listAllMonedas(
        int $id,
        EntityManagerInterface $em) {
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
            $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
                findByMonedaInvitado($moneda);
            if ($monedaMonedas == null) {
                array_push($data, $moneda);
            }
        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($data, 'json'));
    }

    /**
     * Crea un oro que se relaciona con el ruby recibido
     *
     * @param int $idRuby
     */
    public function invitar(
        int $idRuby,
        EntityManagerInterface $em) {
        $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
            findByMonedaPropietario($idRuby);
        if (!$monedaMonedas) {
            throw $this->createNotFoundException('Moneda no encontrada');
        }
        if (count($monedaMonedas) >= 2) {
            $invitados = [];
            foreach ($monedaMonedas as $key => $monedaMoneda) {
                array_push($invitados, $monedaMoneda->
                        getMonedaInvitado()->getDueño());
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

        $arbol = $this->findArbol($monedaNuevaInvitada->GetId());
        $diamante;
        foreach ($arbol as $key => $moneda) {
            if ($moneda[0]['Padre']->getRango() == 3) {
                $diamante = $moneda[0]['Padre'];
            }
        }
        $monedaDApoyo = new MonedaApoyo;
        $monedaDApoyo->setMoneda($monedaNuevaInvitada);
        $monedaDApoyo->setMonedaDApoyo($diamante);
        $em->persist($monedaDApoyo);
        $em->flush();

        return $this->redirectToRoute('dasboard_user');
    }

    /**
     * Devueve la vista de posiciones junto con las monedas
     *
     * @param int $id
     * @return Response render
     */
    public function posiciones(int $id, EntityManagerInterface $em)
    {
        $data = $this->findArbol($id);
        if (!$data) {
            throw $this->createNotFoundException('Extructura no encontrada');
        }
        $monedas = [];
        $relaciones = [];
        foreach ($data as $key => $monedaa) {
            array_push($monedas, $monedaa[0]['Padre']);
            $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
                findByMonedaPropietario($monedaa[0]['Padre']);
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

    /**
     * Divide la extrucutura recivida subiendo el rango de todas las monedas
     * y crea una nueva moneda oro asociada al usuario
     *
     * @param array $arbol
     */
    public function validarRecibida($arbol)
    {
        $em = $this->getDoctrine()->getManager();
        $diamante;
        foreach ($arbol as $key => $moneda) {
            if ($moneda->getRango() == 3) {
                $diamante = $moneda;

                $monedaSave = $em->getRepository(Moneda::class)->
                    find($moneda->getId());
                $monedaSave->setVecesRecibidas(8);
                $monedaSave->setRango(4);

            }
            if ($moneda->getRango() == 2) {
                $monedaSave = $em->getRepository(Moneda::class)->find
                    ($moneda->getId());
                $monedaSave->setRango(3);

                $diamanteApoyo = $em->getRepository(MonedaApoyo::class)->
                    findByMoneda($monedaSave);
                if ($diamanteApoyo) {
                    $diamanteA = $diamanteApoyo[0]->getMonedaDApoyo();
                    if ($diamante != $diamanteA) {
                        $vecesRecividas = $diamanteA->GetVecesRecibidas();
                        $diamanteA->setVecesRecibidas($vecesRecividas + 1);
                        $em->persist($diamanteA);
                        $em->flush();
                    }
                }
                $user = $em->getRepository(User::class)->
                    find($moneda->getDueño()->getId());
                $monedaNew = new Moneda();
                $monedaNew->setDueño($user);
                $monedaNew->setVecesRecibidas(0);
                $monedaNew->setRango(0);
                $monedaNew->setDono(false);
                $em->persist($monedaNew);
                $em->flush();

            }
            if ($moneda->getRango() == 1) {
                $monedaSave = $em->getRepository(Moneda::class)->find
                    ($moneda->getId());
                $monedaSave->setRango(2);
            }
            if ($moneda->getRango() == 0) {
                $monedaSave = $em->getRepository(Moneda::class)->find
                    ($moneda->getId());
                $monedaSave->setRango(1);
            }
            $em->persist($monedaSave);
            $em->flush();
        }
    }

    /**
     * Devuelve el diamante de apoyo asociado a una moneda
     *
     * @param int $id
     * @return array $data
     */
    private function findDiamantesApoyo($id)
    {
        $em = $this->getDoctrine()->getManager();

        $diamanteApoyo = $em->getRepository(MonedaApoyo::class)->
            findByMoneda($monedaSave);
        $diamanteA;
        if ($diamanteApoyo) {
            $diamanteA = $diamanteApoyo[0]->getMonedaDApoyo();
            return $diamanteA;
        }
        return null;
    }

    /**
     * Devuelve el diamante asociado a una moneda
     *
     * @param int $id
     * @return array $data
     */
    private function findArbol($id)
    {
        $em = $this->getDoctrine()->getManager();
        $moneda = $em->getRepository(Moneda::class)->find($id);
        if (!$moneda) {
            throw $this->createNotFoundException('Moneda no encontrada');
        }
        $invitado = $em->getRepository(MonedaMoneda::class)->
            findByMonedaInvitado($id);

        $data = [];
        if ($invitado | $moneda->getRango() >= 3) {
            switch ($moneda->getRango()) {
                case 0:
                    $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
                        findByMonedaInvitado($moneda);
                    if ($monedaMonedas[0]->getMonedaPropietario()->getRango() == 1) {
                        $rubi = $monedaMonedas[0]->getMonedaPropietario();
                    }
                    $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
                        findByMonedaInvitado($rubi);
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
                    $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
                        findByMonedaInvitado($moneda);
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
                case 2:
                    $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
                        findByMonedaInvitado($moneda);
                    if ($monedaMonedas[0]->getMonedaPropietario()->getRango() == 3) {
                        $diamante = $monedaMonedas[0]->getMonedaPropietario();
                    }
                    $data = $this->arbolDeMoneda($diamante);
                    break;
                case 3:
                    $data = $this->arbolDeMoneda($moneda);
                    break;
                case 4:
                    $findOros = $em->getRepository(MonedaApoyo::class)->
                        findByMonedaDApoyo($moneda->getId());

                    $oros = [];
                    foreach ($findOros as $key => $oro) {
                        array_push($oros, [['Padre' => $oro->getMoneda()]]);
                    }
                    $data = $oros;
                    break;
            }
        }
        return $data;
    }

    /**
     * Devuelve el todas las relaciones del diamante
     * y sus hijos hasta llegar a los oros
     *
     * @param  Moneda $diamanteMoneda
     * @return array $data
     */
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
            array_push($monedaEsmeralda[$keym], ["Padre" => $monedaMoneda->
                    getMonedaInvitado()]);
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
        foreach ($monedaEsmeralda as &$esmeralda) {
            $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
                findByMonedaPropietario($esmeralda[0]['Padre']);
            foreach ($monedaMonedas as $keym => $monedaMoneda) {
                $monedasRubies[$cont] = [];
                array_push($monedasRubies[$cont], ["Padre" => $monedaMoneda->
                        getMonedaInvitado()]);
                $cont++;
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
        foreach ($monedasRubies as &$rubi) {
            $monedaMonedas = $em->getRepository(MonedaMoneda::class)->
                findByMonedaPropietario($rubi[0]['Padre']);
            foreach ($monedaMonedas as $keym => $monedaMoneda) {
                $monedasOros[$cont] = [];
                array_push($monedasOros[$cont], ["Padre" => $monedaMoneda->
                        getMonedaInvitado()]);
                $cont++;
            }
        }
        $DE = array_merge($monedaDiamante, $monedaEsmeralda);
        $RO = array_merge($monedasRubies, $monedasOros);
        $data = array_merge($DE, $RO);

        return $data;
    }
}
