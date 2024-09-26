<?php
namespace App\Form;

use App\Entity\SportCompany;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SportCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de l\'entreprise'])
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class, ['label' => 'Mot de passe'])
            ->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('lastName', TextType::class, ['label' => 'Nom'])
            ->add('phoneNumber', TelType::class, ['label' => 'Numéro de téléphone'])
            ->add('address', TextType::class, ['label' => 'Adresse'])
            ->add('description', TextareaType::class)
            ->add('isSubscribed', CheckboxType::class, ['label' => 'Abonné', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SportCompany::class,
        ]);
    }
}