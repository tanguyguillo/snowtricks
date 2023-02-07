$(document).ready(function () {
    var id
    var idPicture

    // when we clic on delete
    $('.btndelete').click(function () {
        id = $(this).attr('id')
        console.log("8");
        //button yes
        $('.tricks-' + id).removeClass('displayNone').addClass('displayContents');
    });

    $('.close-modal').click(function () {
        console.log(id); // OK
        console.log("7");
        // $('.' + id).removeClass('displayContent').addClass('displayNone');
    });

    // $('#deleteThisTrick').click(function () {
    //     console.log("6");
    //     // $('.' + id).removeClass('displayContent').addClass('displayNone');
    // });

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
                //hide the modal button
                $('.tricks-' + id).removeClass('displayContents').addClass('displayNone');
                $('.' + id).addClass('displayNone');
            },
            error: function (error) {
                console.log(error);
                //hide the modal button
                $('#deleteThisTrick').modal('toggle');
            },
        });
    });

    $('.btnAdditionalDelete').click(function () {
        console.log("5");
        idPicture = $(this).attr('id')
        $('.general' + idPicture).addClass('displayNone');  // visual + pencil + trash
    });

    //go to controller delete additional picture : 
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



});
