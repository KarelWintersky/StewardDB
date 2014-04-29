<?php
require_once('../core/core.php');
require_once('../core/core.db.php');
$table = $CONFIG['main_data_table'];

$id = mysql_escape_string($_GET['id']);

$link = ConnectDB();

$jresult = array(
    'state' => 'done',
    'error' => 0,
);

try {
    $q = "DELETE FROM {$table} WHERE is_deleted = 1";
    $r = mysql_query($q) or throw_ex(mysql_error());
} catch (exception $e) {
    $jresult = array(
        'state' => 'error',
        'error' => mysql_error(),
    );
}

CloseDB($link);
print(json_encode($jresult));

?>