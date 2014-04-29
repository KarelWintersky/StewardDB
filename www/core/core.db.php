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

/* Backup MySQL Tables*/

function backup_tables($file, $host, $user, $pass, $name){
    $link = mysql_connect($host, $user, $pass);
    mysql_select_db($name, $link);
    mysql_query("SET NAMES utf8", $link);

    file_put_contents($file, '');

    //получение списка таблиц
    $tables = array();
    $result = mysql_query('SHOW TABLES;', $link);
    while($row = mysql_fetch_row($result)){
        $tables[] = $row[0];
    }

    //обработка таблиц
    if(count($tables)>0){
        foreach($tables as $table){
            backup_table_structure($file, $table, $link);
            backup_table_data($file, $table, $link);
        }
    }
}

function backup_table_structure($file, $table, $link){
    //получение и сохранение структуры таблицы
    $content = 'DROP TABLE IF EXISTS `'.$table."`;\n\n";
    $result = mysql_fetch_row(mysql_query('SHOW CREATE TABLE `'.$table.'`;', $link));
    $content .= $result[1].";\n\n";
    file_put_contents($file, $content, FILE_APPEND);
}

function backup_table_data($file, $table, $link){
    //получение и сохранение данных таблицы
    $result = mysql_fetch_row(mysql_query('SELECT COUNT(*) FROM `'.$table.'`;', $link));
    $count = $result[0];
    $delta = 500;

    //если данные существуют
    if($count>0){
        //определяем не числовые поля
        $not_num = array();
        $result = mysql_query('SHOW COLUMNS FROM `'.$table.'`;', $link);
        $start = 0;
        while($row = mysql_fetch_row($result)){
            if (!preg_match("/^(tinyint|smallint|mediumint|bigint|int|float|double|real|decimal|numeric|year)/", $row[1])) {
                $not_num[$start] = 1;
            }
            $start++;
        }
        //начинаем производить выборки данных
        $start = 0;
        while($count>0){
            $result = mysql_query('SELECT * FROM `'.$table.'` LIMIT '.$start.', '.$delta.';', $link);
            $content = 'INSERT INTO `'.$table.'` VALUES ';
            $first = true;
            while($row = mysql_fetch_row($result)){
                $content .= $first ? "\n(" : ",\n(";
                $first2 = true;
                foreach($row as $index=>$field){
                    if(isset($not_num[$index])){
                        $field = addslashes($field);
                        // $field = ereg_replace("\n", "\\n", $field);
                        $field = str_replace("\n", "\\n", $field);
                        $content .= !$first2 ? (',"'.$field.'"') : ('"'.$field.'"');
                    }else{
                        $content .= !$first2 ? (','.$field) : $field;
                    }
                    $first2 = false;
                }
                $content .= ')';
                $first = false;
            }
            //сохраняем результаты выборки
            file_put_contents($file, $content.";\n\n", FILE_APPEND);
            $count -= $delta;
            $start += $delta;
        }
    }
}


?>