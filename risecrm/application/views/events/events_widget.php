<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-calendar"></i>&nbsp; <?php echo lang("events"); ?>
    </div>
    <div id="upcoming-event-container">
        <div class="panel-body">
            <div style="min-height: 190px;">
                <?php
                if ($events) {
                    foreach ($events as $event) {
                        ?>
                        <div class="mb20">
                            <div><?php echo modal_anchor(get_uri("events/view/"), "<span style='background-color:" . $event->color . "' class='color-tag pull-left'></span>" . $event->title, array("data-post-id" => encode_id($event->id, "event_id"), "title" => lang("event_details"))); ?></div>
                            <div><?php $this->load->view("events/event_time", array("model_info" => $event)); ?></div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div class='text-center'>" . lang("no_event_found") . "</div>";
                    echo "<div class='text-center p15 text-off'><i class='fa fa-calendar' style='font-size:50px;color: #c4c4c4;margin-top: 30px;'></i></div>";
                }
                ?>
            </div>
            <div><?php echo anchor("events", lang("view_on_calendar"), array("class" => "btn btn-default b-a load-more mt15")); ?></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#upcoming-event-container').slimscroll({
            height: "285px",
            borderRadius: "0",
            color: "#ccc"
        });
    });
</script>    