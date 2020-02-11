<?php

namespace App\Controller;

use App\Entity\Moneda;
use App\Entity\MonedaApoyo;
use App\Entity\MonedaMoneda;
use App\Entity\User;
use App\Form\UserEditPasswordType;
use App\Form\UserType;
use App\Form\UserTypeRegister;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UsersController extends AbstractController
{

    function list(EntityManagerInterface $em) {
        $user = $em->getRepository(User::class)->findAll();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $data = [$user];
        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($data, 'json'));
    }

    public function delete($id, EntityManagerInterface $em, Request $request)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $user = $em->getRepository(User::class)->find($id);
        $moneda = $em->getRepository(Moneda::class)->findOneByDueño($id);
        $monedaMonedas = $em->getRepository(MonedaMoneda::class)->findByMonedaPropietario($moneda);
        foreach ($monedaMonedas as $key => $monedaMoneda) {
            $em->remove($monedaMoneda);

        }
        $monedaDApoyo = $em->getRepository(MonedaApoyo::class)->findOneByMoneda($id);
        dump($monedaDApoyo);
        if ($monedaDApoyo) {
            $em->remove($monedaDApoyo);

        }
        if ($moneda) {
            $em->remove($moneda);

        }

        try {
            $em->flush();
        } catch (\Throwable $th) {
            $error = 'OCURRIO UN ERROR AL BORRAR LAS RELACIONES CON LAS MONEDAS';
            return new Response($serializer->serialize($error, 'json'));
        }
        $em->remove($user);
        try {
            $em->flush();
        } catch (\Throwable $th) {
            $error = 'OCURRIO UN ERROR AL BORRAR EL USUARIO';
            return new Response($serializer->serialize($error, 'json'));
        }

        return new Response(0);
    }

    function new (
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em) {
        $user = new User();
        $form = $this->createForm(UserTypeRegister::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->
                encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setFechaCreacion(new DateTime(date("Y-m-d H:i:s")));
            $em->persist($user);

            try {
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                return $this->render(
                    'security/register.html.twig', [
                        'form' => $form->createView(),
                        'error' => true,
                    ]);
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView())
        );
    }

    public function edit(
        $id,
        EntityManagerInterface $em,
        Request $request
    ) {
        $user = $em->getRepository(User::class)->find($id);
        $userLogueado = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $json = $request->request->all();
        $form->handleRequest($request);
        if ($userLogueado->getRoles()[0] == "ROLE_ADMIN") {
            if ($form->isSubmitted() && $form->isValid()) {
                if (array_key_exists("user_edit", $json)) {
                    $user->setRoles(["ROLE_ADMIN"]);
                } else {
                    $user->setRoles(["ROLE_USER"]);
                }

                $em->persist($user);

                try {
                    $em->flush();
                } catch (\Doctrine\DBAL\DBALException $e) {
                    return $this->render(
                        'bundles/TwigBundle/viws/Exception/error409.html.twig', [
                            'form' => $form->createView(),
                            'error' => true,
                        ]);
                }

                return $this->redirectToRoute('dasboard');
            }

            return $this->render(
                'admin/editUser.html.twig',
                ['form' => $form->createView(),
                    'user' => $user,
                ]
            );
        } elseif ($userLogueado->getRoles()[0] == "ROLE_USER") {
            if ($userLogueado->getId() == $id) {
                if ($form->isSubmitted() && $form->isValid()) {
                    $em->persist($user);
                    $em->flush();

                    return $this->redirectToRoute('dasboard_user');
                }
                $user = $this->getUser();

                return $this->render(
                    'user/editProfile.html.twig',
                    ['form' => $form->createView(),
                        'user' => $user,
                    ]
                );
            } else {
                return $this->redirectToRoute('edit_user', [
                    'id' => $userLogueado->getId(),
                ]);
            }
        }
        return $this->redirectToRoute('dasboard');
    }

    public function editPassword(
        $id,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request
    ) {
        $user = $em->getRepository(User::class)->find($id);
        $userLogueado = $this->getUser();
        $form = $this->createForm(UserEditPasswordType::class, $user);
        $json = $request->request->all();
        $form->handleRequest($request);
        if ($userLogueado->getRoles()[0] == "ROLE_ADMIN") {
            if ($form->isSubmitted() && $form->isValid()) {
                if ($user->getPlainPassword()) {
                    $password = $passwordEncoder->
                        encodePassword($user, $user->getPlainPassword());
                    $user->setPassword($password);
                }
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('dasboard');
            }
            $user = $this->getUser();

            return $this->render(
                'admin/editPassword.html.twig',
                ['form' => $form->createView(),
                    'user' => $user,
                ]
            );
        } elseif ($userLogueado->getRoles()[0] == "ROLE_USER") {
            if ($userLogueado->getId() == $id) {
                if ($form->isSubmitted() && $form->isValid()) {
                    if ($user->getPlainPassword()) {
                        $password = $passwordEncoder->
                            encodePassword($user, $user->getPlainPassword());
                        $user->setPassword($password);
                    }
                    $em->persist($user);
                    $em->flush();

                    return $this->redirectToRoute('dasboard_user');
                }
                $user = $this->getUser();

                return $this->render(
                    'user/editProfilePassword.html.twig',
                    ['form' => $form->createView(),
                        'user' => $user,
                    ]
                );
            } else {
                return $this->redirectToRoute('edit_user_password', [
                    'id' => $userLogueado->getId(),
                ]);
            }
        }
        return $this->redirectToRoute('dasboard');
    }
}
