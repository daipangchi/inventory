{
    $('.delete-schedule-link').click(function (e) {
        e.preventDefault();

        $.ajax({
            url: this.href,
            method: 'delete',
            data: {_token: _token}
        });

        $(this).closest('.row').remove();
    });
}
