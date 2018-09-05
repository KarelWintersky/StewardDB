<?php
require_once '../core/__required.php';

$report_requested = $_GET['report'] ?? '';
$filename = $_GET['filename'] ?? 'report.csv';

$n = 1;
$csv_array = array();

switch ($report_requested){
    case 'get_items_not_in_rooms' : {
        // db request
        $q = "SELECT * from {$main_data_table} where room = 0";
        $r = mysqli_query($mysqli, $q);

        // цикл загрузки данных
        if (  ($r !== FALSE) && (@mysqli_num_rows($r)>0)  ) {
            while ($row = mysqli_fetch_assoc($r)) {
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
from {$main_data_table}, rooms
where room != 0 AND room = rooms.id
ORDER BY rooms.room_group, rooms.room_name, dbtitle";
        $r = mysqli_query($mysqli, $q);

        // цикл загрузки данных
        if (  ($r !== FALSE) && (@mysqli_num_rows($r)>0)  ) {
            while ($row = mysqli_fetch_assoc($r)) {
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

$filecontent =  array2csv($csv_array);

download_send_headers($filename, strlen($filecontent));
echo $filecontent;

exit();
