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
    <title>StewardDB: Index</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="styles/layout.css">
    <link rel="stylesheet" href="styles/reports.css">
    <!--[if lt IE 9]>
    <script src="core/js/html5shiv.js"></script>
    <![endif]-->
    <script type="text/javascript" src="core/jq/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="core/jq/jquery.hotkeys.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajaxSetup({cache: false});
            // prompt for input
            $("#input_report_filename")
                    .on('focus', function(){
                        if ($(this).val() == 'report.csv') $(this).val('');
                    })
                    .on('blur', function(){
                        if ($(this).val() == '') $(this).val('report.csv');
                    });

            $("#choose_report").on('click', 'a[download]',  function(){
             window.location.href = $(this).attr('href') + 'filename=' + $("#input_report_filename").val();
                return false;
            });

        });

    </script>
</head>
<body>

<header id="panel-header">
    <div id="panel-header-inner">
        <div id="panel-header-copyright">
            <span title="by Karel Wintersky">©</span> <a href="<?php echo Config::get('basepath'); ?>/" title="В начало"><?php echo Config::get('application_title'); ?></a>
            |
            <h4 class="header-title">Отчеты</h4>
        </div>
    </div>
</header>

<div id="main-wrapper">
    <ul id="choose_report">
        <li class="no-mark">Введите название файла: <input type="text" value="report.csv" size="25" id="input_report_filename"> </li>
        <li>
            <a href="ajax/report.get.php?report=get_items_not_in_rooms&" class="action-get-report" download>Список предметов в неизвестных кабинетах</a>
        </li>
        <li>
            <a href="ajax/report.get.php?report=get_items_are_in_rooms&" class="action-get-report" download>Список предметов с инв.номерами по кабинетам</a>
        </li>
        <li class="no-mark"><hr></li>
        <li class="no-mark"><<< <a href="<?php echo Config::get('basepath'); ?>/"> Назад </a></li>
    </ul>
</div>

<footer>
    <span id="flow-error-line"></span>
</footer>



</body>
</html>