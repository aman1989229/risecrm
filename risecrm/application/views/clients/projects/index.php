<?php if (isset($page_type) && $page_type === "full") { ?>
    <div id="page-content" class="m20 clearfix">
    <?php } ?>

    <div class="panel">
        <?php if (isset($page_type) && $page_type === "full") { ?>
            <div class="page-title clearfix">
                <h1><?php echo lang('projects'); ?></h1>
            </div>
        <?php } else if (isset($page_type) && $page_type === "dashboard") { ?>
            <div class="page-title panel-sky clearfix">
                <h1><?php echo lang('projects'); ?></h1>
            </div>
        <?php } else { ?>
            <div class="tab-title clearfix">
                <h4><?php echo lang('projects'); ?></h4>
            </div>
        <?php } ?>

        <div class="table-responsive">
            <table id="project-table" class="display" width="100%">            
            </table>
        </div>
    </div>
    <?php if (isset($page_type) && $page_type === "full") { ?>
    </div>
<?php } ?>


<script type="text/javascript">
    $(document).ready(function() {
        var hideTools = "<?php
if (isset($page_type) && $page_type === 'dashboard') {
    echo 1;
}
?>";

        $("#project-table").appTable({
            source: '<?php echo_uri("projects/projects_list_data_of_client/" . $client_id) ?>',
            order: [[0, "desc"]],
            hideTools: hideTools,
            columns: [
                {title: '<?php echo lang("id") ?>', "class": "w50"},
                {title: '<?php echo lang("title") ?>'},
                {targets: [2], visible: false, searchable: false},
                {title: '<?php echo lang("deadline") ?>', "class": "w10p"},
                {title: '<?php echo lang("progress") ?>', "class": "w15p"},
                {title: '<?php echo lang("status") ?>', "class": "w10p"},
            ],
            printColumns: [0, 1, 3, 4, 5],
            xlsColumns: [0, 1, 3, 5]
        });
    });
</script>