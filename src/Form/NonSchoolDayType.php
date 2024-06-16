<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\NonSchoolDay;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NonSchoolDayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('band', EntityType::class, [
                'class' => Group::class,
                'label' => 'Grupo',
                'disabled' => true
            ])
            ->add('description', TextType::class, [
                'label' => 'Descripción',
            ])
            ->add('dayDate', DateType::class, [
                'label' => 'Fecha',
                'widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NonSchoolDay::class,
        ]);
    }
}
