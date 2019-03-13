<div id="page-content" class="clearfix m20">
    <div class="panel clearfix">
        <ul data-toggle="ajax-tab" class="nav nav-tabs bg-white inner" role="tablist">
            <li class="title-tab"><h4 class="pl15 pt5 pr15"><?php echo lang("expenses"); ?></h4></li>
            <li><a id="monthly-expenses-button"  role="presentation" class="active" href="javascript:;" data-target="#monthly-expenses"><?php echo lang("monthly"); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("expenses/yearly/"); ?>" data-target="#yearly-expenses"><?php echo lang('yearly'); ?></a></li>
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php echo modal_anchor(get_uri("expenses/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_expense'), array("class" => "btn btn-default mb0", "title" => lang('add_expense'))); ?>
                </div>
            </div>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="monthly-expenses">
                <div class="table-responsive">
                    <table id="monthly-expense-table" class="display" cellspacing="0" width="100%">
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right"><?php echo lang("total") ?>:</th>
                                <th class="text-right" data-current-page="3"></th>
                                <th> </th>
                            </tr>
                            <tr data-section="all_pages">
                                <th colspan="3" class="text-right"><?php echo lang("total_of_all_pages") ?>:</th>
                                <th class="text-right" data-all-page="3"></th>
                                <th> </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="yearly-expenses"></div>
        </div>
    </div>
</div>


<div class="panel clearfix">
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="team_member-monthly-leaves">
            <table id="monthly-leaves-table" class="display" cellspacing="0" width="100%">            
            </table>
            <script type="text/javascript">
                loadExpensesTable = function(selector, dateRange) {
                    $(selector).appTable({
                        source: '<?php echo_uri("expenses/list_data") ?>',
                        dateRangeType: dateRange,
                        filterDropdown: [{name: "category_id", class: "w200", options: <?php echo $categories_dropdown; ?>}],
                        columns: [
                            {title: '<?php echo lang("date") ?>'},
                            {title: '<?php echo lang("category") ?>'},
                            {title: '<?php echo lang("description") ?>'},
                            {title: '<?php echo lang("amount") ?>', "class": "text-right"},
                            {title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"},
                        ],
                        printColumns: [0, 1, 2, 3],
                        xlsColumns: [0, 1, 2, 3],
                        summation: [{column: 3, dataType: 'currency'}]
                    });
                };

                $(document).ready(function() {
                    $("#monthly-expenses-button").trigger("click");
                    loadExpensesTable("#monthly-expense-table", "monthly");
                });
            </script>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="team_member-yearly-leaves"></div>
    </div>
</div>
