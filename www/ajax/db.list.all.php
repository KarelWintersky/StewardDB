<?php
require_once('../core/core.php');
require_once('../core/core.db.php');
$table = $CONFIG['main_data_table'];

$get_total_cost = retVal($_GET['get_total_cost'], 0);

if ( $_GET['status'] == 2 ) $get_total_cost = 1;

$query = getQuery($_GET, $table);

$link = ConnectDB();

$qr = mysql_query($query) or die('error: '.$query);
$nr = @mysql_num_rows($qr);

$rows = array();

$total_cost = 0; $total_loaded = 0;

if ($nr > 0) {
    while ($r = mysql_fetch_assoc($qr)) {
        $r['i_title'] = ($r['i_dt'] != '') ? $r['i_dt'] : $r['i_mt'];
        $rows [] = $r;
        $total_cost += ($get_total_cost != 0) ? 1*$r['i_cost'] : 0;
        $total_loaded++;
    }
}
/* stripcslashes($row['i_comment']) */
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
    .price {
        color: blue;
        font-style: italic;
        font-weight: bold;
    }
</style>

<div id="list_items_wrapper">

<?php
    if ($nr > 0) {
        ?>
        <div>Всего загружено: <?=$total_loaded?></div>
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
                    <br>
                    <small>Нажмите на содержимое ячейки для подробностей</small>
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
            </tr>

<?php foreach ($rows as $i => $row) { ?>

            <tr>
                <td class="td-center">
                    <span><?=$row['i_id']?> </span>
                </td>
                <td class="td-center">
                    &nbsp;<?=$row['i_code']?>&nbsp;
                </td>
                <td class="td-hover-link-like action-show-extended-info-for-id" data-id="<?=$row['i_id']?>">
                    <span class="link-like" title="Редактировать!">
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
<?php }
            if ($get_total_cost != 0) {  ?>
<tr>
    <td colspan="3">Итоговая стомость списанных объектов: </td>
    <td colspan="5"><span class="price"><?=$total_cost?></span></td>
</tr>

<?php } ?>
        </table>
        <?php
//@todo: одно из этих полей надо убрать - выводить полную стоимость списанных объектов ИЛИ
// в таблице, или ниже таблицы отдельно. Непонятно!
        if ($get_total_cost != 0) {
            ?><hr>
            Итоговая стоимость списанных объектов: <span class="price"><?=$total_cost?></span>
            <?php
        }
    } else {
        ?>

        <div class="warning">Возможных объектов в базе данных не найдено!</div>

        <?php
    }
    ?>
</div>