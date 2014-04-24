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
    <title>StewardDB: Index</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="styles/layout.css">
    <link rel="stylesheet" href="styles/index.css">
    <!--[if lt IE 9]>
    <script src="core/js/html5shiv.js"></script>
    <![endif]-->
    <script type="text/javascript" src="core/jq/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="core/jq/jquery.hotkeys.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#actor-logout').on('click', function(){
                document.location.href = '/core/core.login/logout.php';
            });
        });
    </script>
    <style>
        .panel-header-inner-button-logout { margin-right: 1em; }
    </style>
</head>
<body>

<header id="panel-header">
    <div id="panel-header-inner">
        <div id="panel-header-copyright"><a href="/" title="В начало">StewardDB v 0.5</a>
            <sub> by Karel Wintersky</sub>
            |
            <h4 class="header-title">Выбор режима работы</h4>
        </div>
        <div id="panel-header-config">
            <button id="actor-logout" class="panel-header-inner-button-logout">Logout</button>
        </div>
    </div>
</header>

<div id="main-wrapper">
    <ul>
        <li><a href="list.php">Отчет</a></li>
        <li><a href="check.php"> Добавление и проверка инвентарных номеров  </a></li>
        <li><a href="export.reports.php">Экспорт отчетов</a></li>
        <li><a href="help.html" onclick="return false;"> Помощь </a></li>
        <li><a href="core/" target="_blank">Управляющий раздел</a></li>
    </ul>
</div>

<footer>
    <span id="flow-error-line"></span>
</footer>



</body>
</html>