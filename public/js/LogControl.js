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


var email = document.getElementById('email');
var password = document.getElementById('password');



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

function handleSubmit(event) {
    event.preventDefault();

    var email = document.getElementById('email');
    var password = document.getElementById('password');

    if (!verifyEmail(email.value)) {
        displayErrorMessage("Invalid email address");
        return;
    }

    if (!checkPasswordStrength(password.value)) {
        displayErrorMessage("Invalid password");
        return;
    }


}

function displayErrorMessage(message) {
    if (message.trim() !== "") {
        alert(message);
    }
}

var form = document.getElementById('login_form');
form.addEventListener('submit', handleSubmit);

