checkMessageNotifications = function(params, updateStatus) {
    if (params && params.notificationUrl) {
        $.ajax({
            url: params.notificationUrl,
            dataType: 'json',
            success: function(result) {
                if (result.success && result.total_messages) {
                    params.notificationSelector.html("<i class='fa fa-envelope-o'></i> <span class='badge bg-danger up'>" + result.total_messages + "</span>");
                    params.notificationSelector.parent().find(".dropdown-details").html(result.notification_list);
                    if (updateStatus) {
                        //update last notification checking time
                        $.ajax({
                            url: params.notificationStatusUpdateUrl,
                            success: function() {
                                params.notificationSelector.html("<i class='fa fa-envelope-o'></i>");
                            }
                        });
                    }
                }
                if (!updateStatus) {
                    //check notification again after sometime
                    var check_notification_after_every = params.checkNotificationAfterEvery;
                    check_notification_after_every = check_notification_after_every * 1000;
                    if (check_notification_after_every < 10000) {
                        check_notification_after_every = 10000; //don't allow to call this requiest before 10 seconds
                    }

                    setTimeout(function() {
                        checkMessageNotifications(params);
                    }, check_notification_after_every);
                }
            }
        });
    }
};

