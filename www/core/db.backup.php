<?php
require_once('core.php');
require_once('core.db.php');
require_once('core.kwt.php');
require_once('core.login.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) {
    redirectToLogin();
}

$file = "../_backup/sql_dump_".time().".sql";

backup_tables($file, $CONFIG['hostname'], $CONFIG['username'], $CONFIG['password'], $CONFIG['database']);

?>

<button onClick="window.location.href='/core/'">Назад</button>