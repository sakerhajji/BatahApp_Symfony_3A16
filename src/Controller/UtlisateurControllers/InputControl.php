<?php

namespace App\Controller\UtlisateurControllers;

use App\Controller\DateTime;

class InputControl
{
    public function __construct()
    {
    }

    function verifierNom($nom)
    {
        if (trim($nom) === '') {
            return false;
        }
        if (preg_match('/\d/', $nom)) {
            return false;
        }
        if (preg_match('/[^a-zA-Z\s]/', $nom)) {
            return false;
        }
        return true;
    }

    // Function to verify email
    function verifyEmail($email)
    {
        $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
        return preg_match($emailRegex, $email);
    }

    // Function to check password strength
    function checkPasswordStrength($password)
    {
        if (strlen($password) < 6) return 0;
        $pattern = '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!])(?=\S+$).{6,}$/';
        return preg_match($pattern, $password) ? 1 : -1;
    }


    function isDateValidAndOver18($selectedDate)
    {
        $selectedDateObj = new DateTime($selectedDate);
        $currentDate = new DateTime();
        if ($selectedDateObj > $currentDate) {
            return false;
        }
        $ageInterval = $currentDate->diff($selectedDateObj);
        $ageInYears = $ageInterval->y;
        return $ageInYears >= 18;
    }
}
