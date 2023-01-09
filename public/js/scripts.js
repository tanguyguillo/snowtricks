/*!
    * Start Bootstrap - Freelancer v6.0.0 (https://startbootstrap.com/themes/freelancer)
    * Copyright 2013-2020 Start Bootstrap
    * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap-freelancer/blob/master/LICENSE)
    */
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


    // read more fc
    // $(function () {
    // items to show
    var increment = 15;
    var startFilter = 0;
    var endFilter = increment;

    // item selector
    var $this = $('.items');

    var elementLength = $this.find('div').length;
    $('.listLength').text(elementLength);

    // show/hide the Load More button
    if (elementLength > 15) {
        $('.buttonToogle').show();
    }

    $('.items .item').slice(startFilter, endFilter).addClass('shown');
    $('.shownLength').text(endFilter);
    $('.items .item').not('.shown').hide();
    $('.buttonToogle .showMore').on('click', function () {
        if (elementLength > endFilter) {
            startFilter += increment;
            endFilter += increment;
            $('.items .item').slice(startFilter, endFilter).not('.shown').addClass('shown').toggle(500);
            $('.shownLength').text((endFilter > elementLength) ? elementLength : endFilter);
            if (elementLength <= endFilter) {
                $(this).remove();
            }
        }
    });

    // });

    // Closes responsive menu when a scroll trigger link is clicked
    $('.js-scroll-trigger').click(function () {
        $('.navbar-collapse').collapse('hide');
    });

    // Image to Lightbox Overlay
    // $('#slot img').attr('class')... to see later
    $('img').on('click', function () {
        $('#overlay')
            .css({ backgroundImage: `url(${this.src})` })
            .addClass('open')
            .one('click', function () { $(this).removeClass('open'); });
    });

    // Activate scrollspy to add active class to navbar items on scroll
    $('body').scrollspy({
        target: '#mainNav',
        offset: 80
    });

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

    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })


    // console.log("yep");


    //var theHREF;

    // $(".confirmModalLink").click(function (e) {

    //     e.preventDefault();
    //     // theHREF = $(this).attr("href");
    //     $("#confirmModal").modal("show");
    // });

    // $("#confirmModalNo").click(function (e) {
    //     $("#confirmModal").modal("hide");
    // });

    // $("#confirmModalYes").click(function (e) {
    //     window.location.href = theHREF;
    // });













})(jQuery); // End of use strict
