<?php
$table = 'export_csv';
require_once('../core/core.php');
require_once('../core/core.db.php');

$inv_code = IsSet($_GET['inv_code']) ? $_GET['inv_code'] : -1;

$link = ConnectDB();
$data = array();

if ($inv_code != -1 )
{
    $q = "SELECT * from {$table} where inv_code LIKE '{$inv_code}%'";
    $r = mysql_query($q);
    $nr = @mysql_num_rows($r);
    if ($nr == 1) {
        $data['data'] = mysql_fetch_assoc($r);
        $data['state'] = 'found';
    } else {
        $data['state'] = 'notfound';
        $data['data']['inv_code'] = $inv_code;
    }
} else {
    $data['state'] = 'error';
    $data['message'] = $q;
}

CloseDB($link);
print(json_encode($data));
?>