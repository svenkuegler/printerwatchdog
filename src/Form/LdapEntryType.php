<?php

namespace App\Form;

use App\Entity\LdapUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class LdapEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', HiddenType::class)
            ->add('mail', HiddenType::class)
            ->add("displayName", HiddenType::class)
            ->add('userPrincipalName', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LdapUser::class,
        ]);
    }
}
