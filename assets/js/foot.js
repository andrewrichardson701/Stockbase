// SCROLL TO TOP SECTION

//Get the button
var mybutton = document.getElementById("scrollTop");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {
    scrollFunction()
};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        mybutton.className = "viewTranslate";
        document.getElementById("scrollTop").style.width = "max-content";
    } else {
        mybutton.className = "hideTranslate";
        document.getElementById("scrollTop").style.width = "38px";
        document.activeElement.blur();
    }
}
// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    window.scrollTo({top: 0, behavior: 'smooth'});
}


// script to show the rollover box when hovering the version number in the bottom right corner
var popupBoxOwner = document.getElementById('version-about');
var popupBox = document.getElementById('version-check');

if (popupBox !== null) {
    popupBoxOwner.addEventListener('mouseenter', () => {
        popupBox.style.opacity = '1';
        popupBox.style.visibility = 'visible';
    });

    popupBoxOwner.addEventListener('mouseleave', () => {
        popupBox.style.opacity = '0';
        popupBox.style.visibility = 'hidden';
    });
}
//