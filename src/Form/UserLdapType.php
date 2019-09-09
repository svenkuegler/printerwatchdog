<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;


class UserLdapType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => array(
                    'readonly' => true,
                ),
                'help' => $this->translator->trans("Username cannot changed, provided by LDAP")
            ])
            ->add('email')
            ->add("roles", ChoiceType::class, [
                'label' => "Role",
                'multiple' => true,
                'choices' => [
                    $this->translator->trans('Administrator') => 'ROLE_ADMIN',
                    $this->translator->trans('User') => 'ROLE_USER'
                ]
            ])
            ->add('source', HiddenType::class,[
                'data' => 'ldap'
            ])
            /*->add('password', HiddenType::class,[
                'data' => 'passwordProvidedByLdapConnection'
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
