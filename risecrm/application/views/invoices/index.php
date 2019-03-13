<div id="page-content" class="p20 clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('invoices'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("invoices/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_invoice'), array("class" => "btn btn-default", "title" => lang('add_invoice'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="invoice-table" class="display" cellspacing="0" width="100%">   
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right"><?php echo lang("total") ?>:</th>
                        <th class="text-right" data-current-page="5"></th>
                        <th class="text-right" data-current-page="6"></th>
                        <th  colspan="2"> </th>
                    </tr>
                    <tr data-section="all_pages">
                        <th colspan="5" class="text-right"><?php echo lang("total_of_all_pages") ?>:</th>
                        <th class="text-right" data-all-page="5"></th>
                        <th class="text-right" data-all-page="6"></th>
                        <th colspan="2"> </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#invoice-table").appTable({
            source: '<?php echo_uri("invoices/list_data") ?>',
            order: [[0, "desc"]],
            dateRangeType: "monthly",
            filterDropdown: [{name: "status", class: "w150", options: <?php $this->load->view("invoices/invoice_statuses_dropdown"); ?>}],
            columns: [
                {title: '<?php echo lang("invoice_id") ?> ', "class": "w10p"},
                {title: '<?php echo lang("client") ?>', "class": "w15p"},
                {title: '<?php echo lang("project") ?>', "class": "w15p"},
                {title: '<?php echo lang("bill_date") ?>', "class": "w10p"},
                {title: '<?php echo lang("due_date") ?>', "class": "w10p"},
                {title: '<?php echo lang("invoice_value") ?>', "class": "w10p text-right"},
                {title: '<?php echo lang("payment_received") ?>', "class": "w10p text-right"},
                {title: '<?php echo lang("status") ?>', "class": "w10p text-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3, 4, 5, 6, 7],
            xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7],
            summation: [{column: 5, dataType: 'currency', currencySymbol: "₹ "}, {column: 6, dataType: 'currency', currencySymbol: "₹ "}]
        });
    });



</script>