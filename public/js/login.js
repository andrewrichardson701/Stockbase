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