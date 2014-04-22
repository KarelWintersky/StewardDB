<?php
/*
http://www.site-do.ru/db/sql14.php


*/

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
    if ($table == '') $table = 'export_csv';

    $select = "
    SELECT
  {$table}.inv_id AS i_id
, {$table}.inv_code AS i_code
, {$table}.inv_mytitle AS i_mt
, {$table}.inv_dbtitle AS i_dt
, DATE_FORMAT({$table}.inv_date_income, '%d.%m.%Y') AS i_di
, {$table}.inv_comment AS i_comment
, ref_owners.data_str AS r_owner
, ref_status.data_str AS r_status
, rooms.room_name AS r_room
, {$table}.inv_cost_float AS i_cost
";

    $from = " FROM
    {$table}, rooms, ref_owners, ref_status ";

    $where = "
    WHERE
ref_owners.id = {$table}.inv_owner
AND ref_status.id = {$table}.inv_status
AND rooms.id = {$table}.inv_room ";

    $go = "
    ORDER BY inv_room, inv_dbtitle, inv_id ";

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

    $where .= (retVal($get['room']) != '0' ) ? " AND {$table}.inv_room = {$get['room']} " : " ";
    $where .= (retVal($get['status']) != '0' ) ? " AND {$table}.inv_status = {$get['status']} " : " ";
    $where .= (retVal($get['owner']) != '0' ) ? " AND {$table}.inv_owner = {$get['owner']} " :  "";
    // get id for unique request
    $where .= (retVal($get['id']) != '0' ) ? " AND {$table}.inv_id = {$get['id']} " : " ";

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


?>