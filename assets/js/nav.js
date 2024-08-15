// menu links
document.addEventListener('DOMContentLoaded', function () {
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelector('.nav-links');
    const navFloat = document.querySelector('.nav-float'); // floating div
    if (navMenu !== null) {
        navMenu.addEventListener('mouseover', function () {
            navLinks.classList.add('show');
        });
        navFloat.addEventListener('mouseover', function () {
            navLinks.classList.add('show');
        });
        navMenu.addEventListener('mouseout', function () {
            navLinks.classList.remove('show');
        });
        navFloat.addEventListener('mouseout', function () {
            navLinks.classList.remove('show');
        });

    }
});

//burger menu
document.addEventListener('DOMContentLoaded', function () {
    const burgerMenu = document.querySelector('.burger-menu');
    const burgerLinks = document.querySelector('.burger-links');

    burgerMenu.addEventListener('click', function () {
        burgerLinks.classList.toggle('show');
        if (burgerMenu.innerHTML == '<i class="fa-solid fa-bars"></i>') {
            burgerMenu.innerHTML = '<i class="fa-solid fa-bars fa-rotate-90"></i>';
        } else {
            burgerMenu.innerHTML = '<i class="fa-solid fa-bars"></i>';
        }
    });
});