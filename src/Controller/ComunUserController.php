<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\UserType;
use App\Entity\User;

class ComunUserController extends AbstractController
{

    public function index()
    {
        $user = $this->getUser();
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }


    public function edit($id, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        $user = $em->getRepository(User::class)->find($id);
        $form = $this->createForm(UserType::class, $user);
        $json = $request->request->all();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPlainPassword()) {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }
            if (array_key_exists('user_referido', $json['user'])) {
                $userReferido = $em->getRepository(User::class)->find($json['user']['user_referido']);
                $user->setReferido($userReferido);
            }
            $em->persist($user);
            $em->flush();


            return $this->redirectToRoute('dasboard_user');
        }
        $user = $this->getUser();
        $usuarios = $em->getRepository(User::class)->findAll();

        return $this->render(
            'user/editProfile.html.twig',
            ['form' => $form->createView(),
            'usuarios' => $usuarios,
            'user' => $user,
            ]
        );
    }
}
