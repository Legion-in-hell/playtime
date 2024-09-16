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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EstablishmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'required' => true,
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
                'required' => true,
            ])
            ->add('postalCode', IntegerType::class, [
                'label' => 'Code postal',
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'required' => true,
            ])
            ->add('phone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'required' => true,
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('website', TextType::class, [
                'label' => 'Site web',
                'required' => false,
            ])
            ->add('siret', TextType::class, [
                'label' => 'Numéro SIRET',
                'required' => true,
            ])
            ->add('openingHours', CollectionType::class, [
                'entry_type' => OpeningHourType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'attr' => [
                    'class' => 'opening-hours-collection',
                ],
            ])
            ->add('create', SubmitType::class, [
                'label' => 'Créer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Establishment::class,
        ]);
    }
}