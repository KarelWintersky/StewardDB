<?php
$table = 'export_csv';
require_once('../core/core.php');
require_once('../core/core.db.php');

$link = ConnectDB();
$data = array();

$title = isset($_GET['title']) ? $_GET['title'] : '';

$query = "SELECT DISTINCT inv_cost_float, inv_dbtitle, inv_mytitle, inv_comment FROM export_csv
WHERE inv_mytitle LIKE '%".$title."%' AND inv_dbtitle != '' ";

if ($title != '') {
    $rq = mysql_query($query) or die("Ошибка в запросе: ".$query);
    $nr = @mysql_num_rows($rq);
    if ($nr > 0) {
        while ($r = mysql_fetch_assoc($rq)) {
            $rows [] = $r;
        }
    }
} else {
    $nr = 0;
}
?>
<html>
<head>
    <style type="text/css">
        #title_prediction_wrapper {
            width: 700px;
            margin: 1px;
            padding: 0;
            max-height: 700px;
            overflow: scroll;
        }
        .display-block {
            display: block;
        }
        .display-table {
            display: table;
            height: 200px;
        }

        #title_prediction_wrapper > .warning {
            vertical-align: middle;
            text-align: center;
            color: red;
            font-size: large;
            height: 100%;
            display: table-cell;
        }

        /* table */
        .table_title_prediction {
            margin: 1em;
        }
        .table_title_prediction caption {
            padding: 0;
            font-size: 125%;
            color: navy;
            font-weight: bold;
            padding-bottom: 1em;
        }
        .table_title_prediction caption > span {
            color: maroon;
        }

        .table_title_prediction > span, > strong {
            padding: 5px;
        }
        .title_prediction .mytitle {
            color: #006400;
        }
        .table_title_prediction .price {
            color: red;
            text-align: center;
        }
        .table_title_prediction .comment {
            font-size: small;
            color: gray;
        }
    </style>
    <script type="text/javascript">
        var _nr = <?=$nr?>;
        var _class = (_nr > 0) ? 'display-block' : 'display-table';
        $("#title_prediction_wrapper").addClass( _class );
    </script>
</head>
<body>
<div id="title_prediction_wrapper">
<?php
if ($nr > 0) {
    ?>

<table border="1" class="table_title_prediction">
    <caption>Выберите вариант названия объекта<br>
    <span>'<?=$title?>'</span></caption>
    <tr>
        <th>Название<br>в базе</th>
        <th>Название<br>в описи</th>
        <th class="price">Цена (руб.)</th>
        <th>Комментарий</th>
    </tr>

<?php
foreach ($rows as $i => $row) {
echo <<<ANY_ROW
    <tr>
        <td><strong class="action-insert-this-to-dbtitle-input link-like">{$row['inv_dbtitle']}</strong></td>
        <td class="mytitle"><span>{$row['inv_mytitle']}</span></td>
        <td class="price"><span>{$row['inv_cost_float']}</span></td>
        <td class="comment"><span>{$row['inv_comment']}</span></td>
    </tr>
ANY_ROW;
}
?>
</table>
<?php

} else {

?>
<div class="warning">Возможных объектов в базе данных не найдено!</div>

<?php
}
?>
</div>
</body>
</html>