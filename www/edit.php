<?php
require_once('core/core.php');
require_once('core/core.db.php');
require_once('core/core.kwt.php');
require_once('core/core.login.php');


$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) {
    redirectToLogin();
}

$table = 'export_csv';

$id = retVal($_GET['id']);
$get['id'] = $id;
$query = getQuery($get, $CONFIG['main_data_table']);

$link = ConnectDB();

$qr = mysql_query($query) or die('error: '.$query);

$item = array();

if (@mysql_num_rows($qr) == 1) {
    $item = mysql_fetch_assoc($qr);
}

CloseDB($link);
?>
<!DOCTYPE html>
<html>
<head>
    <title>StewardDB: Edit object</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="styles/layout.css">
    <link rel="stylesheet" href="styles/edit.css">
    <link rel="stylesheet" href="core/jq/jquery.jgrowl.css">
    <link rel="stylesheet" href="core/jq/colorbox.css">
    <!--[if lt IE 9]>
    <script src="core/js/html5shiv.js"></script>
    <![endif]-->
    <script type="text/javascript" src="core/jq/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="core/jq/jquery.hotkeys.js"></script>
    <script type="text/javascript" src="core/jq/jquery.jgrowl.js"></script>
    <script type="text/javascript" src="core/jq/jquery.colorbox-min.js"></script>
    <script src="core/js/core.js"></script>
    <script src="core/js/core.options.js"></script>
    <script type="text/javascript">
        var i_id        = <?=$item['i_id']?> ;
        var i_status    = <?=$item['i_status']?> ;
        var i_owner     = <?=$item['i_owner']?>  ;
        var i_room      = <?=$item['i_room']?>  ;
    </script>
    <script src="js/edit.js"></script>
</head>

<body>

<header id="panel-header">
    <div id="panel-header-inner">
        <div id="panel-header-copyright">
            <span title="by Karel Wintersky">©</span> <a href="<?=$CONFIG['basepath']?>/" title="В начало"><?=$CONFIG['application_title']?></a>
            |
            <h4 class="header-title">Редактирование объекта</h4>
        </div>
        <div id="panel-header-config">
            <button id="actor-reload-references">Перегрузить справочники</button>
        </div>
    </div>
</header>

<div id="main-wrapper">
    <!-- форма добавления или обновления данных -->
    <fieldset id="fieldset_working_form">
        <legend>Редактирование объекта учета с внутренним идентификатором: <span style="color:red"> <?=$item['i_id']?> </span> &nbsp; </legend>

        <form id="form_item_edit" action="ajax/db.item.update.php">
            <input type="hidden" name="a_inv_id" value="<?=$item['i_id']?>">
            <dl>
                <dt>
                </dt>
                <dd>
                    <button type="button" id="actor-back-to-list"> <<< Назад к списку</button>
                </dd>

                <dt>Внутренний №:</dt>
                <dd>
                    <input disabled type="text" size="30" value="<?=$item['i_id']?>">
                </dd>

                <dt>Инвентарный номер:</dt>
                <dd>
                    <input type="text" name="a_inv_code" size="30" value="<?=$item['i_code']?>">
                </dd>

                <dt>Название по описи:</dt>
                <dd>
                    <input type="text" name="a_inv_mytitle" size="60" value="<?=$item['i_mt']?>">
                    <button id="actor-title-prediction" type="button">Предсказать название по базе!</button>
                </dd>
                <dt>Название по базе:</dt>
                <dd>
                    <input type="text" name="a_inv_dbtitle" size="60" value="<?=$item['i_dt']?>">
                    <button id="actor-title-copy-from-db" type="button">Скопировать название из базы</button>
                </dd>

                <dt>Владелец:</dt>
                <dd>
                    <select name="a_inv_owner" id="owner" class="input"></select>
                </dd>

                <dt>Кабинет:</dt>
                <dd>
                    <select name="a_inv_rooms" id="rooms" class="input"></select>
                </dd>

                <dt>Статус:</dt>
                <dd>
                    <select name="a_inv_status" id="status" class="input"></select>
                </dd>

                <dt>Дата принятия к учету:</dt>
                <dd><input type="text" name="a_inv_date_income_str" size="60" value="<?=$item['i_di']?>"></dd>

                <dt>Цена (руб.):</dt>
                <dd>
                    <input type="text" size="60" name="a_inv_price" value="<?=$item['i_cost']?>">
                </dd>

                <dt>Комментарий:</dt>
                <dd>
                    <textarea name="a_inv_comment" cols="60" rows="3"><?=$item['i_comment']?></textarea>
                </dd>

                <dt>
                    <button type="button" style="float:left" id="action-delete-item">F8: Удалить </button>
                </dt>
                <dd>
                    <button type="button" id="action-save-item" data-id="<?=$item['i_id']?>">F3: Сохранить</button>
                </dd>
            </dl>
        </form>
    </fieldset>

</div>


</body>
</html>