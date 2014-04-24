<?php
require_once('core/core.php');
require_once('core/core.db.php');
require_once('core/core.kwt.php');
require_once('core/core.login/core.login.php');


$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) {
    header('Location: /core/core.login/');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>StewardDB: Check objects in database</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="styles/layout.css">
    <link rel="stylesheet" href="styles/check.css">
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


    <script src="js/check.js"></script>
</head>
<body>
<header id="panel-header">
    <div id="panel-header-inner">
        <div id="panel-header-copyright"><a href="/" title="В начало">StewardDB v 0.5</a>
            <sub> by Karel Wintersky</sub>
            |
            <h4 class="header-title">Добавление и проверка инвентарных номеров</h4>
        </div>
        <div id="panel-header-config">
            |
            Добавлено: <input type="text" id="log-input-added" class="input-log" value="0" size="5">
            Обновлено: <input type="text" id="log-input-updated" class="input-log" value="0" size="5">
            |
            <button id="actor-reload-references">Перегрузить справочники</button>
        </div>
    </div>
</header>

<div id="main-wrapper">
    <!-- форма проверки инвентарного кода и добавление нового -->
    <fieldset id="panel-itemcheck">
        <legend>Объекты учета в БД</legend>
        <form action="ajax/db.item.check.php" id="db_check_form" method="GET">
            <button type="button" title="Press F2 for new!" id="actor-new-check">F2</button>

            <input type="text" name="check_this_code" id="check_this_code">

            <button type="submit" id="actor-item-check">Проверить</button>
            <span>|</span>
            <button type="button" id="actor-item-add">F4: Добавить объект</button>
            <span>В кабинет №: </span>
            <select name="add-use-this-room" class="action-lose-focus-after-select-option"></select>

        </form>
        <span class="hint">Нажмите <strong>F2</strong> для новой проверки по инвентарному коду.</span>
        <span id="report-errormessage-for-invcode" class="warning-message"></span>
    </fieldset>

    <!-- форма добавления или обновления данных -->
    <fieldset id="fieldset_working_form" class="hidden">
        <legend id="fieldset_form_current_task"></legend>

        <form method="post" id="form_current_task" action="">
            <input type="hidden" name="a_inv_id">
            <dl>
                <dt>Владелец:</dt>
                <dd>
                    <select name="a_inv_owner" id="owner" class="input"></select>
                </dd>

                <dt>Состояние:</dt>
                <dd>
                    <select name="a_inv_status" id="status" class="input"></select>
                </dd>

                <dt>Инвентарный номер:</dt>
                <dd><input type="text" name="a_inv_code" size="30" class="input"></dd>

                <dt>Название по описи:</dt>
                <dd>
                    <input type="text" name="a_inv_mytitle" size="60" class="input">
                    <button id="actor-title-prediction" type="button">Предсказать!</button>
                    <button id="actor-title-copy-from-db" type="button">Скопировать название из базы</button>
                </dd>
                <dt>Название по базе:</dt>
                <dd><input type="text" name="a_inv_dbtitle" size="60" class="input"></dd>

                <dt>Кабинет:</dt>
                <dd>
                    <select name="a_inv_rooms" id="rooms" class="input"></select>
                </dd>

                <dt>Дата принятия к учету:</dt>
                <dd><input type="text" name="a_inv_date_income_str" size="60" class="input"></dd>

                <dt>Комментарий:</dt>
                <dd><input type="text" name="a_inv_comment" size="60" class="input"></dd>
            </dl>
            <button type="submit" class="input">Сохранить</button>
        </form>
    </fieldset>

    <!-- вывод отчета - что вставили или обновили -->
    <fieldset id="fieldset_report" class="hidden">
        <legend>Результат:</legend>
        <dl>
            <dt>Инвентарный номер:</dt>
            <dd><input type="text" name="a_inv_code" size="30" class="input" readonly></dd>
            <dt>Название по описи:</dt>
            <dd><input type="text" name="a_inv_mytitle" size="60" class="input" readonly></dd>
            <dt>Название по базе:</dt>
            <dd><input type="text" name="a_inv_dbtitle" size="60" class="input"readonly></dd>
            <dt>Кабинет:</dt>
            <dd>
                <input type="text" name="a_room" size="60" readonly>
            </dd>
            <dt>Владелец:</dt>
            <dd>
                <input type="text" name="a_owner" size="60" readonly>
            </dd>
            <dt>Состояние:</dt>
            <dd>
                <input type="text" name="a_status" size="60" readonly>
            </dd>
            <dt>Дата учета:</dt>
            <dd><input type="text" name="a_inv_date_income_str" size="60" class="input" readonly></dd>
            <dt>Комментарий:</dt>
            <dd><input type="text" name="a_inv_comment" size="60" class="input" readonly></dd>
        </dl>
        <button type="button" id="actor-final" class="input">Ясно!</button>
    </fieldset>

    <fieldset id="fieldset_log">
        <legend>Log: </legend>
        <div id="log" class="text-small">

        </div>
    </fieldset>
</div>

<footer class="flow-line">
    <span id="flow-error-line"></span>
</footer>

</body>
</html>