<?php
require_once('core.config.php');

function ConnectDB()
{
    global $CONFIG;
    $link = mysql_connect($CONFIG['hostname'], $CONFIG['username'], $CONFIG['password']);
    mysql_select_db($CONFIG['database'], $link) or die("Could not select db: " . mysql_error());
    mysql_query("SET NAMES utf8", $link);
    return $link;
}

function CloseDB($link) // useless
{
    mysql_close($link) or Die("Не удается закрыть соединение с базой данных.");
}

function isConnectedDB()
{
    global $CONFIG;
    return $CONFIG['flag_dbconnected'];
}

function MakeInsert($arr, $table, $where="")
{
    $str = "INSERT INTO $table ";

    $keys = "(";
    $vals = "(";
    foreach ($arr as $key => $val) {
        $keys .= $key . ",";
        $vals .= "'".$val."',";
    }
    $str .= trim($keys,",") . ") VALUES " . trim($vals,",") . ") ".$where;
    return $str;
}

function MakeUpdate($arr,$table,$where="")
{
    $str = "UPDATE $table SET ";
    foreach ($arr as $key=>$val)
    {
        $str.= $key."='".$val."', ";
    };
    $str = substr($str,0,(strlen($str)-2)); // обрезаем последнюю ","
    $str.= " ".$where;
    return $str;
}

function DBIsTableExists($table)
{
    return (mysql_query("SELECT 1 FROM $table WHERE 0")) ? true : false;
}

function throw_ex($er){
    throw new Exception($er);
}


?>