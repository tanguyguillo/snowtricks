//

// // Supression des données
// $("a[data-delete]").on("click", function (e) {
//     e.preventDefault();
//     var link = this;
//     var token = this.getAttribute("data-token");

//     // Affichage de la modale
//     $("body").append(
//         '<div class="modal-confirm"><div><p>Etes-vous sur de vouloir supprimer cet article ?</p><div><button class="yes btn-validate">oui</button><button class="no btn-back">non</button></div></div></div>'
//     );

//     // Confirmation
//     $(".btn-validate").on("click", function () {
//         $.ajax({
//             type: "GET",
//             url: "/admin/blog/delete/126",
//             data: { _token: token },
//             success: function (response) {
//                 console.log(response);
//             },
//             error: function (error) {
//                 console.log(error);
//             },
//         });
//     });