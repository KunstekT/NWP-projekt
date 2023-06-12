<?php

function formatTimeAgo($dateTime)
{
    $now = now();
    $diff = $dateTime->diffInSeconds($now);

    if ($diff < 60) {
        return $diff . ' seconds ago';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minutes ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hours ago';
    } else {
        $days = floor($diff / 86400);
        return $days . ' days ago';
    }
}
?>