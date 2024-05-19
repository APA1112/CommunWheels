<?php

namespace App\Form;

use App\Entity\Schedule;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;

class ScheduleType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('weekDay', ChoiceType::class, [
                'choices' => [
                    'Monday' => 1,
                    'Tuesday' => 2,
                    'Wednesday' => 3,
                    'Thursday' => 4,
                    'Friday' => 5,
                ],
                'label' => 'Dia de la semana',
                'disabled' => true
            ])
            ->add('entrySlot', IntegerType::class, [
                'label' => 'Hora de entrada',
                'constraints' => [
                    new NotNull(),
                    new Assert\Range([
                        'min'=>0,
                        'max'=>6,
                        'minMessage'=>'La hora de entrada no puede ser menor a cero',
                        'maxMessage'=>'La hora de entrada no puede ser mayor a seis',
                        ]),
                ]
            ])
            ->add('exitSlot', IntegerType::class, [
                'label' => 'Hora de salida',
                'constraints' => [
                    new NotNull(),
                    new Assert\Range([
                        'min'=>0,
                        'max'=>6,
                        'minMessage'=>'La hora de salida no puede ser menor a cero',
                        'maxMessage'=>'La hora de salida no puede ser mayor a seis',
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
