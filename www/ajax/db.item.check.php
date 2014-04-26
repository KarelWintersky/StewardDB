<?php
require_once('../core/core.php');
require_once('../core/core.db.php');
$table = $CONFIG['main_data_table'];

$inv_code = IsSet($_GET['inv_code']) ? $_GET['inv_code'] : -1;

$ic = mysql_escape_string($_GET['inv_code']);

$link = ConnectDB();
$data = array();

$q = "SELECT * from {$table} where inv_code LIKE '%{$ic}%'";

if ($inv_code != -1 )
{

    $r = mysql_query($q);
    $nr = @mysql_num_rows($r);
    $data['count'] = $nr;
    $data['query'] = $q;

    if ($nr == 1) {
        $data['data'] = mysql_fetch_assoc($r);
        $data['state'] = 'found';
    } else if ( $nr == 0 ) {
        $data['state'] = 'notfound';
        $data['data']['inv_code'] = $inv_code;
        $data['data']['date_income_str'] = date("d.m.Y");
    } else if ($nr > 1) {
        $data['state'] = 'multi';
    }
} else {
    $data['state'] = 'error';
    $data['message'] = $q;
}

CloseDB($link);
print(json_encode($data));
//print_r($data);
?>