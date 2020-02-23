<?php

namespace App\Controller;

use App\Entity\Mensaje;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use DateTime;

class SoporteController extends AbstractController
{
    /**
     * @Route("/admin/soporte/index", name="soporte_admin")
     */
    public function index()
    {
        return $this->render('soporte/index.html.twig', [
            'controller_name' => 'SoporteController',
        ]);
    }

    function list(EntityManagerInterface $em) {
        
        $users = $em->getRepository(User::class)->findAll();

        $data = [];
        $leido;
        foreach ($users as $key => $user) {
            $mensajes = $em->getRepository(Mensaje::class)->findByRemitente($user);
            $leido = 0 ;
            
            foreach ($mensajes as $key => $mensaje) {
                if ($mensaje->getLeido() == false) {
                    $leido ++;
                }
            }
            if (count($mensajes) > 1) {
                array_push($data,  [
                    "idUser" => $user->getId(),
                    "nombres" => $user->getNombres() . " " .  $user->getApellidos(),
                    "mensajes" => $leido,
                    "fecha" => $mensajes[count($mensajes) - 1 ]->getFechaEnvio()->format('m-d H:i'),
                    ]);
            }
            
        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);


        return new Response($serializer->serialize($data, 'json'));
    }

    
    function listChat(EntityManagerInterface $em, Request $request) {
        
        $de = $request->request->get('de');
        $para = $request->request->get('para');

        $userDe = $em->getRepository(User::class)->find($de);
        $userPara = $em->getRepository(User::class)->find($para);

        $mensajesDe = $em->getRepository(Mensaje::class)->findBy(['remitente' => $de, 'destinatario' => $para], ['fechaEnvio' => 'ASC']);
        $mensajesPara = $em->getRepository(Mensaje::class)->findBy(['remitente' => $para, 'destinatario' => $de], ['fechaEnvio' => 'ASC']);

        $data = [];
        
        foreach ($mensajesDe as $key => $mensajeDe) {
             array_push($data,  [
                "user" => $userDe->getId(),
                "mensaje" => $mensajeDe->getMensaje(),
                "deOrPara" => true,
                "fecha" => $mensajeDe->getFechaEnvio()->format('m-d H:i'),
                ]);
        }

        foreach ($mensajesPara as $key => $mensajePara) {
            array_push($data,  [
               "user" => $userPara->getId(),
               "mensaje" => $mensajePara->getMensaje(),
               "deOrPara" => false,
               "fecha" => $mensajePara->getFechaEnvio()->format('m-d H:i'),
               ]);
       }
        usort($data, function ($a, $b) {
            return strcmp($a["fecha"], $b["fecha"]);
        });
        dump($data);

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($data, 'json'));
    }

    public function edit($id, EntityManagerInterface $em, Request $request)
    {
        $evento = $em->getRepository(Mensaje::class)->find($id);
        $form = $this->createForm(MensajeType::class, $evento);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($evento);
            $em->flush();

            return $this->redirectToRoute('soporte_admin');
        }

        return $this->render(
            'admin/evento/editEvento.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function delete($id, EntityManagerInterface $em, Request $request)
    {
        $evento = $em->getRepository(Mensaje::class)->find($id);
        $em->remove($evento);
        $em->flush();
        return $this->redirectToRoute('soporte_admin');
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
            "fecha" => $mensaje->getFechaEnvio()->format('m-d H:i'),
        ];

        return new Response($serializer->serialize($data, 'json'));
        
    }
}
