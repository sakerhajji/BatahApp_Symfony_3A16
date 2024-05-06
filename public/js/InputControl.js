class InputControl {
    constructor() {}

    // Function to verify name
    verifierNom(nom) {
        if (nom.trim() === '') {
            return false;
        }
        if (/\d/.test(nom)) {
            return false;
        }
        if (/[^a-zA-Z\s]/.test(nom)) {
            return false;
        }
        return true;
    }

    // Function to verify email
    verifyEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Function to check password strength
    checkPasswordStrength(password) {
        if (password.length < 6) return 0;
        const pattern = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!])(?=\S+$).{6,}$/;
        const regex = new RegExp(pattern);
        return regex.test(password) ? 1 : -1;
    }

    // Function to check if a date is valid and the person is over 18
    isDateValidAndOver18(selectedDate) {
        var selectedDateObj = new Date(selectedDate);
        var currentDate = new Date();
        if (selectedDateObj.getTime() > currentDate.getTime()) {
            return false;
        }
        var ageInMilliseconds = currentDate.getTime() - selectedDateObj.getTime();
        var ageInYears = ageInMilliseconds / (1000 * 3600 * 24 * 365);
        return ageInYears >= 18;
    }
}