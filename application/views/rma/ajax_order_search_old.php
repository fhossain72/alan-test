<div>
    <form method="post" id="rma_request">
        <h3 class="montserrat inner_title search_orderID">Order# <?php echo $order_number; ?></h3>
        <input type="hidden" name="order_number" value="<?php echo $order_number; ?>">

        <table width="100%" class="table table-bordered order-items">
            <tr>
                <th width="2%">#</th>
                <th>Item</th>
                <th width="13%">SKU</th>
                <th width="5%" class="text-center">Qty.</th>
                <th width="20%">Return Reason</th>
                <th width="10%">Return Qty.</th>
            </tr>

            <?php
            $count_total_empty_rows = 0;
            foreach ($order_items as $order_item) {
                $product_id          = 0;
                $variation_id        = 0;
                $qty                 = 0;
                $previous_return_qty = 0;
                $meta_items = $this->global_model->get('woocommerce_order_itemmeta', array('order_item_id' => $order_item->order_item_id));
                if(!empty($meta_items)) {
                    foreach ($meta_items as $meta_item) {
                        if($meta_item->meta_key == '_product_id') {
                            $product_id = $meta_item->meta_value;
                        }
                        elseif($meta_item->meta_key == '_variation_id') {
                            $variation_id = $meta_item->meta_value;
                        }
                        elseif($meta_item->meta_key == '_qty') {
                            $qty = $meta_item->meta_value;
                        }
                        else {
                            break;
                        }
                    }
                }
                $item_name = $order_item->order_item_name;
                $item_name = str_replace(array(', 3 months', ', 6 months', ', 12 months'), '', $item_name);
                $item_name = str_replace(array(' - 3 months', ' - 6 months', ' - 12 months'), '', $item_name);
                $item_name = str_replace(array('3 months', '6 months', '12 months'), '', $item_name);
                // get sku info
                $sku_params = array('post_id'  => $variation_id,
                                    'meta_key' => '_sku'
                );
                $sku_info   = $this->global_model->get_row('postmeta', 'meta_value', $sku_params);
                if(!$sku_info) {
                    $sku_params = array('post_id'  => $product_id,
                                        'meta_key' => '_sku'
                    );
                    $sku_info   = $this->global_model->get_row('postmeta', 'meta_value', $sku_params);
                }
                $sku = !empty($sku_info) ? $sku_info->meta_value : 'N/A';
                // get bundle product mapping info
                $bundle_info_params  = array('post_id'  => $variation_id,
                                             'meta_key' => '_bundle_product_mapping_id'
                );
                $bundle_mapping_info = $this->global_model->get_row('postmeta', 'meta_value', $bundle_info_params);
                $bundle_mapping_ids  = array();
                if(!empty($bundle_mapping_info)) {
                    $bundle_mapping_ids = unserialize($bundle_mapping_info->meta_value);
                }
                if(!empty($bundle_mapping_ids)) {
                    foreach ($bundle_mapping_ids as $mapping_id) {
                        // get variation info
                        $variation_info      = $this->global_model->get_row('posts', 'post_title, post_parent', array('ID' => $mapping_id));
                        $variation_name      = !empty($variation_info) ? $variation_info->post_title : $order_item->order_item_name;
                        $variation_name = str_replace(array(', 3 months', ', 6 months', ', 12 months'), '', $variation_name);
                        $variation_name = str_replace(array(' - 3 months', ' - 6 months', ' - 12 months'), '', $variation_name);
                        $variation_name = str_replace(array('3 months', '6 months', '12 months'), '', $variation_name);
                        $variation_parent_id = !empty($variation_info) ? $variation_info->post_parent : 0;
                        // get variation sku
                        $sku_params = array('post_id'  => $mapping_id,
                                            'meta_key' => '_sku'
                        );
                        $sku_info   = $this->global_model->get_row('postmeta', 'meta_value', $sku_params);
                        $sku        = !empty($sku_info) ? $sku_info->meta_value : $sku;
                        // get bundle quantity
                        $qty_params      = array('post_id'  => $mapping_id,
                                                 'meta_key' => '_bundle_product_quantity'
                        );
                        $qty_info        = $this->global_model->get_row('postmeta', 'meta_value', $qty_params);
                        $bundle_quantity = !empty($qty_info) ? $qty_info->meta_value : 1;
                        $total_qty       = $qty * $bundle_quantity;
                        // get sum of previous return qty
                        $rma_request_params   = array('order_id'     => $order_number,
                                                      'variation_id' => $mapping_id,
                                                      'bundle_product_id' => $product_id,
                        );
                        $previous_return_info = $this->global_model->get_row('rma_request', '*, SUM(return_quantity) AS previous_return_qty', $rma_request_params);
                        $previous_return_qty  = !empty($previous_return_info) ? $previous_return_info->previous_return_qty : 0;
                        // return logs
                        $return_logs = $this->global_model->get('rma_request', $rma_request_params);
                        $rest_of_qty = $total_qty - $previous_return_qty;
                        $item_identity = $mapping_id.'_'.$product_id;
                        ?>
                        <tr>
                        <td>
                            <?php
                            if($rest_of_qty != 0) {
                                $count_total_empty_rows++;
                                ?>
                                <input type="checkbox" name="variation_ids[]" class="item_checkbox" value="<?php echo $item_identity; ?>"><?php
                            }
                            ?>
                        </td>
                        <td>
                            <input type="hidden" name="order_items[<?php echo $item_identity; ?>][bundle_product_id]" value="<?php echo $product_id; ?>">
                            <input type="hidden" name="order_items[<?php echo $item_identity; ?>][product_id]" value="<?php echo $variation_parent_id; ?>">
                            <input type="hidden" name="order_items[<?php echo $item_identity; ?>][item_name]" value="<b><?php echo $item_name; ?> </b> <i class='fa fa-long-arrow-right' aria-hidden='true'></i> <?php echo $variation_name; ?>">
                            <p class="product_name"><b><?php echo $item_name; ?></b> <i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?php echo $variation_name; ?></p>
                            <?php
                            if(!empty($return_logs)) {
                                ?>
                                <ul class="logs">
                                <?php
                                foreach ($return_logs as $log) {
                                    $return_by = get_customer_info($log->user_id);
                                    ?>
                                    <li>
                                    <span>&rarr;</span> <?php echo $log->return_quantity; ?>
                                    Quantity return by <?php echo $return_by->first_name; ?>
                                    on <?php echo date('j F, Y', strtotime($log->added)); ?>
                                    reason is <?php echo $log->return_reason; ?>
                                    </li><?php
                                }
                                ?>
                                </ul><?php
                            }
                            ?>
                        </td>
                        <td>
                            <p><?php echo $sku; ?></p>
                            <input type="hidden" name="order_items[<?php echo $item_identity; ?>][sku]" value="<?php echo $sku; ?>">
                        </td>
                        <td class="text-center">
                            <input type="hidden" name="order_items[<?php echo $item_identity; ?>][rest_of_qty]" value="<?php echo $rest_of_qty; ?>" class="rest_of_qty">
                            <input type="hidden" name="order_items[<?php echo $item_identity; ?>][qty]" value="<?php echo $total_qty; ?>" class="order_item_qty">
                            <p><?php echo $total_qty; ?></p>
                        </td>
                        <td class="item-show-hide">
                            <select name="order_items[<?php echo $item_identity; ?>][return_reason]" class="form-control return_reason" disabled>
                                <option value="">Select one</option>
                                <option value="Bad address">Bad address</option>
                                <option value="Refused delivery/RTS">Refused delivery/RTS</option>
                                <option value="Unauthorized return (No RMA # issued by Prestige)">Unauthorized return
                                    (No RMA # issued by Prestige)
                                </option>
                                <option value="Authorized Return (RMA # issued by Prestige)">Authorized Return (RMA #
                                    issued by Prestige)
                                </option>
                            </select>
                        </td>
                        <td class="item-show-hide">
                            <input type="number" name="order_items[<?php echo $item_identity; ?>][return_quantity]" class="form-control return_quantity" disabled>
                        </td>
                        </tr><?php
                    }
                }
                else {
                    // get bundle quantity
                    $qty_params      = array('post_id'  => $variation_id,
                                             'meta_key' => '_bundle_product_quantity'
                    );
                    $qty_info        = $this->global_model->get_row('postmeta', 'meta_value', $qty_params);
                    $bundle_quantity = !empty($qty_info) ? $qty_info->meta_value : 1;
                    $total_qty       = $qty * $bundle_quantity;
                    // get sum of previous return qty
                    $rma_request_params   = array('order_id'     => $order_number,
                                                  'variation_id' => $variation_id
                    );
                    $previous_return_info = $this->global_model->get_row('rma_request', '*, SUM(return_quantity) AS previous_return_qty', $rma_request_params);
                    $previous_return_qty  = !empty($previous_return_info) ? $previous_return_info->previous_return_qty : 0;
                    // return logs
                    $return_logs = $this->global_model->get('rma_request', $rma_request_params);
                    $rest_of_qty = $total_qty - $previous_return_qty;
                    ?>
                    <tr>
                    <td>
                        <?php
                        if($rest_of_qty != 0) {
                            $count_total_empty_rows++;
                            ?>
                            <input type="checkbox" name="variation_ids[]" class="item_checkbox" value="<?php echo $variation_id; ?>"><?php
                        }
                        ?>
                    </td>
                    <td>
                        <input type="hidden" name="order_items[<?php echo $variation_id; ?>][product_id]" value="<?php echo $product_id; ?>">
                        <input type="hidden" name="order_items[<?php echo $variation_id; ?>][item_name]" value="<?php echo $item_name; ?>">
                        <p class="product_name"><?php echo $item_name; ?></p>

                        <?php
                        if(!empty($return_logs)) {
                            ?>
                            <ul class="logs">
                            <?php
                            foreach ($return_logs as $log) {
                                $return_by = get_customer_info($log->user_id);
                                ?>
                                <li>
                                <span>&rarr;</span> <?php echo $log->return_quantity; ?>
                                Quantity return by <?php echo $return_by->first_name; ?>
                                on <?php echo date('j F, Y', strtotime($log->added)); ?>
                                reason is <?php echo $log->return_reason; ?>
                                </li><?php
                            }
                            ?>
                            </ul><?php
                        }
                        ?>
                    </td>
                    <td>
                        <p><?php echo $sku; ?></p>
                        <input type="hidden" name="order_items[<?php echo $variation_id; ?>][sku]" value="<?php echo $sku; ?>">
                    </td>
                    <td class="text-center">
                        <input type="hidden" name="order_items[<?php echo $variation_id; ?>][rest_of_qty]" value="<?php echo $rest_of_qty; ?>" class="rest_of_qty">
                        <input type="hidden" name="order_items[<?php echo $variation_id; ?>][qty]" value="<?php echo $total_qty; ?>" class="order_item_qty">
                        <p><?php echo $total_qty; ?></p>
                    </td>
                    <td class="item-show-hide">
                        <select name="order_items[<?php echo $variation_id; ?>][return_reason]" class="form-control return_reason" disabled>
                            <option value="">Select one</option>
                            <option value="Bad address">Bad address</option>
                            <option value="Refused delivery/RTS">Refused delivery/RTS</option>
                            <option value="Unauthorized return (No RMA # issued by Prestige)">Unauthorized return (No
                                RMA # issued by Prestige)
                            </option>
                            <option value="Authorized Return (RMA # issued by Prestige)">Authorized Return (RMA # issued
                                by Prestige)
                            </option>
                        </select>
                    </td>
                    <td class="item-show-hide">
                        <input type="number" name="order_items[<?php echo $variation_id; ?>][return_quantity]" class="form-control return_quantity" disabled>
                    </td>
                    </tr><?php
                }
            }
            ?>
        </table>

        <div class="clearfix"></div>

        <?php
        if($count_total_empty_rows > 0) {
            ?>
            <div class="shipping-cost-box">
                <div>Shipping Cost:</div>
                <div>
                    <input type="text" name="shipping_cost" class="form-control">
                </div>
            </div>
            <button type="submit" name="submit" class="roboto_condensed cus_button" value="send_request">Submit
                Request
            </button><?php
        }
        ?>
    </form>
</div>