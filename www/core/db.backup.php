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

$backup = get_backup_tables($CONFIG['hostname'], $CONFIG['username'], $CONFIG['password'], $CONFIG['database']);

$name = $path.$fname;
if (function_exists('gzcompress')) {
    $name .= ".gz";
    $gz = gzopen($name, "wb9");
    gzwrite($gz, $backup);
    gzclose($gz);
} else {
    $name = $fname.".sql";
    file_put_contents($name, $backup);
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


