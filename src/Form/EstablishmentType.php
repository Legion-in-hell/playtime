<?php

namespace App\Form;

use App\Entity\Establishment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EstablishmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'required' => true,
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
                'required' => true,
            ])
            ->add('phone', IntegerType::class, [
                'label' => 'Numéro de téléphone',
                'required' => true,
            ])
            ->add('email', TextareaType::class, [
                'label' => 'email',
                'required' => true,
            ])
            ->add('website', TextareaType::class, [
                'label' => 'site web',
                'required' => false,
            ])
            ->add('siret', IntegerType::class, [
                'label' => 'Numéro SIRET',
                'required' => true,
            ])
            ->add('create', SubmitType::class, [
                'label' => 'create',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Establishment::class,
        ]);
    }
}