$(document).ready(function () {
    let id
    let idPicture

    //picture X
    const img = document.createElement('img')
    // img.src = "assets/img/background/empty.png"

    // fonctionnal
    // $('.collapseMain').click(function () {
    //     console.log('click collapseMain')
    //     var anchor = $(this).attr("href");
    //     $("html, body").animate({
    //         scrollTop: $(anchor).offset().top
    //     }, "slow");
    // });



    // when we clicK on delete
    $('.btndelete').click(function () {
        console.log('yes-modal')
        id = $(this).attr('id')
        //button yes
        $('.tricks-' + id).removeClass('displayNone').addClass('displayContents');
    });

    $('.close-modal').click(function () {
        console.log("close"); // OK
    });

    //  main page delete ajax
    $("a[data-delete]").on("click", function (e) {
        console.log('pass' + id)
        console.log('ajax data-delete')
        e.preventDefault();
        $.ajax({
            type: "DELETE",
            url: "/tricks/delete-tricks/" + id,
            data: { "_token": this.dataset.token },
            success: function (response) {
                console.log(response);
                $('#deleteThisTrick').modal('toggle');
                $('.' + id).addClass('displayNone');

                // var visual = "<img src=\"assets/img/tricks/.jpg\empty.png />";
                // $(visual).replaceAll(".empty");

            },
            error: function (error) {
                console.log(error);
                //hide the modal button
                $('#deleteThisTrick').modal('toggle');
            },
        });
    });

    // $.fn.missingImg = function (options) {
    //     var config = {
    //         'source': 'assets/img/tricks/empty.png',
    //         'alt': 'Image not found'
    //     };
    //     if (options) { $.extend(config, options); }
    //     $(this).each(function () {
    //         var $this = $(this),
    //             oImg = this, // original DOM element
    //             testImg = new Image(); // new DOM element

    //         $(testImg).error(function () {
    //             $this.attr({ src: config.source, alt: config.alt });
    //         });

    //         testImg.src = oImg.src; // Reload image
    //     });



    $('.btnAdditionalDelete').click(function () {
        console.log("5");
        idPicture = $(this).attr('id')
        $('.general' + idPicture).addClass('displayNone');  // visual + pencil + trash
    });

    //go to controller delete additional picture : 
    $("a[data-additional-delete]").on("click", function (e) {
        e.preventDefault();
        $.ajax({
            type: "DELETE",
            url: "/tricks/delete-picture/" + idPicture,
            data: { "_token": this.dataset.token },
            success: function (response) {
                alert("Your picture have been deleted");
            },
            error: function (error) {
                alert("Your picture didn't have been deleted, try again");
            },
        });
    });

    $('.updateTrick').click(function () {
        console.log("4");
        console.log('pass update'); // OK
    });

    // close message
    $('.close').click(function () {
        console.log("3");
        $(".alert").alert('close')
    });

    // form main picture
    $('.pencilMainPicture').click(function () {
        console.log("2");
        $('.pencilMainPictureAction').removeClass('displayNone')
    });

    //pencilTitleAction
    $('.pencilTitleAction').click(function () {
        console.log("pencilTitleAction");
        $('.titleAction').removeClass('displayNone')
    });

    //delete main picture on detail page
    $("a[data-main-delete]").on("click", function (e) {
        console.log("passage data-main-delete")
        e.preventDefault();
        id = $(this).attr('id')
        id = id.substring(1);
        $.ajax({
            type: "DELETE",
            url: "/tricks/delete-main-picture-only/" + id,
            data: { "_token": this.dataset.token },
            success: function (response) {
                // document.getElementById('embedPicture').appendChild(img)
                alert("Your picture have been deleted");
                void window.location.reload()
            },
            error: function (error) {
                alert("Your picture didn't have been deleted, try again");
            },
        });
    });

    // $('#deleteThisTrick').click(function () {
    //     console.log("6");
    //     $('.' + id).removeClass('displayContent').addClass('displayNone');
    // });





});
