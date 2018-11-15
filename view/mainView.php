<?php
function auto_version_file(string $file) : string
{
    $siteName = '/codyfulford.com';
    return strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'].$siteName.$file)
            ?   $file
            :   $siteName.preg_replace('{\\.([^./]+)$}', ".".filemtime($_SERVER['DOCUMENT_ROOT'].$siteName . $file).".\$1", $file);
}

function getDaysBetweenDates(string $firstDate, string $secondDate) : int
{
    return round((strtotime($firstDate) - strtotime($secondDate)) / (60 * 60 * 24));
}

function formatDate(string $date) : string
{
    return date('D, M jS, Y', strtotime($date));
}
