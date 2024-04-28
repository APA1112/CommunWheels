<?php

namespace App\Form;

use App\Entity\Driver;
use App\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DriverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Apellidos',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('seats', NumberType::class, [
                'label' => 'Asientos libres',
                'required' => true,
            ])
            ->add('waitTime', NumberType::class, [
                'label' => 'Margen horario',
                'required' => true,
            ])
            ->add('groupCollection', EntityType::class, [
                'label' => 'Grupos',
                'class' => Group::class,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Driver::class,
        ]);
    }
}
