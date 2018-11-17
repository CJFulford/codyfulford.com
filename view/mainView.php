<?php
function auto_version_file($file)
{
    return strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'].$file)
            ?   $file
            :   $siteName.preg_replace('{\\.([^./]+)$}', ".".filemtime($_SERVER['DOCUMENT_ROOT'].$siteName . $file).".\$1", $file);
}

function getDaysBetweenDates($firstDate, $secondDate)
{
    return round((strtotime($firstDate) - strtotime($secondDate)) / (60 * 60 * 24));
}

function formatDate($date)
{
    return date('D, M jS, Y', strtotime($date));
}

function formatTime($time)
{
    return date('g:i A', strtotime($time));
}
