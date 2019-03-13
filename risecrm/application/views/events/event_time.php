<?php

$today = get_today_date();
$tomorrow = get_tomorrow_date();

if ($model_info->start_date == $model_info->end_date) {
    if ($model_info->start_date === $today) {
        echo lang("today");
    } else if ($model_info->start_date === $tomorrow) {
        echo lang("tomorrow");
    } else {
        echo date("D, F d", strtotime($model_info->start_date));
    }

    if ($model_info->start_time * 1) {
        echo ", " . format_to_time($model_info->start_date . " " . $model_info->start_time, false);
        echo " – " . format_to_time($model_info->end_date . " " . $model_info->end_time, false);
    }
} else {
    echo date("D, F d", strtotime($model_info->start_date));
    if ($model_info->start_time * 1) {
        echo ", " . format_to_time($model_info->start_date . " " . $model_info->start_time, false);
    }

    echo " – " . date("D, F d", strtotime($model_info->end_date));
    if ($model_info->end_time * 1) {
        echo ", " . format_to_time($model_info->end_date . " " . $model_info->end_time, false);
    }
}
?>