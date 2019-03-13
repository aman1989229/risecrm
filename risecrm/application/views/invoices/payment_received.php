<div id="page-content" class="p20 clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('payment_received'); ?></h1>
        </div>
        <div class="table-responsive">
            <table id="invoice-payment-table" class="display" width="100%">
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right"><?php echo lang("total") ?>:</th>
                        <th class="text-right" data-current-page="4"></th>
                    </tr>
                    <tr data-section="all_pages">
                        <th colspan="4" class="text-right"><?php echo lang("total_of_all_pages") ?>:</th>
                        <th class="text-right" data-all-page="4"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $("#invoice-payment-table").appTable({
            source: '<?php echo_uri("invoice_payments/payment_list_data/") ?>',
            order: [[0, "asc"]],
            dateRangeType: "monthly",
            filterDropdown: [{name: "payment_method_id", class: "w200", options: <?php echo $payment_method_dropdown; ?>}],
            columns: [
                {title: '<?php echo lang("invoice_id") ?> ', "class": "w10p"},
                {title: '<?php echo lang("payment_date") ?> ', "class": "w15p"},
                {title: '<?php echo lang("payment_method") ?>', "class": "w15p"},
                {title: '<?php echo lang("note") ?>'},
                {title: '<?php echo lang("amount") ?>', "class": "text-right w15p"}
            ],
            summation: [{column: 4, dataType: 'currency', currencySymbol: "₹ "}],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4]
        });

    });
</script>