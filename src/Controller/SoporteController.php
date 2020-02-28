<?php

namespace App\Controller;

use App\Entity\Mensaje;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SoporteController extends AbstractController
{

    public function indexAdmin()
    {
        return $this->render('admin/soporte/index.html.twig', [
            'controller_name' => 'SoporteController',
        ]);
    }

    public function indexUser()
    {
        return $this->render('user/soporte/index.html.twig', [
            'controller_name' => 'SoporteController',
        ]);
    }

    public function listUser(EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $data = [];

        $mensajes = $em->getRepository(Mensaje::class)->findBy(['remitente' => $user]);
        $mensajesRespuesta = $em->getRepository(Mensaje::class)->findBy(['destinatario' => $user]);

        $userResividos = [];

        $leido = 0;
        foreach ($mensajesRespuesta as $key => $mensajeRespuesta) {
            $userResponse = $mensajeRespuesta->getRemitente();
            if (!in_array($userResponse, $userResividos)) {
                array_push($userResividos, $userResponse);
            }
        }

        foreach ($userResividos as $key => $userResivido) {
            $mensajesExistentes = $em->getRepository(Mensaje::class)->findBy(['remitente' => $userResivido, 'destinatario' => $user]);
            $leido = 0;
            foreach ($mensajesExistentes as $key => $mensajeExistentes) {
                if (!$mensajeExistentes->getLeido()) {
                    $leido++;
                }
            }

            if ($userResivido != null) {
                array_push($data, [
                    "idUser" => $userResivido->getId(),
                    "nombres" => $userResivido->getNombres() . " " . $userResivido->getApellidos(),
                    "mensajes" => $leido,
                    "fecha" => $mensajesRespuesta[count($mensajesRespuesta) - 1]->getFechaEnvio()->format('m-d H:i'),
                ]);
            }
        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($data, 'json'));
    }

    function list(EntityManagerInterface $em) {

        $users = $em->getRepository(User::class)->findAll();
        $userLog = $this->getUser();
        $data = [];
        $leido;
        foreach ($users as $key => $user) {

            $mensajes = $em->getRepository(Mensaje::class)->findByRemitente($user);
            $leido = 0;

            foreach ($mensajes as $key => $mensaje) {
                if ($mensaje->getDestinatario() == $userLog && $mensaje->getLeido() == false) {
                    $leido++;
                }
            }
            $roles = $user->getRoles();
            if (count($mensajes) > 0 && $userLog->getId() !== $user->getId() && $roles[0] !== "ROLE_ADMIN") {
                array_push($data, [
                    "idUser" => $user->getId(),
                    "nombres" => $user->getNombres() . " " . $user->getApellidos(),
                    "mensajes" => $leido,
                    "fecha" => $mensajes[count($mensajes) - 1]->getFechaEnvio()->format('m-d H:i'),
                ]);
            }

        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($data, 'json'));
    }

    public function visto(EntityManagerInterface $em, Request $request)
    {
        $user = $this->getUser();
        $chatCon = $request->request->get('con');
        $userCon = $em->getRepository(User::class)->find($chatCon);
        $mensajes = $em->getRepository(Mensaje::class)->findBy(['remitente' => $userCon, 'destinatario' => $user]);

        foreach ($mensajes as $key => $mensaje) {
            $mensaje->setLeido(true);
            $em->persist($mensaje);
            $em->flush();
        }

        return new Response(0);
    }

    public function listChat(EntityManagerInterface $em, Request $request)
    {

        $de = $request->request->get('de');
        $para = $request->request->get('para');

        $userDe = $em->getRepository(User::class)->find($de);
        $userPara = $em->getRepository(User::class)->find($para);

        $mensajesDe = $em->getRepository(Mensaje::class)->findBy(['remitente' => $de, 'destinatario' => $para], ['fechaEnvio' => 'ASC']);
        $mensajesPara = $em->getRepository(Mensaje::class)->findBy(['remitente' => $para, 'destinatario' => $de], ['fechaEnvio' => 'ASC']);

        $mensajesSinLigar = $em->getRepository(Mensaje::class)->findBy(['remitente' => $para, 'destinatario' => null], ['fechaEnvio' => 'ASC']);

        $data = [];
        foreach ($mensajesSinLigar as $key => $mensajeSinLigar) {
            $mensajeSinLigar->setLeido(true);
            $mensajeSinLigar->setDestinatario($userDe);

            array_push($data, [
                "user" => $userDe->getId(),
                "mensaje" => $mensajeSinLigar->getMensaje(),
                "deOrPara" => false,
                "fecha" => $mensajeSinLigar->getFechaEnvio()->format('m-d H:i:s'),
            ]);
            $em->persist($mensajeSinLigar);
            $em->flush();
        }

        foreach ($mensajesDe as $key => $mensajeDe) {
            array_push($data, [
                "user" => $userDe->getId(),
                "mensaje" => $mensajeDe->getMensaje(),
                "deOrPara" => true,
                "fecha" => $mensajeDe->getFechaEnvio()->format('m-d H:i:s'),
            ]);
        }

        foreach ($mensajesPara as $key => $mensajePara) {
            array_push($data, [
                "user" => $userPara->getId(),
                "mensaje" => $mensajePara->getMensaje(),
                "deOrPara" => false,
                "fecha" => $mensajePara->getFechaEnvio()->format('m-d H:i:s'),
            ]);
        }
        usort($data, function ($a, $b) {
            return strcmp($a["fecha"], $b["fecha"]);
        });

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($data, 'json'));
    }

    function new (Request $request, EntityManagerInterface $em) {

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $mensajeRecivido = $request->request->get('mensaje');
        $de = $request->request->get('de');
        $para = $request->request->get('para');
        $userDe = $em->getRepository(User::class)->find($de);
        $userPara = $em->getRepository(User::class)->find($para);
        $mensaje = new Mensaje();
        if ($mensajeRecivido != null) {
            $fechaHoy = new DateTime(date("Y-m-d H:i:s"));
            $mensaje->setFechaEnvio($fechaHoy);
            $mensaje->setRemitente($userDe);
            $mensaje->setDestinatario($userPara);
            $mensaje->setMensaje($mensajeRecivido);
            $em->persist($mensaje);
            $em->flush();
        }

        $data = [
            "mensaje" => $request->request->get('mensaje'),
            "fecha" => $mensaje->getFechaEnvio()->format('m-d H:i:s'),
        ];

        return new Response($serializer->serialize($data, 'json'));

    }

    public function newSoporte(Request $request, EntityManagerInterface $em)
    {

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $mensajeRecivido = $request->request->get('mensaje');
        $de = $request->request->get('de');

        $userDe = $em->getRepository(User::class)->find($de);

        $mensaje = new Mensaje();
        if ($mensajeRecivido != null) {
            $fechaHoy = new DateTime(date("Y-m-d H:i:s"));
            $mensaje->setFechaEnvio($fechaHoy);
            $mensaje->setRemitente($userDe);
            $mensaje->setMensaje($mensajeRecivido);
            $em->persist($mensaje);
            $em->flush();
        }

        $data = [
            "mensaje" => $request->request->get('mensaje'),
            "fecha" => $mensaje->getFechaEnvio()->format('m-d H:i:s'),
        ];

        return new Response($serializer->serialize($data, 'json'));

    }
}
