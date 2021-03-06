<?php
require_once '__required.php';

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) {
    redirectToLogin();
}

?>
<html>
<head>
    <title>StewardDB: Управление</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="../styles/layout.css">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="jq/jquery-1.10.2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".action-button-is-link").on('click',function(){
                window.location.href = $(this).attr('data-href');
            });
            $(".action-button-have-submenu").on('click',function(){
                $('#'+$(this).attr('data-submenu-id') ).toggle(400);
            });
        });
    </script>
    <style>
        table {
            border: 1px dotted blue;
        }
        ul {
            margin-left: 0;
            margin-bottom: 0; margin-top: 0;
            padding: 0;
        }
        ul li {
            list-style-type: none;
            padding-top: 2px;
            padding-bottom: 2px;
        }
        div {
            margin:0;
        }
        /* */
        #footer {
            color: gray;
            background-color: #d3d3d3;
        }

        ol li {
            margin-bottom: 0.5em;
        }
        .hidden {
            display: none;
        }

        /* submenu microengine */
        .have-top-border {
            border-top: 3px navy solid;
            padding-top: 10px;
            margin-top: 10px;
            width: 150px;
        }
        .admin-button-large {
            height: 60px;
            width: 150px;
        }
        .admin-button-small {
            width: 150px;
        }
        .actor-button-sub {
            width: 150px;
        }
        .submenu-fieldset {
            width: 150px;
        }
    </style>
</head>

<body>

<header id="panel-header">
    <div id="panel-header-inner">
        <span title="by Karel Wintersky">©</span> <a href="<?php echo Config::get('basepath'); ?>/" title="В начало"><?php echo Config::get('application_title'); ?></a>
            <sub> by Karel Wintersky</sub>
            |
            <h4 class="header-title">Административный раздел</h4>
        </div>
    </div>
</header>

<div id="main-wrapper">

    <table width="100%">
        <tr>
            <td width="180">
                <!-- типа меню -->
                <ul>
                    <li>
                        Справочники
                    </li>
                    <li>
                        <button data-href="ref.rooms.php" class="admin-button-large action-button-is-link">Помещения</button>
                    </li>
                    <li>
                        <button data-href="ref.abstract.php?ref=ref_status" class="admin-button-large action-button-is-link">Статус учёта</button>
                    </li>
                    <li>
                        <button data-href="ref.abstract.php?ref=ref_owners" class="admin-button-large action-button-is-link">Владельцы средств</button>
                    </li>
                    <li>
                        <button data-href="ref.abstract.php?ref=ref_family" class="admin-button-large action-button-is-link">Вид (семейство)</button>
                    </li>
                    <li>
                        <button data-href="ref.abstract.php?ref=ref_subfamily" class="admin-button-large action-button-is-link">Тип (подвид)</button>
                    </li>
                    <hr>
                    <li>
                        <button data-href="list.deleted.php" class="admin-button-large action-button-is-link">Список удаленных объектов</button>
                    </li>
                    <hr>
                    <li>
                        <button data-href="db.backup.php" class="admin-button-large action-button-is-link">Резервное копирование базы</button>
                    </li>
                    <hr>
                    <li>
                        <button data-href="<?php echo Config::get('basepath'); ?>/" class="admin-button-large action-button-is-link">Выход</button>
                    </li>
                </ul>
            </td>
            <td valign="top">
                <br><br>
                Если вы зашли сюда случайно и не знаете, что здесь нужно делать - пожалуйста, нажмите кнопку
                <button data-href="<?php echo Config::get('basepath'); ?>/" class="admin-button-small action-button-is-link">ВЫХОД</button>
            </td>
        </tr>
    </table>
    <div id="footer">© Karel Wintersky, 01-04-2014 - ?.</div>



</div>


<footer>
    <span id="flow-error-line"></span>
</footer>

</body>
</html>