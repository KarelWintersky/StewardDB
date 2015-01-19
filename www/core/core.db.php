<?php
require_once('config/core.config.php');

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

function getTablePrefix()
{
    global $CONFIG;
    return $CONFIG['tableprefix'];
}

function MakeInsert($arr, $table, $where="")
{
    $table_prefix = getTablePrefix();
    $real_table = (strpos( $table , $table_prefix) == false ) ? $table_prefix.$table : $table;

    $str = "INSERT INTO {$real_table} ";

    $keys = "(";
    $vals = "(";
    foreach ($arr as $key => $val) {
        $keys .= $key . ",";
        $vals .= "'".$val."',";
    }
    $str .= trim($keys,",") . ") VALUES " . trim($vals,",") . ") ".$where;
    return $str;
}

function MakeUpdate($arr, $table, $where="")
{
    $table_prefix = getTablePrefix();
    $real_table = (strpos( $table , $table_prefix) == false ) ? $table_prefix.$table : $table;

    $str = "UPDATE {$real_table} SET ";

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
    $real_table = getTablePrefix() . $table;
    return (mysql_query("SELECT 1 FROM {$real_table} WHERE 0")) ? true : false;
}

function throw_ex($er){
    throw new Exception($er);
}

/* backup the db OR just a table */
function get_backup_tables($host, $user, $pass, $name, $tables = '*')
{

    $link = mysql_connect($host, $user, $pass);
    mysql_select_db($name,$link);

    //get all of the tables
    if($tables == '*')
    {
        $tables = array();
        $result = mysql_query('SHOW TABLES');
        while($row = mysql_fetch_row($result))
        {
            $tables[] = $row[0];
        }
    }
    else
    {
        $tables = is_array($tables) ? $tables : explode(',',$tables);
    }
    $return = '';

    //cycle through
    foreach($tables as $table)
    {
        $result = mysql_query('SELECT * FROM '.$table);
        $num_fields = mysql_num_fields($result);

        $return .= 'DROP TABLE '.$table.';';
        $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
        $return .= "\n\n".$row2[1].";\n\n";

        for ($i = 0; $i < $num_fields; $i++)
        {
            while($row = mysql_fetch_row($result))
            {
                $return.= 'INSERT INTO '.$table.' VALUES(';
                for($j=0; $j<$num_fields; $j++)
                {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n","\\n",$row[$j]);
                    if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                    if ($j<($num_fields-1)) { $return.= ','; }
                }
                $return.= ");\n";
            }
        }
        $return.="\n\n\n";
    }
    return $return;

    //save file
    /*
    $filename = 'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql';
    $handle = fopen($filename, 'w+');
    fwrite($handle,$return);
    fclose($handle);
    return $filename;
    */
}


?>