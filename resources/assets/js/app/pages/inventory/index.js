{
    $('#product-categories').jstree({
        plugins: ['checkbox', 'types'],
        types: {
            default: {
                icon: 'glyphicon glyphicon-folder-close text-info'
            },
            demo: {
                icon: 'glyphicon glyphicon-ok'
            }
        },
        checkbox: {
            visible: true,
            three_state: false,
            whole_node: true,
            keep_selected_style: true,
            cascade: 'up',
            tie_selection: false
        },
    }).show();

    $('thead [data-sort-by]').click(function () {
        let $inventoryForm = $('#inventory-form');
        let sortBy = $(this).data('sort-by');
        let $sortDirection = $inventoryForm.find('[name="sort_direction"]');

        $inventoryForm.find('[name="sort"]').val(sortBy);

        $sortDirection.val($sortDirection.val() === 'desc' ? 'asc' : 'desc');

        $inventoryForm.submit();
    });

    $(`[data-category-level="1"][data-selected="true"]`).each(function () {
        $(this).find('.jstree-anchor').click();
        $('#product-categories').jstree('open_node', $(this));
    });

    setTimeout(function () {
        $(`[data-category-level="2"][data-selected="true"]`).each(function () {
            $(this).find('.jstree-anchor').click();
            $('#product-categories').jstree('open_node', $(this));
        });
    }, 500);

    setTimeout(function () {
        $(`[data-category-level="3"][data-selected="true"]`).each(function () {
            $(this).find('.jstree-anchor').click();
            $('#product-categories').jstree('open_node', $(this));
        });
    }, 1000);
}
