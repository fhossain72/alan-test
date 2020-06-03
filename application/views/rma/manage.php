<div class="row">
    <div class="col-md-12">
        <main class="my_account">
            <h3 class="montserrat page-title"><?php echo $page_title; ?></h3>

            <form method="get" action="" id="rma_ajax_order_search" style="text-align: right;margin-bottom: 10px">
                <input type="number" name="order_id" id="order_id" class="cus_field" value="<?php echo $this->input->get('order_id') ? $this->input->get('order_id') : ''; ?>" placeholder="Order Number" required style="width: 20%;margin-right: -2px"/>
                <button type="submit" name="search" value="1" class="roboto_condensed cus_button ajax_order_search_button" style="height: 42px">
                    Search
                </button>
            </form>

            <table width="100%" class="table table-bordered manage_table">
                <colgroup>
                    <col width="15%">
                    <col width="25%">
                    <col width="10%">
                    <col width="5%">
                    <col width="10%">
                    <col width="20%">
                    <col width="15%">
                </colgroup>
                <thead>
                <tr>
                    <th>Date Time</th>
                    <th>Product</th>
                    <th class="text-center">Order#</th>
                    <th class="text-center">Item Qty.</th>
                    <th class="text-center">Return Qty.</th>
                    <th>Return Reason</th>
                    <th class="text-center">Shipping Cost</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(!empty($rma_parent_request)) {
                    foreach ($rma_parent_request as $p_request) {
                        $request_list = $this->global_model->get('rma_request', array('parent_request_id' => $p_request->id));

                        if(!empty($request_list)) {
                            $list_i = 1;
                            foreach ($request_list as $request) {
                                ?>
                                <tr>
                                <?php
                                if($list_i == 1) {
                                    ?>
                                    <td class="v_middle" rowspan="<?php echo $p_request->total_request; ?>"><?php echo date('j F, Y h:ia', strtotime($p_request->added)); ?></td>
                                    <?php
                                }
                                ?>

                                <td><?php echo $request->item_name; ?></td>

                                <?php
                                if($list_i == 1) {
                                    ?>
                                    <td class="v_middle text-center" rowspan="<?php echo $p_request->total_request; ?>"><?php echo $p_request->order_id; ?></td>
                                    <?php
                                }
                                ?>

                                <td class="text-center"><?php echo $request->item_quantity; ?></td>
                                <td class="text-center"><?php echo $request->return_quantity; ?></td>
                                <td><?php echo $request->return_reason; ?></td>

                                <?php
                                if($list_i == 1) {
                                    ?>
                                    <td class="text-center v_middle" rowspan="<?php echo $p_request->total_request; ?>"><?php echo $p_request->shipping_cost > 0 ? number_format($p_request->shipping_cost, 2) : ''; ?></td>
                                    <?php
                                }
                                ?>
                                </tr><?php
                                $list_i++;
                            }
                        }
                    }
                }
                else {
                    ?>
                    <tr>
                        <td colspan="8" style="text-align:center">
                            <b>No RMA Request Found!</b>
                        </td>
                    </tr><?php
                }
                ?>
                </tbody>
            </table>

            <div class="tablenav top">
                <div class="float-right tablenav-pages">
                    <span class="displaying-num"><?php echo $total; ?> items</span>
                    <nav aria-label="Page navigation example">
                        <?php echo $this->pagination->create_links(); ?>
                    </nav>
                </div>
            </div>
        </main>
    </div>
</div>
