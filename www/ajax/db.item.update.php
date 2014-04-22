<?php

$table = 'export_csv';
require_once('../core/core.php');
require_once('../core/core.db.php');

$id = mysql_escape_string($_GET['inv_id']);

$q = array(
    'inv_code' => mysql_escape_string($_GET['inv_code']),
    'inv_mytitle' => mysql_escape_string($_GET['inv_mytitle']),
    'inv_dbtitle' => mysql_escape_string($_GET['inv_dbtitle']),
    'inv_room' => mysql_escape_string($_GET['inv_room']),
    'inv_status' => mysql_escape_string($_GET['inv_status']),
    'inv_owner' => mysql_escape_string($_GET['inv_owner']),
    'inv_comment' => mysql_escape_string($_GET['inv_comment']),
    'inv_date_income_str' => mysql_escape_string($_GET['inv_date_income_str']),
    'inv_date_income_ts' => ConvertDateToTimestamp(mysql_escape_string($_GET['inv_date_income_str'])),
);
$q['inv_date_income'] = Date('Y-m-d', $q['inv_date_income_ts'] );

$link = ConnectDB();

$qstr = MakeUpdate($q, $table, "WHERE inv_id = {$id}");
$res = mysql_query($qstr, $link) or Die("Unable to update data in DB! ".$qstr);

$data = array(
    'r_code' => $q['inv_code'],
    'r_mytitle' => $q['inv_mytitle'],
    'r_dbtitle' => $q['inv_dbtitle'],
    'r_comment' => $q['inv_comment'],
    'r_date_income_str' => $q['inv_date_income_str'],
    'r_date_income_ts' => $q['r_date_income_ts'],
    'r_room' => R_getDataById($q['inv_room']),
    'r_owner' => AR_getDataById('ref_owners', $q['inv_owner']),
    'r_status' => AR_getDataById('ref_status', $q['inv_status']),
    'r_id'      => $id
);

$jresult = array(
    'state' => 'updated',
    'error' => 0,
    'new_id' => $id,
    'message' => 'Обновлено: ',
    'data' => $data
);


CloseDB($link);
print(json_encode($jresult));
?>