{
    $('#button-add-attribute').click(function () {
        let $clone = $('.product-attributes-container .row.form-group').last().clone();
        let index = parseInt($clone.find('input').attr('name').replace(/^\D+/g, ''), 10) + 1;

        $clone.find('input[name$="[value]"]').val('').attr('name', `attributes[${index}][value]`);
        $clone.find('input[name$="[name]"]').val('').attr('name', `attributes[${index}][name]`);
        $('.product-attributes-container').append($clone);
        $('.button-remove-attribute').removeClass('hidden');

    });

    $('.product-attributes-container').on('click', '.button-remove-attribute', function () {
        let $formGroup = $(this).closest('.row');

        if ($('.product-attributes-container .row.form-group').length > 1) {
            $formGroup.remove();
        }
        else {
            // hide so we can continue using it as a template. When adding a new attribute
            $formGroup.find('input').val('');
            $('.button-remove-attribute').addClass('hidden');
        }
    });

    let specsIndex = $('.specifications-container .row.spec').length;

    $('#add-new-spec').click(function () {
        let index = ++specsIndex;
        let $clone = $('#specs-input-template').clone().show();

        $clone.removeAttr('id');
        $clone.find('[name]').each(function () {
            $(this).attr('name', $(this).attr('name').replace('INDEX', index));
        });
        $clone.find('select').attr('data-role', 'tagsinput').tagsinput('refresh');

        $('.specifications-container').append($clone);
    });

    $('.button-remove-spec').click(function () {
        if ($('.specifications-container').children('row').length == 1) {
            $(this).closest('.row').remove();
        }
        else {
            $(this).closest('.row').remove();
        }
    });

    $('#product-categories-form').submit(function (e) {
        e.preventDefault();

        let checked = $('#product-categories').jstree().get_checked();
        let categoryIds = [];

        checked.forEach(function (item) {
            categoryIds.push($(`#${item}`).data('category-id'));
        });

        $.ajax({
            url: $(this).prop('action'),
            type: 'patch',
            data: {
                _token: _token,
                categoryIds: categoryIds
            }
        }).done(function () {
            window.location.reload();
        });
    });
}
