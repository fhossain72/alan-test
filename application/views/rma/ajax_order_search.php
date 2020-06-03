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

            if(!empty($order_items))
            {
                foreach ($order_items as $order_item) {
                    echo '<pre>'; print_r($order_item); echo '</pre>';
                    $product_id          = $order_item->product_id;
                    $variation_id        = $order_item->variation_id;
                    $previous_return_qty = 0;

                    $item_name = $order_item->item_name;
                    $item_name = str_replace(array(', 3 months', ', 6 months', ', 12 months'), '', $item_name);
                    $item_name = str_replace(array(' - 3 months', ' - 6 months', ' - 12 months'), '', $item_name);
                    $item_name = str_replace(array('3 months', '6 months', '12 months'), '', $item_name);

                    $bundle_mapping_info = $this->global_model->get_join('product_bundle_mapping', array('variation_id' => $variation_id), 'mapping_product_id, mapping_variation_id');

                    echo $this->db->last_query();

                    echo '<pre>'; print_r($bundle_mapping_info); echo '</pre>';
                    if(!empty($bundle_mapping_info)) {
                        foreach ($bundle_mapping_info as $mapping) {
                            // get mapping variation info
                            $mapping_info = $this->global_model->get_row_join('product_variation', array('id' => $mapping->mapping_variation_id));
                            $mapping_prod_info = $this->global_model->get_row_join('product', array('id' => $mapping->mapping_product_id));
                            $mapping_stock_info = $this->global_model->get_row_join('product_stock', array('product_id' => $mapping->mapping_product_id, 'variation_id' => $mapping->mapping_variation_id));

                            // get parent variation
                            $parent_variation_id = get_parent_variation_id($mapping->mapping_product_id, $mapping_info->flavor_id);
                            $parent_variation_id = !empty($parent_variation_id) ? $parent_variation_id : $variation_id;

                            // get stock quantity
                            $stock_quantity = !empty($mapping_info) ? $mapping_info->month_number : 1;
                            $total_qty       = $order_item->quantity;

                            // get sum of previous return qty
                            $rma_request_params   = array(
                                'order_id'     => $order_number,
                                'variation_id' => $mapping->mapping_variation_id,
                                'bundle_product_id' => $product_id
                            );
                            $previous_return_info = $this->global_model->get_row('rma_request', $rma_request_params, '*, SUM(return_quantity) AS previous_return_qty');
                            $previous_return_qty  = !empty($previous_return_info) ? $previous_return_info->previous_return_qty : 0;

                            // return logs
                            $return_logs = $this->global_model->get('rma_request', $rma_request_params);
                            $rest_of_qty = $total_qty - $previous_return_qty;

                            $item_identity = $mapping->mapping_variation_id.'_'.$product_id;

                            ?><tr>
                                <td>
                                    <?php
                                    if($rest_of_qty != 0) {
                                        $count_total_empty_rows++;
                                        ?><input type="checkbox" name="variation_ids[]" class="item_checkbox" value="<?php echo $item_identity; ?>"><?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <input type="hidden" name="order_items[<?php echo $item_identity; ?>][parent_variation_id]" value="<?php echo $parent_variation_id; ?>">
                                    <input type="hidden" name="order_items[<?php echo $item_identity; ?>][bundle_product_id]" value="<?php echo $product_id; ?>">
                                    <input type="hidden" name="order_items[<?php echo $item_identity; ?>][product_id]" value="<?php echo $mapping->mapping_product_id; ?>">
                                    <input type="hidden" name="order_items[<?php echo $item_identity; ?>][item_name]" value="<b><?php echo $item_name; ?> </b> <i class='fa fa-long-arrow-right' aria-hidden='true'></i> <?php echo $mapping_prod_info->title; ?>">
                                    <p class="product_name"><b><?php echo $item_name; ?></b> <i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?php echo $mapping_prod_info->title; ?></p>
                                    <?php
                                    if(!empty($return_logs)) {
                                        ?><ul class="logs">
                                            <?php
                                            foreach ($return_logs as $log) {
                                                $return_by = get_customer_info($log->user_id);

                                                ?><li>
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
                                    <p><?php echo $mapping_info->sku; ?></p>
                                    <input type="hidden" name="order_items[<?php echo $item_identity; ?>][sku]" value="<?php echo $mapping_info->sku; ?>">
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
                        $variation_info = $this->global_model->get_row('product_variation', array('id' => $variation_id));
                        $product_info   = $this->global_model->get_row('product', array('id' => $product_id));

                        // get parent variation
                        $parent_variation_id = get_parent_variation_id($product_id, $variation_info->flavor_id);
                        $parent_variation_id = !empty($parent_variation_id) ? $parent_variation_id : $variation_id;

                        // get stock quantity
                        $stock_quantity = !empty($variation_info) ? $variation_info->month_number : 1;
                        $total_qty      = $order_item->quantity * $stock_quantity;

                        // get sum of previous return qty
                        $rma_request_params   = array(
                            'order_id'     => $order_number,
                            'variation_id' => $variation_id
                        );
                        $previous_return_info = $this->global_model->get_row('rma_request', $rma_request_params, '*, SUM(return_quantity) AS previous_return_qty');
                        $previous_return_qty  = !empty($previous_return_info) ? $previous_return_info->previous_return_qty : 0;

                        // get return logs
                        $return_logs = $this->global_model->get('rma_request', $rma_request_params);
                        $rest_of_qty = $total_qty - $previous_return_qty;

                        ?><tr>
                            <td>
                                <?php
                                if($rest_of_qty != 0) {
                                    $count_total_empty_rows++;
                                    ?><input type="checkbox" name="variation_ids[]" class="item_checkbox" value="<?php echo $variation_id; ?>"><?php
                                }
                                ?>
                            </td>
                            <td>
                                <input type="hidden" name="order_items[<?php echo $variation_id; ?>][parent_variation_id]" value="<?php echo $parent_variation_id; ?>">
                                <input type="hidden" name="order_items[<?php echo $variation_id; ?>][product_id]" value="<?php echo $product_id; ?>">
                                <input type="hidden" name="order_items[<?php echo $variation_id; ?>][item_name]" value="<?php echo $product_info->title; ?>">
                                <p class="product_name"><?php echo $product_info->title; ?></p>

                                <?php
                                if(!empty($return_logs)) {
                                    ?><ul class="logs">
                                        <?php
                                        foreach ($return_logs as $log) {
                                            $return_by = get_customer_info($log->user_id);

                                            ?><li>
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
                                <p><?php echo $variation_info->sku; ?></p>
                                <input type="hidden" name="order_items[<?php echo $variation_id; ?>][sku]" value="<?php echo $variation_info->sku; ?>">
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
                                    <option value="Unauthorized return (No RMA # issued by Prestige)">Unauthorized return (No RMA # issued by Prestige)</option>
                                    <option value="Authorized Return (RMA # issued by Prestige)">Authorized Return (RMA # issued by Prestige)</option>
                                </select>
                            </td>
                            <td class="item-show-hide">
                                <input type="number" name="order_items[<?php echo $variation_id; ?>][return_quantity]" class="form-control return_quantity" disabled>
                            </td>
                        </tr><?php
                    }
                }
            }
            ?>
        </table>

        <div class="clearfix"></div>

        <?php
        if($count_total_empty_rows > 0) {
            ?><div class="shipping-cost-box">
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