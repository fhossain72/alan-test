<div class="row">
    <div class="col-md-12 request_container">
        <form method="post" id="rma_ajax_order_search">
            <h3 class="montserrat page-title"><?php echo $page_title; ?></h3>
            <div class="order-search-box" style="padding: 0">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="number" name="order_number" id="order_number" class="cus_field" value="<?php echo set_value('order_number'); ?>" placeholder="Order Number" required/>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" name="ajax_order_search" value="1" class="roboto_condensed cus_button ajax_order_search_button">
                                Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="clearfix"></div>

        <div class="ajax-order-search-content" style="display:none;"></div>
    </div>
</div>

<script src="<?php echo base_url('assets/js/jquery.validate.min.js'); ?>"></script>

<script type="text/javascript">
    jQuery(document).ready(function () {
        // manage html content
        jQuery('#rma_ajax_order_search').submit(function () {
            var order_number = jQuery('#order_number').val();
            jQuery('.ajax_order_search_button').html('Please wait...');

            if (order_number) {
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '<?php echo site_url('ajax/load_order_rma_content'); ?>',
                    data: {order_number: order_number},
                    success: function (data) {
                        jQuery('.ajax_order_search_button').html('Search');

                        jQuery('.ajax-order-search-content').show();
                        jQuery('.ajax-order-search-content').html(data);
                    }
                });
            }

            return false;
        });

        // select order items
        jQuery('body').on('click', '.item_checkbox', function () {
            if (jQuery(this).is(':checked')) {
                jQuery(this).parents('tr').find('.item-show-hide input, .item-show-hide select').removeAttr('disabled');
            } else {
                jQuery(this).parents('tr').find('.item-show-hide input, .item-show-hide select').attr('disabled', 'disabled');
                jQuery(this).parents('tr').find('.item-show-hide input').val('');
                jQuery(this).parents('tr').find('.item-show-hide select').val('');
            }
        });

        // on change item qty
        jQuery('body').on('input change keyup paste', '.return_quantity', function () {
            var rest_of_qty = jQuery(this).parents('tr').find('.rest_of_qty').val();
            var return_quantity = jQuery(this).val();

            rest_of_qty = parseInt(rest_of_qty);
            return_quantity = parseInt(return_quantity);

            if (return_quantity > rest_of_qty) {
                jQuery(this).val(rest_of_qty);
                alert('You are not allowed more than ' + rest_of_qty);
            }
        });
    });
</script>