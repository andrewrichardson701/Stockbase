// show/hide the password on login screen
function togglePassword(eye, passID) {
    var password = document.getElementById(passID);
    if (password.type == 'password') {
        password.type = 'text';
        if (eye.classList.contains('fa-eye')) {
            eye.classList.remove('fa-eye');
            console.log(eye.classList);
        }
        if (!eye.classList.contains('fa-eye-slash')) {
            eye.classList.add('fa-eye-slash');
        }
    } else {
        password.type = 'password';
        if (!eye.classList.contains('fa-eye')) {
            eye.classList.add('fa-eye');
        }
        if (eye.classList.contains('fa-eye-slash')) {
            eye.classList.remove('fa-eye-slash');
        }
    }
}

