<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use DateTime;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNombres('admin');
        $user->setApellidos('admin');
        $user->setTelmovil('0000');
        $user->setEmail('admin@suenodorado.com');
        $user->setPais('none');
        $user->setCiudad('none');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setFechaCreacion(new DateTime(date("Y-m-d H:i:s")));
        $password = $this->encoder->encodePassword($user, 'admin85204561583#');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}
