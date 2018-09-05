<?php
require_once '../core/__required.php';

$inv_code = $_GET['inv_code'] ?? -1;

$ic = mysqli_escape_string($mysqli, $_GET['inv_code']);

$data = array();

$q = "SELECT * from {$main_data_table} where inv_code LIKE '%{$ic}%'";

if ($inv_code != -1 )
{

    $r = mysqli_query($mysqli, $q);
    $nr = @mysqli_num_rows($r);
    $data['count'] = $nr;
    $data['query'] = $q;

    if ($nr == 1) {
        $data['data'] = mysqli_fetch_assoc($r);
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

print(json_encode($data));