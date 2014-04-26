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
        $date_array = date_parse($str_date);
    }
    return mktime(12, 0, 0, $date_array['month'], $date_array['day'], $date_array['year']);
}

/*
конвертирует дату из человекопонятного представления в массив
*/
function ConvertDateToArray($str_date)
{
    if (function_exists('date_parse_from_format')) {
        $date_as_array = date_parse_from_format('d/m/Y',$str_date);
    } else {
        $date_as_array = date_parse($str_date);
    }
    return $date_as_array;
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
function getQuery($get, $table='')
{
    global $CONFIG;
    if ($table == '') $table = $CONFIG['main_data_table'];

    $select = "
    SELECT
  {$table}.id AS i_id
, {$table}.inv_code AS i_code
, {$table}.mytitle AS i_mt
, {$table}.dbtitle AS i_dt
, DATE_FORMAT({$table}.date_income, '%d.%m.%Y') AS i_di
, {$table}.comment AS i_comment
, ref_owners.data_str AS r_owner
, ref_status.data_str AS r_status
, rooms.room_name AS r_room
, {$table}.cost_float AS i_cost
, {$table}.owner AS i_owner
, {$table}.room AS i_room
, {$table}.status AS i_status
";

    $from = " FROM
    {$table}, rooms, ref_owners, ref_status ";

    $where = "
    WHERE
{$table}.is_deleted = 0
AND ref_owners.id = {$table}.owner
AND ref_status.id = {$table}.status
AND rooms.id = {$table}.room ";

    $go = "
    ORDER BY room, dbtitle, {$table}.id ";

    $family = retVal($get['family']);
    $subfamily = retVal($get['subfamily']);

    if ($family != 0) {
        $select .= " , ref_family.data_str AS r_family ";
        $where .= " AND ref_family.id = {$table}.family ";
        $where .= " AND {$table}.family = {$family}";
        $from .= " , ref_family ";

        if ($subfamily != 0) {
            $select .= " , ref_subfamily.data_str AS r_subfamily ";
            $where .= " AND ref_subfamily.id = {$table}.subfamily ";
            $where .= " AND {$table}.subfamily = {$subfamily}";
            $from .= " , ref_subfamily ";
        }
    }

    $where .= (retVal($get['room']) != '0' ) ? " AND {$table}.room = {$get['room']} " : " ";
    $where .= (retVal($get['status']) != '0' ) ? " AND {$table}.status = {$get['status']} " : " ";
    $where .= (retVal($get['owner']) != '0' ) ? " AND {$table}.owner = {$get['owner']} " :  "";
    // get id for unique request
    $where .= (retVal($get['id']) != '0' ) ? " AND {$table}.id = {$get['id']} " : " ";

    return $select . $from . $where . $go;
}

function AR_getDataById($ref, $id)
{
    $q = "SELECT data_str FROM {$ref} WHERE id = {$id}";
    $r = mysql_query($q);
    if (@mysql_num_rows($r) > 0) {
        $ret = mysql_fetch_assoc($r);
    }
    return $ret['data_str'];
}

function R_getDataById($id)
{
    $q = "SELECT room_name FROM rooms WHERE id = {$id}";
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