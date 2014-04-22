<?php

$table = 'export_csv';
require_once('../core/core.php');
require_once('../core/core.db.php');

$link = ConnectDB();
$data = array();

$ic = mysql_escape_string($_GET['code']);

// if ($ic === '') die(); //@todo: remove at deploy

$query = "SELECT DISTINCT inv_id, inv_code, inv_cost_float, inv_dbtitle, rooms.room_name, ref_status.data_str AS current_status
FROM export_csv, rooms, ref_status
WHERE inv_code LIKE '%{$ic}%'
AND
rooms.id = inv_room
AND
ref_status.id = inv_status
ORDER BY inv_code, room_name, inv_dbtitle";

$rq = mysql_query($query) or die("Ошибка в запросе: ".$query);
$nr = @mysql_num_rows($rq);

while ($r = mysql_fetch_assoc($rq)) {
    $rows[ $r['inv_id'] ] = $r;
}

?>

<html>
<head>
    <title><?=$nr?></title>
    <style type="text/css">
        #invcode_prediction_wrapper {
            min-width: 600px;
            max-height: 700px;
            overflow: scroll;
        }

        #invcode_prediction_wrapper > table caption {
            font-size: 125%;
            font-weight: bold;
            color: navy;
        }
        #table_invcode_prediction th {
            padding: 5px;
        }
        #table_invcode_prediction td.price {
            text-align: center;
        }
        #table_invcode_prediction td {
            font-size: small;
            padding-left: 10px;
            padding-right: 10px;
        }
        #table_invcode_prediction td.normal {
            font-size: inherit;
        }

    </style>
</head>

<body>
<div id="invcode_prediction_wrapper">

    <table border="1" id="table_invcode_prediction">
        <caption>Объекты с похожими инвентарными кодами '<?=$ic?>'</caption>

        <tr>
            <th>#</th>
            <th>Инв. код</th>
            <th>Название<br> в базе</th>
            <th>Цена</th>
            <th>Помещение</th>
            <th>Статус</th>
        </tr>

        <?php foreach ($rows as $i => $row) { ?>

        <tr>
            <td><?=$row['inv_id']?></td>
            <td class="normal">
                <strong class="link-like action-search-for-this-invcode"><?=$row['inv_code']?></strong>
            </td>
            <td>
                <?=$row['inv_dbtitle']?>
            </td>
            <td class="price">
                <?=$row['inv_cost_float']?>
            </td>
            <td class="normal">
                <?=$row['room_name']?>
            </td>
            <td>
                <?=$row['current_status']?>
            </td>
        </tr>

        <?php } ?>

    </table>



</div>

<!--
<?php
print_r($rows)
?>
-->

</body>
</html>