<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProfileType;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(Request $request, SessionInterface $session, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()->getSource() == 'ldap') {
            $form->remove('plainPassword');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->has('plainPassword')) {
                $user->setPassword($passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData()));
            }
            $session->set('_locale', $form->getData()->getLocale());
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
