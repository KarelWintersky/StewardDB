<?php
$table = 'export_csv';
require_once('../core/core.php');
require_once('../core/core.db.php');
require_once('../core/core.reports.php');

$report_requested = IsSet($_GET['report']) ? $_GET['report'] : '';
$filename = Isset($_GET['filename']) ? $_GET['filename'] : 'report.csv';

$n = 1;
$csv_array = array();

$link = ConnectDB();


switch ($report_requested){
    case 'get_items_not_in_rooms' : {
        // db request
        $q = "SELECT * from {$table} where inv_room = 0";
        $r = mysql_query($q);

        // цикл загрузки данных
        if (  ($r !== FALSE) && (@mysql_num_rows($r)>0)  ) {
            while ($row = mysql_fetch_assoc($r)) {
                // склейка
                $csv_array[] = array_merge( array( '№' => $n ) , array(
                    'Название'              => $row['inv_dbtitle'],
                    'Инвентарный номер'     => $row['inv_code'],
                    'Дата принятия к учету' => $row['inv_date_income_str']
                ));
                $n++;
            }
        }
        break;
    }
    case 'get_items_are_in_rooms': {
        // db request
        $q = "select inv_dbtitle, inv_mytitle, inv_code, inv_date_income_str, rooms.room_name , inv_comment
from export_csv, rooms
where inv_room != 0	AND inv_room = rooms.id
ORDER BY rooms.room_group, rooms.room_name, inv_dbtitle";
        $r = mysql_query($q);

        // цикл загрузки данных
        if (  ($r !== FALSE) && (@mysql_num_rows($r)>0)  ) {
            while ($row = mysql_fetch_assoc($r)) {
                // склейка
                $csv_array[] = array_merge( array( '№' => $n ) , array(
                    'Название в 1С'              => $row['inv_dbtitle'],
                    'Название в описи'           => $row['inv_mytitle'],
                    'Инвентарный номер'          => $row['inv_code'],
                    'Помещение'                  => $row['room_name'],
                    'Дата принятия к учету'      => $row['inv_date_income_str'],
                    'Комментарий'                  => $row['inv_comment'],
                ));
                $n++;
            }
        }


        break;
    }
    case '': {
        break;
    }
}; // switch

CloseDB($link);

$filecontent =  array2csv($csv_array);

download_send_headers($filename, strlen($filecontent));
echo $filecontent;

exit();
?>