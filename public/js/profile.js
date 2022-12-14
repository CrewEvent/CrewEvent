

$(document).ready(function () {


    var readURL = function (input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.profile-pic').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }


    $(".file-upload").on('change', function () {
        readURL(this);
    });

    $(".upload-button").on('click', function () {
        $(".file-upload").click();
    });

    $('.save_img').on('click', function () {
        var data = $('.file-upload').val();

        // $.ajax({
        //     url: './profile/photo/edit',
        //     data: data,
        //     success: function (response) {
        //         console.log(repsonse)
        //     },
        //     error: function (response) {
        //         console.log(response);
        //     }
        // });

        $('form.edit_img_form').submit();
    })
});