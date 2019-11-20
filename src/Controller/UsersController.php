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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\UserEditType;
use App\Form\UserType;
use App\Entity\User;
use DateTime;

class UsersController extends AbstractController
{

    public function list(EntityManagerInterface $em)
    {
        $repository = $em->getRepository(User::class);
        $usuarios = $repository->findAll();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($usuarios, 'json'));
    }

    public function edit($id, EntityManagerInterface $em, Request $request)
    {
        $user = $em->getRepository(User::class)->find($id);
        $form = $this->createForm(UserEditType::class, $user);

        $json = $request->request->all();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (array_key_exists('user_referido', $json['user_edit'])) {
                $userReferido = $em->getRepository(User::class)->find($json['user_edit']['user_referido']);
                $user->setReferido($userReferido);
            }
            if (array_key_exists('user_rol', $json['user_edit'])) {
                $user->setRoles(['ROLE_ADMIN']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('dasboard');
        }

        $usuarios = $em->getRepository(User::class)->findAll();
        return $this->render(
            'admin/editUser.html.twig',
            ['form' => $form->createView(),
            'usuarios' => $usuarios,
            'user' => $user,
            ]
        );
    }

    public function delete($id, EntityManagerInterface $em, Request $request)
    {
        $user = $em->getRepository(User::class)->find($id);

        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('dasboard');
    }

    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setMonedasBitcoin(0);
            $user->setMonedasMarketcoin(0);
            $user->setVecesRecividas(0);
            $user->setFechaCreacion(new DateTime(date("Y-m-d H:i:s")));

            $em->persist($user);
            $em->flush();


            return $this->redirectToRoute('app_login');
        }

         return $this->render(
             'security/register.html.twig',
             array('form' => $form->createView())
         );
    }
}
