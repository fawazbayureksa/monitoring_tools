/**
 * Page: About
 * - Vanilla JS showcase carousel with autoplay, prev/next, dot indicators, and pause on hover
 */
(function () {
    'use strict';

    var AUTOPLAY_MS = 5000;

    var carousel = document.getElementById('aboutCarousel');
    if (!carousel) return;

    var slides = carousel.querySelectorAll('.about-carousel__slide');
    var dots = carousel.querySelectorAll('.about-carousel__dot');
    var prevBtn = document.getElementById('aboutCarouselPrev');
    var nextBtn = document.getElementById('aboutCarouselNext');

    if (slides.length <= 1) return;

    var currentIndex = 0;
    var timerId = null;

    function goToSlide(index) {
        currentIndex = (index + slides.length) % slides.length;
        for (var i = 0; i < slides.length; i++) {
            var active = i === currentIndex;
            slides[i].classList.toggle('is-active', active);
            if (dots[i]) {
                dots[i].classList.toggle('is-active', active);
                dots[i].setAttribute('aria-selected', active ? 'true' : 'false');
            }
        }
    }

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    function startAutoplay() {
        stopAutoplay();
        timerId = window.setInterval(nextSlide, AUTOPLAY_MS);
    }

    function stopAutoplay() {
        if (timerId !== null) {
            window.clearInterval(timerId);
            timerId = null;
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            prevSlide();
            startAutoplay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            nextSlide();
            startAutoplay();
        });
    }

    for (var i = 0; i < dots.length; i++) {
        (function (dotIndex) {
            dots[dotIndex].addEventListener('click', function () {
                goToSlide(dotIndex);
                startAutoplay();
            });
        })(i);
    }

    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);
    startAutoplay();
})();
