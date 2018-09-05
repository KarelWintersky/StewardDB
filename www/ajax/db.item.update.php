<?php
require_once '../core/__required.php';

$id = mysqli_escape_string($mysqli, $_GET['inv_id']);
$q = array(
    'inv_code'  => mysqli_escape_string($mysqli, $_GET['inv_code']),
    'mytitle'   => mysqli_escape_string($mysqli, $_GET['inv_mytitle']),
    'dbtitle'   => mysqli_escape_string($mysqli, $_GET['inv_dbtitle']),
    'room'      => mysqli_escape_string($mysqli, $_GET['inv_room']),
    'status'    => mysqli_escape_string($mysqli, $_GET['inv_status']),
    'cost_float' => mysqli_escape_string($mysqli, $_GET['inv_price'] ?? ''), // cost!
    'owner'     => mysqli_escape_string($mysqli, $_GET['inv_owner']),
    'comment'   => mysqli_escape_string($mysqli, $_GET['inv_comment'] ?? ''),
    'date_income_str' => mysqli_escape_string($mysqli, $_GET['inv_date_income_str']),
);
$q['date_income_ts'] = ConvertDateToTimestamp($q['date_income_str']);
$q['date_income'] = Date('Y-m-d', $q['date_income_ts'] );

$qstr = MakeUpdate($q, $main_data_table, "WHERE id = {$id}");

$res = mysqli_query($mysqli, $qstr) or Die("Unable to update data in DB! ".$qstr);

$data = array(
    'r_code' => $q['inv_code'],
    'r_mytitle' => $q['mytitle'],
    'r_dbtitle' => $q['dbtitle'],
    'r_comment' => $q['comment'],
    'r_date_income_str' => $q['date_income_str'],
    'r_date_income_ts' => $q['date_income_ts'],
    'r_room' => R_getDataById($q['room']),
    'r_owner' => AR_getDataById('ref_owners', $q['owner']),
    'r_status' => AR_getDataById('ref_status', $q['status']),
    'r_id'      => $id
);

$jresult = array(
    'state' => 'updated',
    'error' => 0,
    'new_id' => $id,
    'message' => 'Обновлено: ',
    'data' => $data
);


print(json_encode($jresult));
