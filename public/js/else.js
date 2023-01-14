$(document).ready(function () {
    var id

    $('.btndelete').click(function () {
        console.log("1");
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

    // go to controler
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


});
