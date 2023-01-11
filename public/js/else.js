$(document).ready(function () {

    // const myModal = document.getElementById('myModal')
    // const myInput = document.getElementById('myInput')

    // myModal.addEventListener('shown.bs.modal', () => {
    //     myInput.focus()
    // })

    var slug
    $('.btndelete').click(function () {
        slug = $(this).attr('id')
        console.log(slug); // OK
    });

    // $('#confirmDeleteTrick').click(function () {
    //     var pathDeleteTrick = "/tricks/delete-tricks/" + slug  //+ "?ajax=1"
    //     console.log(pathDeleteTrick); // OK

    //     jQuery.ajax({
    //         url: pathDeleteTrick,
    //         type: "POST",
    //         data: { slug: slug },
    //         success: function (data) {
    //             alert('ok');
    //         },
    //         error: function () {
    //             alert('mal');
    //         }
    //     });

});







});