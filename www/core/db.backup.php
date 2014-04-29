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
$t = time();
$fname = "sql_dump_".$t;
$path = "../_backup/"; // $CONFIG['basepath']
// $file = .$fname.$fext;

backup_tables($path.$fname.".sql", $CONFIG['hostname'], $CONFIG['username'], $CONFIG['password'], $CONFIG['database']);

if (function_exists('gzcompress')) {
    $gz = gzopen($path.$fname.".gz", "wb9");
    gzwrite($gz, file_get_contents($path.$fname.".sql"));
    gzclose($gz);
    unlink($path.$fname.".sql");
    $name = $fname.".gz";
} else {
    $name = $fname.".sql";
}


?>
<html>
    <head>
        <title>Резервная копия базы StewardDB</title>
        <style type="text/css">
            button {
                height: 60px;
                width: 150px;
                font-size: large;
            }
        </style>
    </head>
    <body>
    Резервная копия базы сделана успешно.<br>
    Дата создания копии: <?php echo date("d/m/Y H:i:s (P)", $t )?> <br>
    Файл с резервной копией: <a href="<?=$CONFIG['basepath']?>/_backup/<?=$name?>"><?=$name?></a><br>
    <button onClick="window.location.href='<?=$CONFIG['basepath']?>/core/'">Назад</button>
    </body>
</html>


