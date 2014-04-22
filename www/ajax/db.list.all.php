<?php

$table = 'export_csv';
require_once('../core/core.php');
require_once('../core/core.db.php');

$get_total_cost = retVal($_GET['get_total_cost'], 0);

$query = getQuery($_GET, 'export_csv');

$link = ConnectDB();

$qr = mysql_query($query) or die('error: '.$query);
$nr = @mysql_num_rows($qr);

$rows = array();

$total_cost = 0;

if ($nr > 0) {
    while ($r = mysql_fetch_assoc($qr)) {
        $r['i_title'] = ($r['i_dt'] != '') ? $r['i_dt'] : $r['i_mt'];
        $rows [] = $r;
        $total_cost += ($get_total_cost != 0) ? 1*$r['i_cost'] : 0;
    }
}

CloseDB($link);

?>
<!-- [<?=$nr?>] : <?=$query?> -->
<style type="text/css">
    .table_items_list {
        width: 100%;
    }
    .td-small {
        font-size: small;
    }
    .td-center {
        text-align: center;
    }

    .warning {
        color: #cd0a0a;
        font-style: italic;
        font-size: large;
    }
</style>

<div id="list_items_wrapper">

<?php
    if ($get_total_cost != 0) {
?>
        Итоговая сумма : <?=$total_cost?><br>

        <?php
    }
    if ($nr > 0) {
        ?>

        <table border="1" class="table_items_list" id="exportable">
            <tr>
                <th>
                    (id)
                </th>
                <th>
                    Инв.код
                </th>
                <th>
                    Название
                </th>
                <th class="td-small">
                    Дата постановки<br>
                    на учет
                </th>
                <th>
                    Цена
                </th>
                <th>
                    Владелец
                </th>
                <th>
                    Статус
                </th>
                <th>
                    Помещение
                </th>
                <!-- <th>
                    *
                </th> -->
            </tr>

<?php foreach ($rows as $i => $row) { ?>

            <tr>
                <td class="td-center">
                    <button class="action-show-extended-info-for-id" data-id="<?=$row['i_id']?>"><?=$row['i_id']?></button>
                </td>
                <td class="td-center">
                    &nbsp;<?=$row['i_code']?>&nbsp;
                </td>
                <td>
                    <span title="<?=stripcslashes($row['i_comment'])?>">
                        <?=$row['i_title']?>
                    </span>
                </td>
                <td class="td-center">
                    <?=$row['i_di']?>
                </td>
                <td class="td-center">
                    <?=$row['i_cost']?>
                </td>
                <td class="td-small">
                    <?=$row['r_owner']?>
                </td>
                <td class="td-small">
                    <?=$row['r_status']?>
                </td>
                <td>
                    <?=$row['r_room']?>
                </td>
                <!-- <td class="td-center">
                    *edit*
                </td> -->

            </tr>
<?php } ?>
        </table>
        <?php
    } else {
        ?>

        <div class="warning">Возможных объектов в базе данных не найдено!</div>

        <?php
    }
    ?>
</div>