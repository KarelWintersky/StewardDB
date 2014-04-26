<?php
require_once('../core/core.php');
require_once('../core/core.db.php');
$table = $CONFIG['main_data_table'];

$id = mysql_escape_string($_GET['inv_id']);

$q = array(
    'inv_code' => mysql_escape_string($_GET['inv_code']),
    'mytitle' => mysql_escape_string($_GET['inv_mytitle']),
    'dbtitle' => mysql_escape_string($_GET['inv_dbtitle']),
    'room' => mysql_escape_string($_GET['inv_room']),
    'status' => mysql_escape_string($_GET['inv_status']),
    'cost_float' => mysql_escape_string($_GET['inv_price']),
    'owner' => mysql_escape_string($_GET['inv_owner']),
    'comment' => mysql_escape_string($_GET['inv_comment']),
    'date_income_str' => mysql_escape_string($_GET['inv_date_income_str']),
);
$q['date_income_ts'] = ConvertDateToTimestamp($q['date_income_str']);
$q['date_income'] = Date('Y-m-d', $q['date_income_ts'] );

$link = ConnectDB();

$qstr = MakeUpdate($q, $table, "WHERE id = {$id}");
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