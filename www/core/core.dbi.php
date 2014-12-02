<?php
class dbConfig {
    public static $CFG = array(
        'hostname' => array(
            'local' => 'localhost',
            'pfrf' => 'localhost',
            'sweb' => 'localhost',
            'phoenix'   =>  'localhost'
        ),
        'username' => array(
            'local' => 'root',
            'pfrf' => 'arris',
            'sweb' => 'opu_stewarddb',
            'phoenix'   =>  'arris'
        ),
        'password' => array(
            'local' => '',
            'pfrf' => 'nopasswordisset',
            'sweb' => 'password',
            'phoenix'   =>  'Nopa$$word1sset'
        ),
        'database' => array(
            'local' => 'stewarddb',
            'pfrf' => 'stewarddb',
            'sweb' => 'opuru_stewarddb',
            'phoenix'   =>  'phoenix'
        ),
        'basepath' => array(
            'local' => '',
            'pfrf'  => '/stewarddb',
            'sweb'	=> '',
            'phoenix'   =>  '/stewarddb'
        ),
        'table_prefix'  => array(
            'local' => '',
            'pfrf'  => '',
            'sweb'	=> '',
            'phoenix'   =>  ''
        )
    );
    public static $remote_hosting_keyname = 'phoenix';
}

class dbi extends dbConfig
{
    public function __construct()
    {
        $hostname = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? self::$CFG['hostname']['local']     : self::$CFG['hostname'][ self::$remote_hosting_keyname ];
        $username = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? self::$CFG['username']['local']     : self::$CFG['username'][ self::$remote_hosting_keyname ];
        $password = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? self::$CFG['password']['local']     : self::$CFG['password'][ self::$remote_hosting_keyname ];
        $database = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? self::$CFG['database']['local']     : self::$CFG['database'][ self::$remote_hosting_keyname ];

        // коннект к базе
    }

    public function get_backup_tables($host, $user, $pass, $name, $tables = '*')
    {
        // предполагается что мы уже соединились
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
    }


}


?>