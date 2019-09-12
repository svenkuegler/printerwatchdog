<?php

namespace App\Controller;

use App\Entity\LdapUser;
use App\Entity\User;
use App\Form\LdapEntryType;
use App\Form\UserLdapType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'ldapEnabled' => (is_null($this->getParameter('ldap.server'))) ? false : true
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIsActive(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{id}/activate", name="user_activate", methods={"GET"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function activate(Request $request, UserRepository $userRepository, TranslatorInterface $translator)
    {
        if($user = $userRepository->findOneBy(['id' => $request->get('id')])) {
            $user->setIsActive(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", $translator->trans("User activated!"));
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("{id}/deactivate", name="user_deactivate", methods={"GET"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deactivate(Request $request, UserRepository $userRepository, TranslatorInterface $translator)
    {
        if($user = $userRepository->findOneBy(['id' => $request->get('id')])) {
            $user->setIsActive(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", $translator->trans("User deactivated!"));
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, User $user, TranslatorInterface $translator): Response
    {
        if($user->getSource() == 'ldap') {
            $form = $this->createForm(UserLdapType::class, $user);
        } else {
            $form = $this->createForm(UserType::class, $user);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", $translator->trans("User successfully changed!"));
            return $this->redirectToRoute('user_index', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/ldap_list", name="user_ldap_list", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function ldapList(UserRepository $userRepository): Response
    {
        if(is_null($this->getParameter('ldap.server'))) {
            return $this->render("user/ldap_disabled.html.twig");
        }

        $ldap = Ldap::create('ext_ldap', ['connection_string' => $this->getParameter('ldap.connection_string')]);
        $ldap->bind($this->getParameter('ldap.bind.user'), $this->getParameter('ldap.bind.password'));

        $query = $ldap->query($this->getParameter('ldap.query_dn'), $this->getParameter('ldap.query_string'));
        $result = $query->execute();

        $existingUser = $userRepository->getAllUsername();

        $ldapResult = [];
        $forms = [];
        foreach ($result as $entry) {
            $lu = new LdapUser();
            $lu->setDisplayName((string)$entry->getAttribute('displayName')[0])
                ->setMail((string)$entry->getAttribute('mail')[0])
                ->setUsername((string)$entry->getAttribute('sAMAccountName')[0])
                ->setUserPrincipalName((string)$entry->getAttribute('userPrincipalName')[0])
                ->setAlreadyExists(in_array((string)$entry->getAttribute('userPrincipalName')[0], $existingUser) )
                ;
            $ldapResult[] = $lu;
            $forms[] = $this->createForm(LdapEntryType::class, $lu, ['action' => '/user/ldap_import'])->createView();
        }

        return $this->render("user/ldap_import.html.twig",[
            'ldapResult' => $ldapResult,
            'forms' => $forms,
            'dn' => $this->formatDn($this->getParameter('ldap.query_dn'))
        ]);
    }

    /**
     * @Route("/ldap_import", name="user_ldap_import", methods={"POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ldapImport(Request $request, TranslatorInterface $translator)
    {
        $ldapUser = new LdapUser();
        $form = $this->createForm(LdapEntryType::class, $ldapUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = new User();

            $user->setUsername($ldapUser->getUserPrincipalName())
                ->setSource('ldap')
                ->setIsActive(false)
                ->setEmail($ldapUser->getMail())
                ->setRoles(['ROLE_USER'])
                ->setPassword('passwordProvidedByLdapConnection')
                ;

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", $translator->trans("User imported successfully."));
        } else {
            $this->addFlash("danger", $translator->trans("Ooops! Something is going wrong!"));
        }

        return $this->redirectToRoute('user_ldap_list');
    }

    /**
     * @param $dn
     * @return string
     */
    private function formatDn($dn) {
        $dn = str_replace("ou=", "/", $dn);
        $dn = str_replace("dc=", "/", $dn);
        $ex = explode(",", $dn);

        return implode('', $ex);
    }
}
