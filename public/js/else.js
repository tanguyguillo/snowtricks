$(document).ready(function () {
    var id
    var idPicture

    $('.btndelete').click(function () {
        id = $(this).attr('id')
        $('.' + id).removeClass('displayNone').addClass('displayContents');
    });

    $('.close-modal').click(function () {
        console.log(id); // OK
        $('.' + id).removeClass('displayContent').addClass('displayNone');
    });

    $('#deleteThisTrick').click(function () {
        $('.' + id).removeClass('displayContent').addClass('displayNone');
    });

    // go to controller main page
    $("a[data-delete]").on("click", function (e) {
        e.preventDefault();
        $.ajax({
            type: "DELETE",
            url: "/tricks/delete-tricks/" + id,
            data: { "_token": this.dataset.token },
            success: function (response) {
                console.log(response);
                $('#deleteThisTrick').modal('toggle');
                $('.' + id).addClass('displayNone');
            },
            error: function (error) {
                console.log(error);
                $('#deleteThisTrick').modal('toggle');
            },
        });
    });

    $('.btnAdditionalDelete').click(function () {
        idPicture = $(this).attr('id')
        $('.general' + idPicture).addClass('displayNone');  // visual + pencil + trash
    });

    //go to controller delete additional picture : error 500 
    $("a[data-additional-delete]").on("click", function (e) {
        console.log('passage data-additional-delete"');
        e.preventDefault();
        $.ajax({
            type: "DELETE",
            url: "/tricks/delete-picture/" + idPicture,
            data: { "_token": this.dataset.token },
            success: function (response) {
                console.log(response);
                alert("Your picture have been deleted");
            },
            error: function (error) {
                console.log(error);
                alert("Your picture didn't have been deleted, try again");
            },
        });
    });

    $('.updateTrick').click(function () {
        console.log('pass update'); // OK
    });

    // close message
    $('.close').click(function () {
        $(".alert").alert('close')
    });

    // form main picture
    $('.pencilMainPicture').click(function () {
        //console.log("pencilMainPicture");
        $('.pencilMainPictureAction').removeClass('displayNone')
    });

    //pencilTitleAction
    $('.pencilTitleAction').click(function () {
        console.log("pencilTitleAction");
        $('.titleAction').removeClass('displayNone')
    });



});
