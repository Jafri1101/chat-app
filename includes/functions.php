<?php

function getStatus($last_seen)
{
    if (!$last_seen) {
        return "Offline";
    }

    $last_seen_time = strtotime($last_seen);
    $current_time = time();
    $difference = $current_time - $last_seen_time;

    if ($difference <= 60) {
        return "Online";
    } else {
        return "Last seen " . date("H:i", $last_seen_time);
    }
}
