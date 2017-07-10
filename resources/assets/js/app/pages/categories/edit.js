{
    $('#delete-category-button').click(function () {
        let confirmed = confirm('Are you sure you want to do this? All child categories will be deleted.');

        if (confirmed) {
            $.ajax({
                url: '/categories/' + $(this).data('category-id'),
                type: 'delete',
                data: {_token: _token}
            }).done(function () {
                document.location.href = document.location.origin + '/categories';
            });
        }
    });
}
