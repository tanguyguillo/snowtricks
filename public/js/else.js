$(document).ready(function () {
    var id
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


    // // // Supression des données
    // $("a[data-delete]").on("click", function (e) {
    //     e.preventDefault();
    //     var link = this;
    //     var token = this.getAttribute("data-token");

    //     // Affichage de la modale
    //     // $("body").append(
    //     //     '<div class="modal-confirm"><div><p>Etes-vous sur de vouloir supprimer cet article ?</p><div><button class="yes btn-validate">oui</button><button class="no btn-back">non</button></div></div></div>'
    //     // );
    // });

    // // Confirmation ... ncaught ReferenceError: token is not defined
    // $(".btn-validate").on("click", function () {
    //     $.ajax({
    //         type: "POST",
    //         url: "http://127.0.0.1:8000/tricks/delete-tricks/127",
    //         data: { "_token": this.dataset.token },
    //         success: function (response) {
    //             console.log(response);
    //         },
    //         error: function (error) {
    //             console.log(error);
    //         },
    //     });
    // });

    $(".btn-validate").on("click", function () {

        e.preventDefault();
        var link = this;
        var token = this.getAttribute("data-token");

        fetch(link.getAttribute("href"), {
            method: "DELETE",
            headers: {
                "X-requested-with": "XMLHttpRequest",
                "Content-type": "application/json",
            },
            body: JSON.stringify({ _token: this.dataset.token }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    console.log(data.success);
                    //$(".modal-confirm").remove();
                } else {
                    console.log(data.error);
                }
            })
            .catch((e) => console.log(e));
    });


});
