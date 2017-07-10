{
    let files = [];

    $('#fileupload').change(function () {
        window.$element = $(this);
        handleFiles(this.files);
    });

    $('#fileupload-button').click(function () {
        let productId = $('#fileupload').data('product-id');
        let data = new FormData;

        data.append('_token', $('#fileupload').data('csrf-token'));

        files.forEach(function (file) {
            data.append('files[]', file);
        });

        $.ajax({
            url: `inventory/${productId}/images`,
            type: 'post',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
        }).done(function () {
            window.location.reload(false);
        }).fail(function (response) {
            let data = response.responseJSON;

            if (response.status === 422) {
                createValidationAlert(data, $('.images-validation-container'));
            }
            else {
                createAlert(data.type, data.message);
            }
        });
    });

    $('.lnk-delete-image').click(function (e) {
        e.preventDefault();
        let imageId = $(this).data('image-id');
        let productId = $(this).data('product-id');

        $.ajax({
            url: `inventory/${productId}/images/${imageId}`,
            type: 'delete',
            data: {_token: _token}
        }).done(() => {
            window.location.reload(false);
        });
    });

    function handleFiles($files) {
        for (let i = 0; i < $files.length; i++) {
            let file = $files[i];
            let imageType = /^image\//;

            if (!imageType.test(file.type)) {
                $files.splice(i, 1);
                i--;
                continue;
            }

            files.push(file);

            let reader = new FileReader();
            let $container = $('<div class="col-xs-12 m-b-1">');
            let $img = $('<img class="img-thumbnail" style="max-width: 200px;max-height: 200px;">');
            $img.prop('file', file);
            $container.append($img);

            reader.onload = function (e) {
                $img.prop('src', e.target.result);
                $('#files').append($container);
            };

            reader.readAsDataURL(file);
        }

        if ($files.length) {
            $('#fileupload-button').show();
        }
        else {
            $('#fileupload-button').hide();
        }
    }
}
