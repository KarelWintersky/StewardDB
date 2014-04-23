<?php
$table = 'export_csv';

require_once('../core/core.php');
require_once('../core/core.db.php');

$id = retVal($_GET['id']);

$get['id'] = $id;
$query = getQuery($get, 'export_csv');

$link = ConnectDB();

$qr = mysql_query($query) or die('error: '.$query);
$nr = @mysql_num_rows($qr);

$row = array();

if ($nr == 1) {
    $row = mysql_fetch_assoc($qr);
}

CloseDB($link);
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

</style>

<div id="popup-db-list-item-info">
    <dl>
        <dt>Внутренний №:</dt>
        <dd>
            <input type="text" size="30" class="pdlii-input" readonly value="<?=$row['i_id']?>">
        </dd>

        <dt>Инвентарный номер:</dt>
        <dd>
            <input type="text" size="30" class="pdlii-input" readonly value="<?=$row['i_code']?>">
        </dd>

        <dt>Название по базе:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?=$row['i_dt']?>">
        </dd>

        <dt>Название по описи:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?=$row['i_mt']?>">
        </dd>

        <dt>Кабинет:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?=$row['r_room']?>">
        </dd>

        <dt>Владелец:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?=$row['r_owner']?>">
        </dd>

        <dt>Статус:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?=$row['r_status']?>">
        </dd>

        <dt>Дата учета:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?=$row['i_di']?>">
        </dd>

        <dt>Цена:</dt>
        <dd>
            <input type="text" size="60" class="pdlii-input" readonly value="<?=$row['i_cost']?>">
        </dd>

        <dt>Комментарий:</dt>
        <dd>
            <textarea readonly class="pdlii-input" cols="60" rows="3"><?=$row['i_comment']?></textarea>
        </dd>
    </dl>
</div>