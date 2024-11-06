const delay = ms => new Promise(res => setTimeout(res, ms));

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        login(); // Call the login function
    });
});

document.addEventListener('keydown', function(event) {
    var target = event.target;
    if (target && target.id === 'otp_code' && event.key === 'Enter') {
        // Prevent the default action if needed
        event.preventDefault();

        // Call the desired function
        checkotp();
    }
});

function jsInfo(info, color) { // used to type in the hidden p element on the login page.
    var p = document.getElementById('js-info');

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "includes/responsehandling.inc.php?error="+info+"&ajax=1", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var output = xhr.responseText;
            var infoText = output;
            p.style.color = color;
            p.style.display = 'block';
            p.innerText = infoText;
        } else {
            p.style.color = color;
            p.style.display = 'block';
            p.innerText = info;
        }
    };
    xhr.send();
    
    
}

async function otpLogin(user_id, redirect_url) {
    var csrf_token = document.getElementById('csrf_token').value;
    $.ajax({
        type: "POST",
        url: "includes/login.inc.php",
        data: {
            submit_final: 1,
            user_id: user_id,
            redirect_url: redirect_url,
            csrf_token: csrf_token
        },
        dataType: "json",
        success: async function(response){
            console.log(response);
            window.location.href = redirect_url;
            // does the thing.
        },
        error: function(response) {
            console.log('error');
            console.log(response);
        },
        async: true
    });
}

async function checkotp() {
    var body = document.getElementById("body");
    var otp = document.getElementById("otp_code").value;
    var redirect_url = document.getElementById("redirect_url").value;
    var statusP = document.getElementById("status_info");
    var bypass_2fa = document.getElementById("bypass_2fa").checked;

    if (redirect_url == '' || redirect_url == null) {
        redirect_url = 'index.php?login=success';
    }

    $.ajax({
        type: "POST",
        url: "includes/2fa.inc.php",
        data: {
            checkotp: 1,
            otp: otp,
            bypass_2fa: bypass_2fa
        },
        dataType: "json",
        success: async function(response){
            if (response["status"] == "true") {
                statusP.style.display = "block";
                statusP.innerText = response["data"];
                if (response['data'].includes('successful') && response.hasOwnProperty('user_id')) {
                    var user_id = response['user_id'];
                    statusP.innerText += '. Redirecting...';
                    await delay(1500);
                    // do final ajax
                    otpLogin(user_id, redirect_url);
                }
            } else {
                console.log(response['status']);
                console.log(response['data']);
            }
        },
        error: function(response) {
            console.log('error');
            console.log(response);
        },
        async: false
    });
}

function load2fa(user_id, redirect_url) {
    var body = document.getElementById('body');

    $.ajax({
        type: "POST",
        url: "includes/2fa.inc.php",
        data: {
            make2fa: 1,
            user_id: user_id,
            redirect_url: redirect_url
        },
        dataType: "json",
        success: function(response){
            // do something with redirect_url to put it on the page.
            if (response['status'] == 'true') {
                body.innerHTML += response['data'];
            }
        },
        error: function(response) {
            console.log(response);
        },
        async: true // <- this turns it into synchronous
    });
}

function login() {
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;
    var csrf_token = document.getElementById('csrf_token').value;
    var local = document.getElementById('local-toggle').checked;
    var submit = document.getElementById('submit').value;

    $.ajax({
        type: "POST",
        url: "includes/login.inc.php",
        data: {
            csrf_token: csrf_token,
            submit: submit,
            username: username,
            password: password,
            local: local
        },
        dataType: "json",
        success: function(response){
            if (response.hasOwnProperty('login') && response['login'] == 'success') {
                if (response.hasOwnProperty('user_id')) { // check if user_id is set
                    var user_id = response['user_id'];
                    if (response.hasOwnProperty('redirect_url')) {
                        var redirect_url = response['redirect_url'];
                    } else {
                        var redirect_url = 'index.php?login=success';
                    }
                    if (response.hasOwnProperty('2fa') && response['2fa'] == true) {
                        if (response['2fa_set'] == true) {
                            askFor2FA(user_id, redirect_url);
                        } else {
                            load2fa(user_id, redirect_url);
                        }
                        
                    } else if(response.hasOwnProperty('2fa') && response['2fa'] == false) {
                        window.location.href = redirect_url;
                    }
                    
                } else {
                    jsInfo('No User ID found...', 'red');
                }
            } else if (response.hasOwnProperty('error')) {
                jsInfo(response['error'], 'red');
            } else if (response.hasOwnProperty('sqlerror')) {
                jsInfo(response['sqlerror'], 'red');
            }
        },
        error: function(response) {
            console.log('error');
            console.log(response);
        },
        async: true // <- this turns it into synchronous
    });
}       

function askFor2FA(user_id, redirect_url) {
    //makeotp
    var body = document.getElementById('body');

    $.ajax({
        type: "POST",
        url: "includes/2fa.inc.php",
        data: {
            makeotp: 1,
            user_id: user_id,
            redirect_url: redirect_url
        },
        dataType: "json",
        success: function(response){
            // do something with redirect_url to put it on the page.
            if (response['status'] == 'true') {
                body.innerHTML += response['data'];
            }
        },
        error: function(response) {
            console.log(response);
        },
        async: true // <- this turns it into synchronous
    });

}



var toggle = document.getElementById("local-toggle");
var reset = document.getElementById("password-reset");
if (toggle.checked) {
    reset.hidden=false;
} else {
    reset.hidden=true;
}

toggle.addEventListener('change', (event) => {
    var reset = document.getElementById("password-reset");
    if (event.currentTarget.checked) {
        reset.hidden=false;
    } else {
        reset.hidden=true;
    }
})

function modalLoadSwipe() {
    var modal = document.getElementById("modalDivSwipe");
    modal.style.display = "block";
    modal.hidden = false;
}

// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseSwipe = function() { 
    var modal = document.getElementById("modalDivSwipe");
    modal.style.display = "none";
}

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