// show/hide the password on login screen
function togglePassword(eye, passID) {
    var password = document.getElementById(passID);
    if (password.type == 'password') {
        password.type = 'text';
        if (eye.classList.contains('fa-eye')) {
            eye.classList.remove('fa-eye');
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


var validatePassword = 0;
var validatePasswordConfirm = 0;
var validateUsername = 0;
var validateEmail = 0;
var validateFirstname = 0;
var validateLastname = 0;

function checkCredentials(field, type) {
    var data = field.value;
    var csrf_token = document.getElementById('csrf_token').value;
    var ajax_needed = 0;
    var submit = document.getElementById('submit');
    var meter = document.getElementById('password-strength-meter');

    if (type == 'password') {
        var passwordMeter = document.getElementById('password-strength-meter');
        ajax_needed = 0;
        var check = checkPasswordStrength(data);
        meter.classList = '';
        switch(check) {
            case 1: 
                passwordMeter.value = 2;
                break;
            case 2: 
                passwordMeter.value = 4;
                break;
            case 3: 
                passwordMeter.value = 6;
                break;
            case 4: 
                passwordMeter.value = 8;
                break;
            case 5: 
                passwordMeter.value = 10;
                break;
            default:
                passwordMeter.value = 0;
        }
        if (check == 5) {
            validatePassword = 1;
            document.getElementById(type+'-check').hidden = false;
        } else {
            validatePassword = 0;
            document.getElementById(type+'-check').hidden = true;
        }
    } else if (type == 'password-confirm') {
        ajax_needed = 0;
        var password = document.getElementById('password').value;
        if (password === data && password!=="") {
            // password matches
            validatePasswordConfirm = 1;
            document.getElementById(type+'-check').hidden = false;
            document.getElementById(type+'-error').hidden = true;
            document.getElementById(type+'-success').hidden = false;
        } else {
            validatePasswordConfirm = 0;
            document.getElementById(type+'-check').hidden = true;
            document.getElementById(type+'-error').hidden = false;
            document.getElementById(type+'-success').hidden = true;
        }
    } else if (type == 'username') {
        ajax_needed = 1;
    } else if (type == 'email') {
        ajax_needed = 1;
    } else if (type == 'firstname') {
        ajax_needed = 0;
        if (data.length >= 3) {
            validateFirstname = 1
            document.getElementById(type+'-check').hidden = false;
        } else {
            validateFirstname = 0;
            document.getElementById(type+'-check').hidden = true;
        }
    } else if (type == 'lastname' || type == 'lastname') {
        ajax_needed = 0;
        if (data.length >= 3) {
            validateLastname = 1
            document.getElementById(type+'-check').hidden = false;
        } else {
            validateLastname = 0;
            document.getElementById(type+'-check').hidden = true;
        }
    }
    if (ajax_needed == 1) {
        $.ajax({
            type: "POST",
            url: "includes/credentials.inc.php",
            data: {
                credentials_check: 1,
                type: type,
                data: data,
                csrf_token: csrf_token
            },
            dataType: "json",
            success: function(response){
                if (response.match == 1) {
                    document.getElementById(type+'-error').hidden = false;
                    if (type == "username"){
                        validateUsername = 0;
                    }
                    if (type == "email") {
                        validateEmail = 0;
                    }
                    document.getElementById(type+'-check').hidden = true;
                } else {
                    
                    if (type == "username"){
                        if (data.length >= 3) {
                            document.getElementById(type+'-error').hidden = true;
                            document.getElementById(type+'-check').hidden = false;
                            validateUsername = 1;
                        } else {
                            document.getElementById(type+'-error').hidden = false;
                            validateUsername = 0;
                        }   
                    }
                    if (type == "email") {
                        var isEmailValid = (email) => {
                            return String(email)
                                .toLowerCase()
                                .match(
                                    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                                );
                        };
                        
                        var emailIsValid = isEmailValid(data);
                        
                        if (emailIsValid) {
                            validateEmail = 1;
                            document.getElementById(type + '-error').hidden = true;
                            document.getElementById(type + '-check').hidden = false;
                        } else {
                            validateEmail = 0;
                            document.getElementById(type + '-error').hidden = true;
                            document.getElementById(type + '-check').hidden = true;
                        }
                        
                    }
                    
                }
            },
            error: function(response) {
                console.log(response);
            },
            async: false // <- this turns it into synchronous
        });
       
    }
    if (validatePassword == 1 && validatePasswordConfirm == 1 && validateEmail == 1 && validateUsername == 1 && validateFirstname == 1 && validateLastname == 1) {
        console.log('Credentials OK');
        submit.disabled = false;
    } else {
        // var score = validatePassword+validatePasswordConfirm+validateEmail+validateUsername;
        // console.log('Credentials issue '+score);
        submit.disabled = true;
    }

}

function checkPasswordStrength(password) {
    // Regular expressions for various strength conditions
    const minLength = 8; // Minimum length
    const uppercaseRegex = /[A-Z]/; // At least one uppercase letter
    const lowercaseRegex = /[a-z]/; // At least one lowercase letter
    const numberRegex = /[0-9]/; // At least one number
    const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/; // At least one special character
    
    let strength = 0;

    // Check for each condition and increment strength accordingly
    if (password.length >= minLength) strength++;
    if (uppercaseRegex.test(password)) strength++;
    if (lowercaseRegex.test(password)) strength++;
    if (numberRegex.test(password)) strength++;
    if (specialCharRegex.test(password)) strength++;

    // Determine strength level based on how many conditions are met
    let strengthLevel = "";
    if (strength === 5) {
        strengthLevel = "Strong";
    } else if (strength === 4) {
        strengthLevel = "Medium";
    } else if (strength >= 2) {
        strengthLevel = "Weak";
    } else {
        strengthLevel = "Very Weak";
    }

    return strength;
}