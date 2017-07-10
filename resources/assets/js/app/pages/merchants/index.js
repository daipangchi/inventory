{
    let $table = $('#merchants-table');

    $table.on('click', '.merchant-verify', function () {
        let $this = $(this);
        let merchantId = $this.data('merchant-id');
        let data = JSON.stringify({
            is_verified: !$this.data('is-verified'),
            _token: _token
        });

        $.ajax({
            url: `/merchants/${merchantId}`,
            type: 'patch',
            data: data,
            headers: {'Content-Type': 'application/json'},
            success: function (data) {
                window.location.reload();
            }
        })
    });

    $table.on('click', '.merchant-disable', function () {
        let $this = $(this);
        let merchantId = $this.data('merchant-id');
        let data = JSON.stringify({
            is_disabled: !$this.data('is-disabled'),
            _token: _token
        });

        $.ajax({
            url: `/merchants/${merchantId}`,
            type: 'patch',
            data: data,
            headers: {'Content-Type': 'application/json'},
            success: function (data) {
                window.location.reload();
            }
        })
    });

    $table.on('click', '.merchant-delete', function () {
        let confirmation = prompt('Are you sure you want to delete this merchant? type "yes" to confirm');

        if (confirmation === 'yes') {
            let $this = $(this);
            let merchantId = $this.data('merchant-id');

            $.ajax({
                url: `/merchants/${merchantId}`,
                type: 'delete',
                headers: {'X-CSRF-TOKEN': _token},
                success: function () {
                    window.location.reload();
                }
            });
        }
    });
}
