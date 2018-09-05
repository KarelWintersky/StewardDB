<?php
require_once 'core/__required.php';

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) {
    redirectToLogin();
}

?>
<!DOCTYPE html>
<html>
<head>
<head>
    <title>StewardDB: объекты в таблице</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="styles/layout.css">
    <link rel="stylesheet" href="styles/list.css">
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
    <script type="text/javascript" src="core/js/core.excel.js"></script>
    <script type="text/javascript">
        var backend_url = '<?php echo Config::get('basepath'); ?>/ajax/db.list.all.php';
    </script>
    <script src="js/list.js"></script>
</head>
<body>

<header id="panel-header">
    <div id="panel-header-inner">
        <div id="panel-header-copyright">
            <span title="by Karel Wintersky">©</span> <a href="<?php echo Config::get('basepath'); ?>/" title="В начало"><?php echo Config::get('application_title'); ?></a>
            |
            <h4 class="header-title">Отчет о средствах</h4>
        </div>
    </div>
</header>

<div id="main-wrapper">
    <fieldset id="panel-search-selectors" class="action-hash-selectors">
        <legend>Критерии поиска: </legend>
        <ul>
            <li class="pss-have-border">
                <div>
                    <span>Помещение:</span>
                    <select name="room" class="search-selector"></select>
                </div>
            </li>
            <li class="pss-have-border">
                <div>
                    <span>Вид:</span>
                    <select name="family" class="action-reload-subfamily" disabled></select>
                </div>
            </li>
            <li class="pss-have-border">
                <div>
                    <span>Тип:</span>
                    <select name="subfamily" class="" disabled></select>
                </div>
            </li>
            <li class="pss-have-border">
                <div>
                    <span>Статус учета:</span>
                    <select name="status" class="search-selector"></select>
                </div>

            </li>
            <li class="pss-have-border">
                <div>
                    <span>Владелец:</span>
                    <select name="owner" class="search-selector"></select>
                </div>

            </li>
            <li class="button">
                <div>
                    <button id="actor-show-selected">Показать выбранное</button>
                </div>
            </li>
            <li></li>
            <li class="button">
                <div>
                    <button id="actor-show-all">Показать все</button>
                </div>

            </li>
            <li class="button">
                <div>
                    <button id="actor-reset-selectors">Сброс</button>
                </div>
            </li>
            <li class="button">
                <div>
                    <button id="actor-excel-export">Экспорт EXCEL</button>
                </div>
            </li>
        </ul>
    </fieldset>
    <hr>
    <fieldset id="panel-output" class="table-hl-rows">
        <legend> Результат: </legend>
        <div id="ajax-spinner"></div>
        <span id="panel-output-span">

        </span>
    </fieldset>
</div>
</body>
</html>