<?php
require_once '../core/__required.php';

$q = array(
    'inv_code'  => mysqli_real_escape_string($mysqli, $_GET['inv_code']),
    'mytitle'   => mysqli_real_escape_string($mysqli, $_GET['inv_mytitle']),
    'dbtitle'   => mysqli_real_escape_string($mysqli, $_GET['inv_dbtitle']),
    'room'      => mysqli_real_escape_string($mysqli, $_GET['inv_room']),
    'status'    => mysqli_real_escape_string($mysqli, $_GET['inv_status']),
    'owner'     => mysqli_real_escape_string($mysqli, $_GET['inv_owner']),
    'comment'   => mysqli_real_escape_string($mysqli, $_GET['inv_comment']),
    'date_income_str'   => mysqli_real_escape_string($mysqli, $_GET['inv_date_income_str']),
    'date_income_ts'    => ConvertDateToTimestamp(mysqli_real_escape_string($mysqli, $_GET['inv_date_income_str'])),
);
$q['date_income'] = Date('Y-m-d', $q['date_income_ts'] );

$link = ConnectDB();

$qstr = MakeInsert($q, $main_data_table);
$res = mysqli_query($mysqli, $qstr) or Die("Unable to insert data to DB! ".$qstr);
$new_id = mysqli_insert_id($mysqli) or Die("Unable to get last insert id! Last request is [$qstr]");

$data = array(
    'r_code' => mysqli_real_escape_string($mysqli, $_GET['inv_code']),
    'r_mytitle' => mysqli_real_escape_string($mysqli, $_GET['inv_mytitle']),
    'r_dbtitle' => mysqli_real_escape_string($mysqli, $_GET['inv_dbtitle']),
    'r_comment' => mysqli_real_escape_string($mysqli, $_GET['inv_comment']),
    'r_date_income_str' => mysqli_real_escape_string($mysqli, $_GET['inv_date_income_str']),
    'r_date_income_ts' => ConvertDateToTimestamp(mysqli_real_escape_string($mysqli, $_GET['inv_date_income_str'])),
    'r_room'        => R_getDataById($q['inv_room']),
    'r_owner'       => AR_getDataById('ref_owners', $q['inv_owner']),
    'r_status'      => AR_getDataById('ref_status', $q['inv_status']),
    'r_id'          => $new_id
);

$jresult = array(
    'state' => 'added',
    'error' => 0,
    'new_id' => $new_id,
    'message' => 'Добавлено: ',
    'data' => $data
);


print(json_encode($jresult));