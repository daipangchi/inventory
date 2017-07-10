{
    let $selected = null;
    let selectedName = null;
    let selectedId = null;
    let selectedLevel = null;
    let priceChangesIndex = 0;
    let removeParentAnchor = '<a id="remove-parent-button" class="text-danger" style="display: inline;"><i class="glyphicon glyphicon-remove"></i>Remove</a>';

    $('#categories').jstree({
        types: {
            default: {
                icon: 'glyphicon glyphicon-folder-close text-info'
            },
            demo: {
                icon: 'glyphicon glyphicon-ok'
            }
        },
        plugins: ['types']
    }).show();

    $('#categories [role=treeitem]').click(function (e) {
        let $container = $(e.target).closest('[role=treeitem]');
        $selected = $(e.target);

        selectedName = $container.data('category-name');
        selectedId = $container.data('category-id');
        selectedLevel = parseInt($container.data('category-level'), 10) + 1;

        $('#edit-selected-button').html(`Edit <strong>${escapeHtml(selectedName)}</strong>`).show();
        $('#category-parent').html(escapeHtml(selectedName) + '<br>' + removeParentAnchor);
        $('#category-parent-input').val(selectedId).data('level', selectedLevel);
        $('#remove-parent-button').show();
    });

    $('#edit-selected-button').click(function () {
        if (selectedId) {
            window.location.href = window.location.origin + '/categories/' + selectedId + '/edit';
        }
    });

    $('.page-categories-index').on('click', '#remove-parent-button', function () {
        $('#category-parent').text('No parent category selected.');
        $('#category-parent-input').val(selectedId).data('level', selectedLevel);
        $('#remove-parent-button').hide();
    });

    // Remove empty rows for taxes/deductions before submitting
    $('.page-categories-index form, .page-categories-edit form').submit(function () {
        let $form = $(this);

        $form.find('.taxes-row').each(function () {
            let $row = $(this);
            let allInputsAreEmpty = true;

            $(this).find('input').each(function () {
                if ($(this).val().length) {
                    allInputsAreEmpty = false;
                }
            });

            if (allInputsAreEmpty) {
                $row.remove();
            }
        });

        $form.find('.deductions-row').each(function () {
            let $row = $(this);
            let allInputsAreEmpty = true;

            $(this).find('input').each(function () {
                let $input = $(this);

                if ($input.attr('type') === 'radio') {

                    if ($input.is(':checked')) {
                        allInputsAreEmpty = false;
                    }

                }
                else if ($input.val().length) {
                    allInputsAreEmpty = false;
                }
            });

            if (allInputsAreEmpty) {
                $row.remove();
            }
        });
    });

    // ==================================================
    // TAXES
    // ==================================================
    $('#button-add-tax').click(function () {
        priceChangesIndex++;
        let $this = $(this);
        let $row = $('.taxes-row').first();
        let $new = $row.clone();

        // Copy and clear fields
        $new.find('option').prop('checked', false);
        $new.find('input[type=radio]').prop('checked', false);
        $new.find('input[type=number]').val('');
        $new.find('input, select').each(function () {
            let $input = $(this);
            let name = $input.prop('name').replace('0', priceChangesIndex);
            $input.prop('name', name);
        });
        $new.addClass('m-t-1');

        $('.taxes').append($new);

        console.log($this.closest('.taxes'));

        $('.button-remove-tax').removeClass('hidden').prop('disabled', false);
    });

    $('.taxes').on('click', '.button-remove-tax', function () {
        let $this = $(this);

        if ($this.data('category-tax-id')) {
            $.ajax({
                url: '/categories/' + $this.data('category-id') + '/taxes/' + $this.data('category-tax-id'),
                method: 'delete',
                data: {_token: _token}
            });
        }

        $this.closest('.taxes-row').remove();

        if ($('.taxes-row').length === 1) {
            $('.taxes-row').find('.button-remove-tax').addClass('hidden').prop('disabled', true);
        }
    });

    // ==================================================
    // DEDUCTIONS
    // ==================================================
    $('#button-add-deduction').click(function () {
        priceChangesIndex++;
        let $row = $('.deductions-row').first();
        let $new = $row.clone();

        // Copy and clear fields
        $new.find('option').prop('checked', false);
        $new.find('input[type=radio]').prop('checked', false);
        $new.find('input[type=number]').val('');
        $new.find('input, select').each(function () {
            let $input = $(this);
            let name = $input.prop('name').replace('0', priceChangesIndex);
            $input.prop('name', name);
        });
        $new.addClass('m-t-1');

        $('.deductions').append($new);

        $('.button-remove-deduction').removeClass('hidden').prop('disabled', false);
    });

    $('.deductions').on('click', '.button-remove-deduction', function () {
        let $this = $(this);

        if ($this.data('category-deduction-id')) {
            $.ajax({
                url: '/categories/' + $this.data('category-id') + '/deductions/' + $this.data('category-deduction-id'),
                method: 'delete',
                data: {_token: _token}
            });
        }

        $this.closest('.deductions-row').remove();

        if ($('.deductions-row').length === 1) {
            $('.deductions-row').find('.button-remove-deduction').addClass('hidden').prop('disabled', true);
        }
    });
}
