<?php

namespace App\Form;

use App\Entity\Absence;
use App\Entity\Driver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbsenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('driver', EntityType::class, [
                'label' => 'Conductor',
                'class' => Driver::class,
                'disabled' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descripcion',
            ])
            ->add('absenceDate', DateType::class, [
                'label' => 'Fecha',
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Absence::class,
        ]);
    }
}
