<?php
require_once '../core/__required.php';

$query = getQuery($_GET, $main_data_table);

$qr = mysqli_query($mysqli, $query) or die('error: '.$query);
$nr = @mysqli_num_rows($qr);

$rows = array();

$total_cost = 0; $total_loaded = 0;

$get_total_cost = $_GET['get_total_cost'] ?? 0;

if ($nr > 0) {
    while ($r = mysqli_fetch_assoc($qr)) {
        $r['i_title'] = ($r['i_dt'] != '') ? $r['i_dt'] : $r['i_mt'];
        $rows [] = $r;
        $total_cost += ($get_total_cost != 0) ? 1*$r['i_cost'] : 0;
        $total_loaded++;
    }
}
/* stripcslashes($row['i_comment']) */
?>
<!-- [<?php echo $nr; ?>] : <?php echo $query; ?> -->
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
    if ($nr > 0) { ?>
        <div>Всего загружено: <?php echo $total_loaded; ?></div>
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
                    <small>Нажмите на содержимое ячейки для информации и восстановления</small>
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
                    <span><?php echo $row['i_id']; ?> </span>
                </td>
                <td class="td-center">
                    &nbsp;<?php echo $row['i_code']; ?>&nbsp;
                </td>
                <td class="td-hover-link-like action-show-extended-info-for-id" data-id="<?php echo $row['i_id']; ?>">
                    <span class="link-like" title="Редактировать!">
                        <?php echo $row['i_title']; ?>
                    </span>
                </td>
                <td class="td-center">
                    <?php echo $row['i_di']; ?>
                </td>
                <td class="td-center">
                    <?php echo $row['i_cost']; ?>
                </td>
                <td class="td-small">
                    <?php echo $row['r_owner']; ?>
                </td>
                <td class="td-small">
                    <?php echo $row['r_status']; ?>
                </td>
                <td>
                    <?php echo $row['r_room']; ?>
                </td>
<!--
                <td class="td-center">
                    <button data-id="<?php echo $row['i_id']; ?>" class="action-restore-item">Восстановить</button>
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