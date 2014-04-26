<?php
require_once('../core/core.php');
require_once('../core/core.db.php');
require_once('../core/core.reports.php');
$table = $CONFIG['main_data_table'];

$report_requested = IsSet($_GET['report']) ? $_GET['report'] : '';
$filename = Isset($_GET['filename']) ? $_GET['filename'] : 'report.csv';

$n = 1;
$csv_array = array();

$link = ConnectDB();


switch ($report_requested){
    case 'get_items_not_in_rooms' : {
        // db request
        $q = "SELECT * from {$table} where room = 0";
        $r = mysql_query($q);

        // цикл загрузки данных
        if (  ($r !== FALSE) && (@mysql_num_rows($r)>0)  ) {
            while ($row = mysql_fetch_assoc($r)) {
                // склейка
                $csv_array[] = array_merge( array( '№' => $n ) , array(
                    'Название'              => $row['dbtitle'],
                    'Инвентарный номер'     => $row['code'],
                    'Дата принятия к учету' => $row['date_income_str']
                ));
                $n++;
            }
        }
        break;
    }
    case 'get_items_are_in_rooms': {
        // db request
        $q = "select dbtitle, mytitle, inv_code, date_income_str, rooms.room_name , comment
from {$table}, rooms
where room != 0 AND room = rooms.id
ORDER BY rooms.room_group, rooms.room_name, dbtitle";
        $r = mysql_query($q);

        // цикл загрузки данных
        if (  ($r !== FALSE) && (@mysql_num_rows($r)>0)  ) {
            while ($row = mysql_fetch_assoc($r)) {
                // склейка
                $csv_array[] = array_merge( array( '№' => $n ) , array(
                    'Название в 1С'              => $row['dbtitle'],
                    'Название в описи'           => $row['mytitle'],
                    'Инвентарный номер'          => $row['code'],
                    'Помещение'                  => $row['room_name'],
                    'Дата принятия к учету'      => $row['date_income_str'],
                    'Комментарий'                  => $row['comment'],
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