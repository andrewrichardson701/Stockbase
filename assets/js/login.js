const delay = ms => new Promise(res => setTimeout(res, ms));

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        login(); // Call the login function
    });
});

function jsInfo(info, color) { // used to type in the hidden p element on the login page.
    var p = document.getElementById('js-info');
    
    p.style.color = color;
    p.style.display = 'block';
    p.innerText = info;
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
    var accountName = document.getElementById("account_name").value;
    var redirect_url = document.getElementById("redirect_url").value;
    var statusP = document.getElementById("status_info");
    var secret = document.getElementById("otp_secret").value;
    var user_id = document.getElementById("user_id").value;

    if (redirect_url == '' || redirect_url == null) {
        redirect_url = 'index.php?login=success';
    }

    $.ajax({
        type: "POST",
        url: "includes/2fa.inc.php",
        data: {
            checkotp: 1,
            accountName: accountName,
            user_id: user_id,
            otp: otp,
            secret: secret
        },
        dataType: "json",
        success: async function(response){
            if (response["status"] == "true") {
                statusP.style.display = "block";
                statusP.innerText = response["data"];
                if (response['data'].includes('successful')) {
                    statusP.innerText += '. Redirecting...';
                    await delay(1500);
                    // do final ajax
                    otpLogin(user_id, redirect_url);
                }
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

