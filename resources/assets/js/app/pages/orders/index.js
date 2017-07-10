{
    $('.checkbox-status').click(function () {
        $('.checkbox-status').each(function () {
            let $checkbox = $(this);
            let status = $checkbox.val();

            $(`select[name="statuses[]"] option[value="${status}"]`).prop('selected', $checkbox.prop('checked'));
        });

        $(this).closest('form').submit();
    });

    $('#checkbox-action-all').click(function () {
        if ($(this).prop('checked')) {
            $('.checkbox-select-order').prop('checked', true);
        }
        else {
            $('.checkbox-select-order').prop('checked', false);
        }
    });

    $('#button-approve-orders').click(function () {
        swal({
            title: "Are you sure you want to approve these selected orders?",
            text: "",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        },
        function() {
            
            let orderProductIds = getSelectedItems();
            $.ajax({
                url: '/orders/patch',
                type: 'patch',
                data: {
                    _token: _token,
                    orderProductIds: orderProductIds,
                    action: 'approve'
                }
            }).done(function () {
                
                swal("Approved!", "", "success");
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
                
                
            }).fail(function() { 
                swal("Error", "There was a problem communicating with the shipping server.", "error");
            });                        
        });                
    });

    $('#button-reject-orders').click(function () {
        swal({
            title: "Are you sure you want to reject these selected orders?",
            text: "",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        },
        function() {
            
            var orderProductIds = getSelectedItems();
            $.ajax({
                url: '/orders/patch',
                type: 'patch',
                data: {
                    _token: _token,
                    orderProductIds: orderProductIds,
                    action: 'reject'
                }
            }).done(function () {
                swal("Rejected!", "", "success");
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            }).fail(function() { 
                swal("Error", "There was a problem communicating with the shipping server.", "error");
            });
             
        });                      
        
    });

    function getSelectedItems() {
        let orderProductIds = [];

        $('.checkbox-select-order:checked').closest('tr').find('.order_item').each(function () {
            orderProductIds.push($(this).data('order-product-id'));
        });

        return orderProductIds;
    }
}
