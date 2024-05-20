<?php

namespace App\Form;

use App\Entity\Driver;
use App\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class DriverType extends AbstractType
{
    private $security;
    public $data = false;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $driver = $options['data'];

        $user = $driver->getUser();

        $this->data = $user->isIsGroupAdmin();

        $builder
            ->add('name', TextType::class, [
                'label'         => 'Nombre',
                'required'      => true,
            ])
            ->add('lastName', TextType::class, [
                'label'         => 'Apellidos',
                'required'      => true,
            ])
            ->add('email', EmailType::class, [
                'label'         => 'Email',
                'required'      => true,
            ])
            ->add('seats', IntegerType::class, [
                'label'         => 'Asientos libres en el coche',
                'required'      => true,
            ])
            ->add('waitTime', IntegerType::class, [
                'label'         => 'Margen horario (número del 0 al 2)',
                'required'      => true,
            ]);

            if ($this->security->isGranted('ROLE_GROUP_ADMIN')) {
                $builder->add('isAdmin', CheckboxType::class, [
                    'label'     => 'Es Administrador',
                    'data'      => $this->data,
                    'mapped'    => false,
                    'required'  => false
                ]);
            }
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'        => Driver::class,
        ]);
    }
}
