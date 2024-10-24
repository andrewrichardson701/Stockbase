// swipe card reader bit - should now be unused
$(document).ready(function() {
    $(document).keypress(function(event) {
        var swipeModal = document.getElementById('modalDivSwipe');
            if (swipeModal.display === 'block') {
            // Assuming the card input triggers a keypress event
            var cardData = String.fromCharCode(event.which);
            var cardData_input = document.getElementById('cardData');
            var cardModifyForm = document.getElementById('cardModifyForm');
            cardData_input.value = cardData;
            cardModifyForm.submit();
        }
    });
});

// #############

// modals
function modalLoadReset2FA(id) {
    var modal = document.getElementById("modalDivReset2FA");
    modal.style.display = "block";
    var user_id_element = document.getElementById('2fareset_user_id');
    user_id_element.value = id;
}
function modalCloseReset2FA() { 
    var modal = document.getElementById("modalDivReset2FA");
    modal.style.display = "none";
    var user_id_element = document.getElementById('2fareset_user_id');
    user_id_element.value = '';
}
function modalLoadSwipe(type, card) {
    var modal = document.getElementById("modalDivSwipe");
    var cardTypeInput = document.getElementById('cardType');
    var cardCardInput = document.getElementById('cardCard');
    var cardHead = document.getElementById('cardHead');
    modal.style.display = "block";
    cardTypeInput.value = type;
    cardCardInput.value = card;
    if (type == 'assign') {
        cardHead.innerText = 'Assign Swipe Card '+card;
    } else {
        cardHead.innerText = 'Re-assign Swipe Card '+card;
    }
}
// When the user clicks on <span> (x), close the modal or if they click the image.
modalCloseSwipe = function() { 
    var modal = document.getElementById("modalDivSwipe");
    var cardTypeInput = document.getElementById('cardType');
    var cardCardInput = document.getElementById('cardCard');
    var cardHead = document.getElementById('cardHead');
    modal.style.display = "none";
    cardTypeInput.value = '';
    cardCardInput.value = '';
    cardHead.innerText = '';
}
function modalLoadLoginHistory() {
    var modal = document.getElementById("modalDivLoginHistory");
    modal.style.display = "block";
}
// When the user clicks on <span> (x), close the modal or if they click the image.
function modalCloseLoginHistory() { 
    var modal = document.getElementById("modalDivLoginHistory");
    modal.style.display = "none";
}

//#################

function changeTheme() {
    var select = document.getElementById('theme-select');
    var value = select.value;
    var css = document.getElementById('theme-css');
    var profile_id = document.getElementById('profile-id').value;
    var theme = document.getElementById('theme-select-option-'+value).title;
    var theme_name = document.getElementById('theme-select-option-'+value).alt;
    // css.href = "./assets/css/theme-"+theme+".css";


    var xhr = new XMLHttpRequest();
        xhr.open("GET", "includes/change-theme.inc.php?change=1&theme_file_name="+theme+"&value="+value+"&theme_name="+theme_name+"&user-id="+profile_id, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Parse the response and populate the shelf select box
                var re = JSON.parse(xhr.responseText);
                if (re == 'success') {
                    css.href = './assets/css/'+theme;
                } 
            }
        };
        xhr.send();
}