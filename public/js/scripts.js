/*!
    * Start Bootstrap - Freelancer v6.0.0 (https://startbootstrap.com/themes/freelancer)
    * Copyright 2013-2020 Start Bootstrap
    * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap-freelancer/blob/master/LICENSE)
    */

// document.querySelector("html").classList.add('js');

// var fileInput = document.querySelector(".input-file"),
//     button = document.querySelector(".input-file-trigger"),
//     the_return = document.querySelector(".file-return");

// button.addEventListener("keydown", function (event) {
//     if (event.keyCode == 13 || event.keyCode == 32) {
//         console.log("1");
//         fileInput.focus();
//     }
// });
// button.addEventListener("click", function (event) {
//     fileInput.focus();
//     console.log("1");
//     return false;
// });
// fileInput.addEventListener("change", function (event) {
//     console.log("1");
//     the_return.innerHTML = this.value;
// });


(function ($) {
    "use strict"; // Start of use strict

    // Smooth scrolling using jQuery easing
    $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function () {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: (target.offset().top - 71)
                }, 1000, "easeInOutExpo");
                return false;
            }
        }
    });

    // Scroll to top button appear
    $(document).scroll(function () {
        var scrollDistance = $(this).scrollTop();
        if (scrollDistance > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });

    // Closes responsive menu when a scroll trigger link is clicked
    $('.js-scroll-trigger').click(function () {
        $('.navbar-collapse').collapse('hide');
    });

    // Image to Lightbox Overlay
    $('img').on('click', function () {
        $('#overlay')
            .css({ backgroundImage: `url(${this.src})` })
            .addClass('open')
            .one('click', function () { $(this).removeClass('open'); });
    });

    // Activate scrollspy to add active class to navbar items on scroll
    // $('body').scrollSpy({
    //     target: '#mainNav',
    //     offset: 80
    // });

    // var scrollSpy = new bootstrap.ScrollSpy(document.body, {
    //     target: '#mainNav',
    //     offset: 80
    // })

    // Collapse Navbar  // not used....
    var navbarCollapse = function () {
        if ($("#mainNav").offset().top > 100) {
            $("#mainNav").addClass("navbar-shrink");
        } else {
            $("#mainNav").removeClass("navbar-shrink");
        }
    };
    // Collapse now if page is not at top
    navbarCollapse();
    // Collapse the navbar when page is scrolled
    $(window).scroll(navbarCollapse);

    // Floating label headings for the contact form
    $(function () {
        $("body").on("input propertychange", ".floating-label-form-group", function (e) {
            $(this).toggleClass("floating-label-form-group-with-value", !!$(e.target).val());
        }).on("focus", ".floating-label-form-group", function () {
            $(this).addClass("floating-label-form-group-with-focus");
        }).on("blur", ".floating-label-form-group", function () {
            $(this).removeClass("floating-label-form-group-with-focus");
        });
    });




})(jQuery); // End of use strict
