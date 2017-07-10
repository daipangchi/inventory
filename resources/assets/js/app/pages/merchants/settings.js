{
    let $page = $('.page-merchants-settings');
    let checked = $('.payment-type-container').find('input[checked]')[0];

    $page.find('form').hide();

    if (checked) {
        $('.form-' + checked.value).show();
    }

    $('.payment-type-container input').click(function () {
        $page.find('form').hide();
        $('.form-' + this.value).show();
    });

    $('#address_same').click(function () {
        showCorrectForm(this);
    });

    $('input[name="payment_type"]').each(function () {
        if ($(this).prop('checked')) {
            showCorrectForm(this);
        }
    });

    $('#link-connect-ebay').click(function (e) {
        let $form = $('form.ebay');

        $.ajax({
            url: $form.prop('action'),
            type: 'patch',
            data: $form.serialize()
        }).done(function () {
            $.get('/settings/ebay/get_ebay_auth_url').done(function (response) {
                document.location.href = response;
            });
        }).fail(function (response) {
            createValidationAlert(response.responseJSON, $('.ebay-validations-container'));
        });

        e.preventDefault();
        e.stopPropagation();
    });

    $('#deduction-form').submit(function (e) {
        let $form = $(this);
        e.preventDefault();

        $('.deductions-validations-container').empty();

        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: $form.serialize(),
        }).done(function (response) {
            let $row = $('.deduction-template').clone().removeClass('hidden deduction-template');
            $row.children('.deduction-from').append(response.data.from_weight + ' lbs');
            $row.children('.deduction-to').append(response.data.to_weight + ' lbs');
            $row.children('.deduction-amount').append('$' + response.data.amount);

            $('#deductions-table').removeClass('hidden').find('tbody').append($row);
            $form.find('input[type="number"]').val('');
            $form.find('input[type="number"]').first().focus();
        }).error(function (response) {
            createValidationAlert(response.responseJSON, $('.deductions-validations-container'));
        });
    });

    $('#deductions-table').on('click', '.link-delete-deduction', function (e) {
        e.preventDefault();

        let $anchor = $(this);
        let id = $anchor.data('deduction-id');

        $.ajax({
            url: '/settings/deductions',
            type: 'delete',
            data: {
                _token: _token,
                id: id
            },
            dataType: "json",
        }).done(function () {
            $anchor.closest('tr').remove();
        });
    });

    $('#button-disconnect-amazon, #button-disconnect-ebay').click(function () {
        let channel = $(this).data('channel');
        swal({
            title: `Are you sure you want to disconnect your ${channel} account?`,
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Disconnect",
            cancelButtonText: "No, do not disconnect.",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (confirmed) {
            if (confirmed) {
                $.ajax({
                    url: '/settings/disconnect',
                    type: 'delete',
                    data: {channel: channel, _token: _token}
                }).done(function () {
                    document.location.reload();
                });
            } else {
            }
        });
    });

    function showCorrectForm(element) {
        let $element = $(element);
        let $billingAddressContainer = $('.billing-address-container');
        let addressIsSame = $element.prop('checked');

        if (addressIsSame) {
            $billingAddressContainer.find('input').each(function () {
                if ($(element).attr('type') === 'text') {
                    this.value = '';
                }
            });

            $billingAddressContainer.hide();
        }
        else {
            $billingAddressContainer.show();
        }
    }
}
