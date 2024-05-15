<?php

namespace App\Form;

use App\Entity\Driver;
use App\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
                'disabled' => true
            ])
            ->add('origin', TextType::class, [
                'label' => 'Origen',
                'required' => true,
            ])
            ->add('destination', TextType::class, [
                'label' => 'Destino',
                'required' => true,
            ])
            ->add('drivers', Select2EntityType::class, [
                'label' => 'Conductores',
                'class' => Driver::class,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
