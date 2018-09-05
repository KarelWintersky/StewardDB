<?php
require_once '../core/__required.php';

$id = $_GET['id'] ?? 0;
$is_deleted = $_GET['is_deleted'] ?? 0;

$get['id'] = $id;

$query = getQuery($_GET, $main_data_table);

$qr = mysqli_query($mysqli, $query) or die('error: '.$query);
$nr = @mysqli_num_rows($qr);

$row = array();

if ($nr == 1) {
    $row = mysqli_fetch_assoc($qr);
}

CloseDB($mysqli);
?>
<style>
    #popup-db-list-item-info dl {
        padding: 1em; margin: 0;
        padding-top: 25px;
        display: block;
    }
    #popup-db-list-item-info dt {
        float: left;
        width: 200px;
        text-align: right;
        padding-right: 5px;
        min-height: 1px;
        font-size: 120%;
    }
    #popup-db-list-item-info dd {
        position: relative;
        top: -1px;
        margin-bottom: 5px;
    }
    #popup-db-list-item-info .pdlii-input  {
        font-size: 100%;
        border-color: gray;
    }
    #popup-db-list-item-info button {
        font-size: large;
    }
</style>
<!--
<?=$query?>
-->
<div id="popup-db-list-item-info">
    <dl>
        <dt>Внутренний №:</dt>
        <dd>
            <input type="text" size="30" class="pdlii-input" readonly value="<?php echo $row['i_id']; ?>">
            <?php
            if ($is_deleted) echo '<span class="pdlii-warning">Объект удален!!!</span>';
            ?>
        </dd>

        <dt>Инвентарный номер:</dt>
        <dd>
            <input type="text" size="30" class="pdlii-input" readonly value="<?php echo $row['i_code']; ?>">
        </dd>

        <dt>Название по базе:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?php echo $row['i_dt']; ?>">
        </dd>

        <dt>Название по описи:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?php echo $row['i_mt']; ?>">
        </dd>

        <dt>Кабинет:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?php echo $row['r_room']; ?>">
        </dd>

        <dt>Владелец:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?php echo $row['r_owner']; ?>">
        </dd>

        <dt>Статус:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?php echo $row['r_status']; ?>">
        </dd>

        <dt>Дата учета:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?php echo $row['i_di']; ?>">
        </dd>

        <dt>Цена:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?php echo $row['i_cost']; ?>">
        </dd>

        <dt>Комментарий:</dt>
        <dd>
            <textarea readonly class="pdlii-input" cols="60" rows="3"><?php echo $row['i_comment']; ?></textarea>
        </dd>
        <dt></dt>
        <dd>
            <button class="action-edit-by-id action-restore-item" data-id="<?php echo $row['i_id']; ?>"><span>Редактировать запись</span></button>
<?php //@todo: место для потенциального бага!!!
/*  Здесь у кнопки сразу два класса - action-edit & action-remove. Я так сделал, чтобы не плодить
два разных файла для показа информации по предмету - один вызывается из list, другой - из list.deleted
Другой вариант - передовать в этот скрипт имя экшен-класса. Тоже усложнение.
Баг возникнет, если в одном списке-отображении одновременно будут использоваться УДАЛЕНИЕ и РЕДАКТИРОВАНИЕ
информации. Если такое возникнет - придется что-то делать.
*/
?>
        </dd>
    </dl>
</div>