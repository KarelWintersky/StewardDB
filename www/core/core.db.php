<?php

function ConnectDB()
{
    $db_config = Config::get('database');

    $link = mysqli_connect($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database'], $db_config['port'])
    or die("Can't establish connection to '{$db_config['hostname']}' for user '{$db_config['username']}' at '{$db_config['database']}' database");

    mysqli_select_db($link, $db_config['database']) or die("Could not select db: " . mysqli_error($link));

    mysqli_query($link, "SET NAMES utf8");
    return $link;
}

function CloseDB($link) // useless
{
    mysqli_close($link) or Die("Не удается закрыть соединение с базой данных.");
}

function getTablePrefix()
{
    $db_config = Config::get('database');

    return $db_config['prefix'];
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
    global $mysqli;
    $real_table = getTablePrefix() . $table;
    return (mysqli_query($mysqli, "SELECT 1 FROM {$real_table} WHERE 0")) ? true : false;
}

/* backup the db OR just a table */
function get_backup_tables($host, $user, $pass, $name, $tables = '*')
{
    $mysqli = mysqli_connect($host, $user, $pass);
    mysqli_select_db($mysqli, $name);

    //get all of the tables
    if($tables == '*')
    {
        $tables = array();
        $result = mysqli_query($mysqli, 'SHOW TABLES');
        while($row = mysqli_fetch_row($result))
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
        $result = mysqli_query($mysqli, 'SELECT * FROM '.$table);
        $num_fields = mysqli_num_fields($result);

        $return .= 'DROP TABLE '.$table.';';
        $row2 = mysqli_fetch_row(mysqli_query($mysqli, 'SHOW CREATE TABLE '.$table));
        $return .= "\n\n".$row2[1].";\n\n";

        for ($i = 0; $i < $num_fields; $i++)
        {
            while($row = mysqli_fetch_row($result))
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


}

function redirectToLogin()
{
    header('Location: '.Config::get('basepath').'/entrance.php');
    die();
}

function isLogged()
{
    $we_are_logged = true;
    // вот тут мы проверямем куки и сессию на предмет "залогинились ли мы"
    $we_are_logged = $we_are_logged && isset($_SESSION);
    $we_are_logged = $we_are_logged && !empty($_SESSION);
    $we_are_logged = $we_are_logged && isset($_SESSION['u_id']);
    $we_are_logged = $we_are_logged && $_SESSION['u_id'] != -1;
    $we_are_logged = $we_are_logged && isset($_COOKIE['u_libdb_logged']);
    return $we_are_logged;
}

function DBLoginCheck($login, $password)
{
    global $mysqli;
    $real_table = getTablePrefix() . 'users';

    // возвращает массив с полями "error" и "message"
    // логин мы передали точно совершенно, мы это проверили в скрипте, а пароль может быть и пуст
    // а) логин не существует
    // б) логин существует, пароль неверен
    // в) логин существует, пароль верен

    $userlogin = mysqli_real_escape_string($mysqli, mb_strtolower($login));

    $q_login = "SELECT password, permissions , id FROM {$real_table} WHERE login = '$userlogin'";

    $r_login = mysqli_query($mysqli, $q_login) or die('Error '. $q_login);

    if (mysqli_num_rows($r_login)==1) {
        // логин существует
        $user = mysqli_fetch_assoc($r_login);
        if ($password == $user['password']) {
            // пароль верен
            $return = array(
                'error' => 0,
                'message' => 'User credentials correct! ',
                'id' => $user['id'],
                'permissions' => $user['permissions'],
                'url' => Config::get('basepath') .'/index.php'
            );
        } else {
            // пароль неверен
            $return = array(
                'error' => 1,
                'message' => 'Пароль не указан или неверен! Проверьте раскладку клавиатуры! '
            );
        }
    } else {
        // логин не существует
        $return = array(
            'error' => 2,
            'message' => 'Пользователь с логином '.$login.' в системе не обнаружен! '
        );
    }
    return $return;
}




