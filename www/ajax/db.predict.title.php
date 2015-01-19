<?php
require_once('../core/core.php');
require_once('../core/core.db.php');

$table = $CONFIG['main_data_table'];
$table_prefix = $CONFIG['tableprefix'];
$table = (strpos( $table , $table_prefix) == false ) ? $table_prefix.$table : $table;


$link = ConnectDB();
$data = array();

$title = isset($_GET['title']) ? $_GET['title'] : '';

$query = "SELECT DISTINCT cost_float, dbtitle, mytitle, comment FROM {$table}
WHERE mytitle LIKE '%".$title."%' AND dbtitle != '' ";

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
        .link-like {
            text-decoration: underline;
            color: #00008b;
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

<?php if ($nr > 0) { ?>

<table border="1" class="table_title_prediction">
    <caption>Выберите вариант названия объекта<br>
    <span>'<?=$title?>'</span></caption>
    <tr>
        <th>Название<br>в базе</th>
        <th>Название<br>в описи</th>
        <th class="price">Цена (руб.)</th>
        <th>Комментарий</th>
    </tr>

<?php foreach ($rows as $i => $row) { ?>

    <tr>
        <td><strong class="action-insert-this-to-dbtitle-input link-like"><?=$row['dbtitle']?></strong></td>
        <td class="mytitle"><span><?=$row['mytitle']?></span></td>
        <td class="price"><span><?=$row['cost_float']?></span></td>
        <td class="comment"><span><?=$row['comment']?></span></td>
    </tr>

<?php } ?>

</table>

<?php } else { ?>

<div class="warning">Возможных объектов в базе данных не найдено!</div>

<?php } ?>

</div>
</body>
</html>