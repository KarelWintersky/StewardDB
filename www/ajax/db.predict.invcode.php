<?php
require_once '../core/__required.php';

$data = array();

$ic = mysqli_escape_string($mysqli, $_GET['code']);
$prefix = getTablePrefix();

$query = "
SELECT DISTINCT 
    {$main_data_table}.id, 
    inv_code, 
    cost_float, 
    dbtitle, 
    {$prefix}rooms.room_name, 
    {$prefix}ref_status.data_str AS current_status
FROM 
    {$main_data_table}, 
    {$prefix}rooms, 
    {$prefix}ref_status
WHERE 
    inv_code LIKE '%{$ic}%'
AND
    {$prefix}rooms.id = room
AND
    {$prefix}ref_status.id = status
ORDER BY 
    inv_code, room_name, dbtitle";

$rq = mysqli_query($mysqli, $query) or die("Ошибка в запросе: ".$query);
$nr = @mysqli_num_rows($rq);

while ($r = mysqli_fetch_assoc($rq)) {
    $rows[ $r['id'] ] = $r;
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
            <td><?=$row['id']?></td>
            <td class="normal">
                <strong class="link-like action-search-for-this-invcode"><?=$row['inv_code']?></strong>
            </td>
            <td>
                <?=$row['dbtitle']?>
            </td>
            <td class="price">
                <?=$row['cost_float']?>
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
</body>
</html>