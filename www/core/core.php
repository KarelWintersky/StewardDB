<?php
require_once('jsonwrapper/jsonwrapper.php');

/*
конвертирует дату из человекопонятного представления в метку времени
*/
function ConvertDateToTimestamp($str_date, $format="d/m/Y")
{
    if (function_exists('date_parse_from_format')) {
        $date_array = date_parse_from_format('d.m.Y',$str_date);
    } else {
        if (function_exists('date_parse')) {
            $date_array = date_parse($str_date);
        } else {
            // date_parse not implemented
            $date_array_pre = getdate(strtotime($str_date));
            $date_array['month']    = $date_array_pre['mon'];
            $date_array['day']      = $date_array_pre['mday'];
            $date_array['year']     = $date_array_pre['year'];
        }

    }
    return mktime(12, 0, 0, $date_array['month'], $date_array['day'], $date_array['year']);
}

/*
конвертирует дату из человекопонятного представления в массив
*/
function ConvertDateToArray($str_date)
{
    if (function_exists('date_parse_from_format')) {
        $date_array = date_parse_from_format('d.m.Y',$str_date);
    } else {
        if (function_exists('date_parse')) {
            $date_array = date_parse($str_date);
        } else {
            // date_parse not implemented
            $date_array_pre = getdate(strtotime($str_date));
            $date_array['month']    = $date_array_pre['mon'];
            $date_array['day']      = $date_array_pre['mday'];
            $date_array['year']     = $date_array_pre['year'];
            $date_array['hour']     = 12;
            $date_array['minute']   = 0;
            $date_array['second']   = 0;
        }
    }
    return $date_array;
}

/*
конвертирует "человекопонятную" дату в MYSQL-формат
*/
function convertUserDateToSQLDate($string)
{
    Date('Y-m-d', ConvertDateToTimestamp($string) );
}

/*
возвращает значение переменной (из GET-а) или значение по умолчанию, если там пусто или её нет
*/
function retVal($value, $default=0)
{
    return (isset($value) && $value != '') ? $value : $default;
}

/*
строит универсальный запрос на основе GET'а (в лист)
*/
function getQuery($get, $a_table='')
{
    global $CONFIG;
    $t_prefix = getTablePrefix();
    $table = (strpos( $a_table , $t_prefix) == false ) ? $t_prefix.$a_table : $a_table;

    // $table = ($a_table == '') ? $CONFIG['main_data_table'] : $a_table;

    $select = "
    SELECT
  {$table}.id AS i_id
, {$table}.inv_code AS i_code
, {$table}.mytitle AS i_mt
, {$table}.dbtitle AS i_dt
, DATE_FORMAT({$table}.date_income, '%d.%m.%Y') AS i_di
, {$table}.comment AS i_comment
, {$t_prefix}ref_owners.data_str AS r_owner
, {$t_prefix}ref_status.data_str AS r_status
, {$t_prefix}rooms.room_name AS r_room
, {$table}.cost_float AS i_cost
, {$table}.owner AS i_owner
, {$table}.room AS i_room
, {$table}.status AS i_status
";

    /* $from = " FROM
    {$table} LEFT JOIN ref_status ON {$table}.status = ref_status.id
    , rooms, ref_owners  "; */
    /* $where = "
    WHERE
ref_owners.id = {$table}.owner
AND ref_status.id = {$table}.status
AND rooms.id = {$table}.room "; */

    // Правильный запрос с join'ами
    $from = " FROM
    {$table}
    LEFT JOIN {$t_prefix}ref_status    ON {$table}.status = {$t_prefix}ref_status.id
    LEFT JOIN {$t_prefix}rooms         ON {$table}.room = {$t_prefix}rooms.id
    LEFT JOIN {$t_prefix}ref_owners    ON {$table}.owner = {$t_prefix}ref_owners.id
    ";

    $where = " WHERE ";

    // get by 'is deleted' for deleted list request
    $where .= (retVal($get['is_deleted']) != '0' ) ? " {$table}.is_deleted = 1 " : " {$table}.is_deleted = 0 ";

    $family = retVal($get['family']);
    $subfamily = retVal($get['subfamily']);

    if ($family != 0) {
        $select .= " , {$t_prefix}ref_family.data_str AS r_family ";
        $where .= " AND {$t_prefix}ref_family.id = {$table}.family ";
        $where .= " AND {$table}.family = {$family}";
        $from .= " , {$t_prefix}ref_family ";

        if ($subfamily != 0) {
            $select .= " , {$t_prefix}ref_subfamily.data_str AS r_subfamily ";
            $where .= " AND {$t_prefix}ref_subfamily.id = {$table}.subfamily ";
            $where .= " AND {$table}.subfamily = {$subfamily}";
            $from .= " , {$t_prefix}ref_subfamily ";
        }
    }

    $where .= (retVal($get['room']) != '0' ) ? " AND {$table}.room = {$get['room']} " : " ";
    $where .= (retVal($get['status']) != '0' ) ? " AND {$table}.status = {$get['status']} " : " ";
    $where .= (retVal($get['owner']) != '0' ) ? " AND {$table}.owner = {$get['owner']} " :  "";
    // get by id for unique request
    $where .= (retVal($get['id']) != '0' ) ? " AND {$table}.id = {$get['id']} " : " ";

    $go = "
    ORDER BY room, dbtitle, {$table}.id ";

    return $select . $from . $where . $go;
}

function AR_getDataById($ref, $id)
{
    $t_prefix = getTablePrefix();
    $q = "SELECT data_str FROM {$t_prefix}{$ref} WHERE id = {$id}";
    $r = mysql_query($q);
    if (@mysql_num_rows($r) > 0) {
        $ret = mysql_fetch_assoc($r);
    }
    return $ret['data_str'];
}

function R_getDataById($id)
{
    $t_prefix = getTablePrefix();
    $q = "SELECT room_name FROM {$t_prefix}rooms WHERE id = {$id}";
    $r = mysql_query($q);
    if (@mysql_num_rows($r) > 0) {
        $ret = mysql_fetch_assoc($r);
    }
    return $ret['room_name'];
}

function ConvertToHumanBytes($size) {
    $filesizename = array(" Bytes", " K", " M", " G", " T", " P", " E", " Z", " Y");
    return $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 0) . $filesizename[$i] : '0 Bytes';
}

/**
 * @param $url
 */
function Redirect($url)
{
    if (headers_sent() === false) header('Location: '.$url);
}


/**
 * @param bool $debugmode
 * @return bool
 */
function isAjaxCall($debugmode=false)
{
    $debug = (isset($debugmode)) ? $debugmode : false;
    return ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) || ($debug);
}



?>