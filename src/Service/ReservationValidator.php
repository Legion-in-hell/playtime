<?php

namespace App\Service;

use App\Entity\Reservation;

class ReservationValidator
{
    public function validate(Reservation $reservation): ValidationResult
    {
        $result = new ValidationResult();
        
        $company = $reservation->getSportCompany();
        $date = $reservation->getDate();
        $time = $reservation->getTime();
        
        if (!$date || !$time) {
            $result->addError('La date et l\'heure sont requises.');
            return $result;
        }

        $dateTime = new \DateTime($date->format('Y-m-d') . ' ' . $time);
        
        if ($dateTime <= new \DateTime()) {
            $result->addError('La date de réservation doit être dans le futur.');
        }
        
        $dayOfWeek = strtolower($dateTime->format('l')); // Jour en anglais et en minuscules
        
        $schedule = $company->getSchedules()->filter(function($s) use ($dayOfWeek) {
            return strtolower($s->getDayOfWeek()) === $dayOfWeek;
        })->first();
        
        if (!$schedule) {
            $result->addError('L\'entreprise est fermée ce jour-là.');
        } else {
            $openingTime = $schedule->getOpeningTime();
            $closingTime = $schedule->getClosingTime();
            
            $reservationTime = \DateTime::createFromFormat('H:i', $time);
            
            if ($reservationTime < $openingTime || $reservationTime > $closingTime) {
                $result->addError('L\'entreprise est fermée à cette heure-là.');
            }
        }
                
        return $result;
    }
}

class ValidationResult
{
    private $errors = [];
    
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    public function isValid(): bool
    {
        return empty($this->errors);
    }
}