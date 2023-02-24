$(document).ready(function () {
    let id
    let idPicture

    $(function () {
        $('a[data-confirm]').click(function (ev) {
            var href = $(this).attr('href');

            if (!$('#dataConfirmModal').length) {
                $('body').append('<div id="dataConfirmModal" class="modal" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="dataConfirmLabel">Merci de confirmer</h3></div><div class="modal-body"></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Non</button><a class="btn btn-danger" id="dataConfirmOK">Oui</a></div></div></div></div>');
            }
            $('#dataConfirmModal').find('.modal-body').text($(this).attr('data-confirm'));
            $('#dataConfirmOK').attr('href', href);
            $('#dataConfirmModal').modal({ show: true });

            return false;
        });
    });

    const img = document.createElement('img')

    $('.btndelete').click(function () {
        id = $(this).attr('id')
        $('.tricks-' + id).removeClass('displayNone').addClass('displayContents');
    });

    $('.close-modal').click(function () {
        $('.tricks-' + id).removeClass('displayContents').addClass('displayNone');
    });

    $("a[data-delete]").on("click", function (e) {
        e.preventDefault();
        $.ajax({
            type: "DELETE",
            url: "/tricks/delete-tricks/" + id,
            data: { "_token": this.dataset.token },
            success: function (response) {
                $('#deleteThisTrick').modal('toggle');
                $('.' + id).addClass('displayNone');
            },
            error: function (error) {
                $('#deleteThisTrick').modal('toggle');
            },
        });
    });

    $('.btnAdditionalDelete').click(function () {
        idPicture = $(this).attr('id')
    });

    $("a[data-additional-delete]").on("click", function (e) {
        e.preventDefault();

        $.ajax({
            type: "DELETE",
            url: "/tricks/delete-picture/" + idPicture,
            data: { "_token": this.dataset.token },
            success: function (response) {
                $('.general' + idPicture).addClass('displayNone');
                alert("Your picture have been deleted");
            },
            error: function (error) {
                alert("Your picture didn't have been deleted, try again");
            },
        });
    });

    $('.updateTrick').click(function () {
        console.log("4");
    });

    $('.close').click(function () {
        console.log("3");
        $(".alert").alert('close')
    });

    $('.pencilMainPicture').click(function () {
        console.log("2");
        $('.pencilMainPictureAction').removeClass('displayNone')
    });

    $('.pencilTitleAction').click(function () {
        console.log("pencilTitleAction");
        $('.titleAction').removeClass('displayNone')
    });

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
                alert("Your picture have been deleted");
                void window.location.reload()
            },
            error: function (error) {
                alert("Your picture didn't have been deleted, try again");
            },
        });
    });

});
