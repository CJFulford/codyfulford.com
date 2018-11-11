<?php
function auto_version_file(string $file) : string
{
    $siteName = '/codyfulford.com';
    return strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'].$siteName.$file)
            ?   $file
            :   $siteName.preg_replace('{\\.([^./]+)$}', ".".filemtime($_SERVER['DOCUMENT_ROOT'].$siteName . $file).".\$1", $file);
}
