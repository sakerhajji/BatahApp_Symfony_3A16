function verifierNom(nom) {
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

function verifyEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function checkPasswordStrength(password) {
    if (password.length < 6) return 0;

    const pattern = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!])(?=\S+$).{6,}$/;
    const regex = new RegExp(pattern);

    if (regex.test(password)) {
        return 1;
    } else {
        return -1;
    }
}

function isDateValidAndOver18(selectedDate) {
    var selectedDateObj = new Date(selectedDate);
    var currentDate = new Date();

    if (selectedDateObj.getTime() > currentDate.getTime()) {
        return false;
    }

    var ageInMilliseconds = currentDate.getTime() - selectedDateObj.getTime();
    var ageInYears = ageInMilliseconds / (1000 * 3600 * 24 * 365);

    return ageInYears >= 18;
}

var first_name = document.getElementById('first_name');
var last_name = document.getElementById('last_name');
var email = document.getElementById('email');
var password = document.getElementById('password');
var confirm_password = document.getElementById('confirm_password');
var date_de_naissance = document.getElementById('date_de_naissance');

first_name.addEventListener('input', function() {
    if (!verifierNom(this.value)) {
        first_name.style.border = '1px solid red';
    } else {
        first_name.style.border = '1px solid green';
    }
});
last_name.addEventListener('input', function() {
    if (!verifierNom(this.value)) {
        last_name.style.border = '1px solid red';
    } else {
        last_name.style.border = '1px solid green';
    }
});
email.addEventListener('input', function() {
    if (!verifyEmail(this.value)) {
        email.style.border = '1px solid red';
    } else {
        email.style.border = '1px solid green';
    }
});
password.addEventListener('input', function() {
    if (!checkPasswordStrength(this.value)) {
        password.style.border = '1px solid red';
    } else {
        password.style.border = '1px solid green';
    }
});
confirm_password.addEventListener('input', function() {
    if (password.value !== confirm_password.value) {
        confirm_password.style.border = '1px solid red';
    } else {
        confirm_password.style.border = '1px solid green';
    }
});
date_de_naissance.addEventListener('input', function() {
    if (!isDateValidAndOver18(this.value)) {
        date_de_naissance.style.border = '1px solid red';
    } else {
        date_de_naissance.style.border = '1px solid green';
    }
});



