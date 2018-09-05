<?php

function download_send_headers($filename, $filesize)
{
    header ("HTTP/1.1 200 OK");
    header ("X-Powered-By: PHP/" . phpversion());
    header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
    header ("Cache-Control: None");
    header ("Pragma: no-cache");
    header ("Accept-Ranges: bytes");
    header ("Content-Disposition: inline; filename=\"" . $filename . "\"");

    if (isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
        Header('Content-Type: application/force-download');
    else
        Header('Content-Type: application/octet-stream');
    header ("Content-Length: " . $filesize);
    header ("Age: 0");
    header ("Proxy-Connection: close");
    header('Accept-Ranges: bytes');
    header('Content-Length: ' . $filesize);

}

function array2csv(array &$array)
{
    if (count($array) == 0) {
        return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    fputcsv($df, array_keys(reset($array)));
    foreach ($array as $row) {
        fputcsv($df, $row, ';');
    }
    fclose($df);
    return ob_get_clean();
}



