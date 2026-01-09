<?php

namespace App\Form;

use App\Entity\Salle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;


class EvenementCreationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('dateDebut', DateTimeType::class, [
                'mapped' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('dateFin', DateTimeType::class, [
                'mapped' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => 'nom',
                'mapped' => false,
            ]);
    }
}
