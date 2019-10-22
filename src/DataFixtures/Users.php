<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class User extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {


        $user = new Users();
        $user->setUsername('root');
        $user->setEmail('root@ngost.by');
        $user->setPhone('+375297788585');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER', 'ROLE_SUPER_ADMIN']);
        $plainPassword = 'root';
        $encoded = $this->encoder->encodePassword($user, $plainPassword);

        $user->setPassword($encoded);
        $manager->persist($user);

        $manager->flush();
    }
}
