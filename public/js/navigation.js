"use strict";

// SLIDING NAVIGATION FUNCTION
const navSlide = () => {
    const burger = document.querySelector('.burger');
    const nav = document.querySelector('.container-nav');
    const navClose = document.querySelector('.nav-close');
    const navLinks = document.querySelectorAll('.list-item-nav');
    const closeNavigation = () => {
        nav.classList.toggle('nav-active');

        // ANIMATE lINKS
        navLinks.forEach((link, index) => {
            if (link.style.animation) {
                link.style.animation = '';
            } else {
                link.style.animation = `navLinkFade 1000ms linear both ${index / 7}s`;
            }
        });
    };
    const openNavigation = () => {
        nav.classList.toggle('nav-active');
        // ANIMATE lINKS
        navLinks.forEach((link, index) => {
            if (link.style.animation) {
                link.style.animation = '';
            } else {
                link.style.animation = `navLinkFade 1000ms linear both ${index / 7 - 0.15}s`;
            }
        });
        // ANIMATE BURGER
        burger.classList.toggle('toggle');
    };
    // TOGGLE NAV
    burger.addEventListener('click', () => {
        openNavigation();
    });
    // CLOSE NAVIGATION
    navClose.addEventListener('click', () => {
        closeNavigation();
    });
    document.addEventListener('keyup', (event) => {
        if (event.keyCode === 27) {
            closeNavigation();
        }
    });
};

// CALL FUNCTIONS
const app = () => {
    navSlide();
};

$(document).ready(function () {
    app();
});