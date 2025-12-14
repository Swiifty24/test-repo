$(document).ready(function() {
    // Mobile menu toggle
    $('#mobileMenuBtn').click(function() {
        $(this).toggleClass('active');
        $('#mobileMenu').toggleClass('active');
    });

    // Close mobile menu when clicking a link
    $('#mobileMenu a').click(function() {
        $('#mobileMenuBtn').removeClass('active');
        $('#mobileMenu').removeClass('active');
    });

    // Smooth scroll
    $('a[href^="#"]').click(function(e) {
        var target = $(this.hash);
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 800);
        }
    });
});