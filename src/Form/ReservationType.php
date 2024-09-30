<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\SportCompany;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $company = $options['company'];

        $builder
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'query_builder' => function ($er) use ($company) {
                    return $er->createQueryBuilder('s')
                        ->where('s.sportCompany = :company')
                        ->setParameter('company', $company);
                },
                'label' => 'Service',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker'],
                'label' => 'Date',
            ])
            ->add('time', ChoiceType::class, [
                'choices' => [],
                'label' => 'Heure',
                'placeholder' => 'Choisissez d\'abord une date',
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($company) {
            $form = $event->getForm();
            $data = $event->getData();

            if (isset($data['date'])) {
                $date = new \DateTime($data['date']);
                $dayOfWeek = strtolower($date->format('l'));
                $schedule = $company->getSchedules()->filter(function($s) use ($dayOfWeek) {
                    return strtolower($s->getDayOfWeek()) === $dayOfWeek;
                })->first();

                if ($schedule) {
                    $timeChoices = $this->generateTimeChoices($schedule->getOpeningTime(), $schedule->getClosingTime());

                    $form->add('time', ChoiceType::class, [
                        'choices' => array_combine($timeChoices, $timeChoices),
                        'label' => 'Heure',
                    ]);
                }
            }
        });
    }

    private function generateTimeChoices(\DateTimeInterface $openTime, \DateTimeInterface $closeTime): array
    {
        $interval = new \DateInterval('PT30M');
        $timeChoices = [];

        $current = clone $openTime;
        while ($current <= $closeTime) {
            $timeChoices[] = $current->format('H:i');
            $current->add($interval);
        }

        return $timeChoices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'company' => null,
        ]);

        $resolver->setRequired('company');
    }
}