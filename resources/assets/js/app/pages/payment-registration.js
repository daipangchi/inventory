{
    let $page = $('.page-profile');

    $('.payment-types-container input').click(function () {
        $page.find('form.payment').hide();
        $('.form-' + this.value).show();
    });

    $('[id^=address_same]').click(function () {
        showCorrectForm(this);
    });

    $('input[name="payment_type"]').each(function () {
        if ($(this).prop('checked')) {
            showCorrectForm(this);
        }
    });

    if (!$('.payment-types-container input[checked]').length) {
        $('#type_ach').click();
    }

    function showCorrectForm(element) {
        let $this = $(element);
        let $billingAddressContainer = $('.billing-address-container');
        let addressIsSame = $this.prop('checked');

        if (addressIsSame) {
            $billingAddressContainer.find('input').each(function () {
                if ($(this).attr('type') === 'text') {
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
