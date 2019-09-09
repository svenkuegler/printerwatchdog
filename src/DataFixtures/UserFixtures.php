<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // Create Admin User
        $userAdmin = new User();
        $userAdmin->setUsername("admin")
            ->setIsActive(true)
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setEmail('admin@printerwatchdog.org')
            ->setPassword($this->passwordEncoder->encodePassword($userAdmin,'123456'))
            ->setSource('local')
            ;
        $manager->persist($userAdmin);


        // Create Normal User
        $user = new User();
        $user->setUsername("testuser")
            ->setIsActive(true)
            ->setRoles(['ROLE_USER'])
            ->setEmail('testuser@printerwatchdog.org')
            ->setPassword($this->passwordEncoder->encodePassword($user,'123456'))
            ->setSource('local')
        ;
        $manager->persist($user);


        // Create inactive User
        $userInactive = new User();
        $userInactive->setUsername("testuserinactive")
            ->setIsActive(false)
            ->setRoles(['ROLE_USER'])
            ->setEmail('testuser-inactive@printerwatchdog.org')
            ->setPassword($this->passwordEncoder->encodePassword($userInactive,'123456'))
            ->setSource('local')
        ;
        $manager->persist($userInactive);

        // Create Normal LDAP User
        $ldap_user = new User();
        $ldap_user->setUsername("ldapuser@mydomain.local")
            ->setIsActive(true)
            ->setRoles(['ROLE_USER'])
            ->setEmail('ldapuser@printerwatchdog.org')
            ->setPassword(null)
            ->setSource('ldap')
        ;
        $manager->persist($ldap_user);

        $manager->flush();
    }
}
