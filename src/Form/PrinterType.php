<?php

namespace App\Form;

use App\Entity\Printer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrinterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name')
            ->add('Ip')
            ->add('SerialNumber')
            ->add('Location')
            ->add('lastCheck')
            ->add('isColorPrinter')
            ->add('TonerBlack')
            ->add('TonerYellow')
            ->add('TonerMagenta')
            ->add('TonerCyan')
            ->add('Type')
            ->add('TotalPages');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Printer::class,
        ]);
    }
}
