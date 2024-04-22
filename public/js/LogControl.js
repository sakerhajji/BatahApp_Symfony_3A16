

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

