{
    $('#csv-import-button').click(function () {
        $('#csv-input').click();
    });

    $('#csv-input').change(function () {
        let path = $(this).val();
        let fileNameIndex = (path.lastIndexOf('\\') + 1) || (path.lastIndexOf('/') + 1);
        let filename = path.substr(fileNameIndex);
        $('#selected-file').text('Selected file: ' + filename);
    });

    $('#csv-import-form').submit(function (e) {
        e.preventDefault();

        let formData = new FormData($(this)[0]);

        $.ajax({
            url: '/inventory/csv',  //Server script to process data
            type: 'POST',
            xhr: function () {  // Custom XMLHttpRequest
                let myXhr = $.ajaxSettings.xhr();

                if (myXhr.upload) { // Check if upload property exists
                    myXhr.upload.addEventListener('progress', function () {
                        // For handling the progress of the upload
                    }, false);
                }

                return myXhr;
            },
            // Ajax events
            success: function () {
                location.href = location.origin + '/inventory';
            },
            error: function () {
                //
            },
            // Form data
            data: formData,
            //Options to tell jQuery not to process data or worry about content-type.
            cache: false,
            contentType: false,
            processData: false
        });
    });

    let columns = [
        {data: 'category_id'},
        {data: 'path_by_name'},
    ];
}
