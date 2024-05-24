<?php

namespace App\Form;

use App\Entity\Schedule;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $weekDay = [1=>'Lunes', 2=>'Martes', 3=>'Miércoles', 4=>'Jueves', 5=>'Viernes'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($weekDay) {
            $form = $event->getForm();
            $data = $event->getData();

            $form
            ->add('entrySlot', ChoiceType::class, [
                'label' => $weekDay[$data->getWeekDay()] . ' hora de entrada',
                'choices' => [
                    'No voy'   => 0,
                    '8:15'   => 1,
                    '9:15'  => 2,
                    '10:15' => 3,
                    '11:45' => 4,
                    '12:45' => 5,
                    '13:45' => 6,
                ],
            ])
            ->add('exitSlot', ChoiceType::class, [
                'label' => $weekDay[$data->getWeekDay()] . ' hora de salida',
                'choices' => [
                    'No voy'   => 0,
                    '9:15'   => 1,
                    '10:15'  => 2,
                    '11:15' => 3,
                    '12:45' => 4,
                    '13:45' => 5,
                    '14:45' => 6,
                ],
            ]);
        });
        /*$builder
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
            ->add('entrySlot', ChoiceType::class, [
                'label' => 'Hora de entrada',
                'choices' => [
                    'No voy'   => 0,
                    '8:15'   => 1,
                    '9:15'  => 2,
                    '10:15' => 3,
                    '11:45' => 4,
                    '12:45' => 5,
                    '13:45' => 6,
                ],
            ])
            ->add('exitSlot', ChoiceType::class, [
                'label' => 'Hora de salida',
                'choices' => [
                    'No voy'   => 0,
                    '9:15'   => 1,
                    '10:15'  => 2,
                    '11:15' => 3,
                    '12:45' => 4,
                    '13:45' => 5,
                    '14:45' => 6,
                ],
            ]);*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
