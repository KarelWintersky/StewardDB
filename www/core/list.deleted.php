<?php
require_once '__required.php';

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
    <link rel="stylesheet" href="../styles/layout.css">
    <link rel="stylesheet" href="../styles/list.css">
    <link rel="stylesheet" href="jq/jquery.jgrowl.css">
    <link rel="stylesheet" href="jq/colorbox.css">
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <![endif]-->
    <script type="text/javascript" src="jq/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="jq/jquery.jgrowl.js"></script>
    <script type="text/javascript" src="jq/jquery.colorbox-min.js"></script>
    <script src="js/core.js"></script>
    <script src="js/core.options.js"></script>
    <script type="text/javascript" src="js/core.excel.js"></script>
    <script type="text/javascript">
        function reloadItemsList(url, message)
        {
            var msg = message || 'Операция успешно завершена';
            $("#panel-output-span").empty().load(url);
            $.jGrowl(msg)
        }

        $(document).ready(function () {
            var base_url = '<?php echo Config::get('basepath'); ?>';

            var backend_url = base_url + '/ajax/db.deleted.list.php?is_deleted=1';
            $.ajaxSetup({cache: false, async: false });

            reloadItemsList(backend_url, 'Данные загружены');

            $("#actor-exit").on('click',function(event){
                window.history.back();
            });

            $("#actor-excel-export").on('click', function(){
                tableToExcel('exportable', 'Deleted items');
            });

            $("#actor-purge-items").on('click', function(){
                $.get(base_url + '/ajax/db.deleted.purge.php').done(function(data){
                    var res = (data != '') ? $.parseJSON(data) : {'status': 'null' } ;
                    res['state'] == 'done' ? reloadItemsList(backend_url, 'Записи, помеченные на удаление окончательно уничтожены') : $.jGrowl('Ошибка очистки!');
                });
            });

            $("#panel-output-span")
                    .on('click', ".action-show-extended-info-for-id", function(){
                        var id = $(this).attr('data-id');
                        $.colorbox({
                            href: base_url + '/ajax/db.list.item.php?is_deleted=1&id='+id,
                            width: 800,
                            onClosed: function() { }
                        });
                    });
            // bind on colorboxed button
            $("#colorbox").on('click', ".action-restore-item", function(){
                var id = $(this).attr('data-id');
                $.get(base_url + '/ajax/db.deleted.restore.php?id='+id).done(function(data){
                    var res = (data != '') ? $.parseJSON(data) : {'status': 'null' } ;
                    res['state'] == 'done' ? reloadItemsList(backend_url, 'Данные восстановлены. Внимание, статус восстановленного объекта не определен!!! ') : $.jGrowl('Ошибка восстановления!');
                    $.colorbox.close();
                });
            });
        });
    </script>
    <style type="text/css">
        .action-edit-by-id span {
            display: none;
        }
        .action-edit-by-id::after {
            content: "Восстановить!";
        }
        .pdlii-input {
            background-color: #fffacd;
            color: blue;
        }
        .pdlii-warning {
            color: red;
            font-size: large;
        }
    </style>
</head>
<body>

<header id="panel-header">
    <div id="panel-header-inner">
        <div id="panel-header-copyright">
            <span title="by Karel Wintersky">©</span> <a title="В начало"><?php echo Config::get('application_title'); ?></a>
            |
            <h4 class="header-title">Просмотр удаленных объектов</h4>
        </div>
    </div>
</header>

<div id="main-wrapper">
    <fieldset id="panel-search-selectors" class="action-hash-selectors">
        <ul>
            <li class="button">
                <div>
                    <button id="actor-exit">Назад</button>
                </div>
            </li>
            <li></li>
            <li class="button">
                <div>
                    <button id="actor-excel-export">Экспорт EXCEL</button>
                </div>
            </li>
            <li></li>
            <li class="button">
                <div>
                    <button id="actor-purge-items">Purge all</button>
                </div>
            </li>
        </ul>
    </fieldset>
    <hr>
    <fieldset id="panel-output" class="table-hl-rows">
        <legend> Список удаленных объектов: </legend>
        <div id="ajax-spinner"></div>
        <span id="panel-output-span">
        </span>
    </fieldset>
</div>

<!--
На данный момент реализация просмотра удаленных объектов (и их удаления) не совсем корректна.<br>
        Правильно - не ставить флажок "удален", а ставить статус "удален" (но не позволять по нему искать в листинге объектов, как?)<br>
        При восстановлении - ставить статус "восстановлен".<br>

        При таком подходе у нас после восстановления логичнее будет установлен статус.<br>

        Костылем может быть решение - как показывать список объектов запросом, в котором
        при совокупности условий<br><br>
        <code>
            ... ref_status.data_str AS r_status ...<br>
            AND ref_status.id = {$table}.status
        </code>
        <br>
        <br>
        может не найтись ref_status.id , соответствующий {table}.status (если записи о статусе нет в справочнике)
        <br>
        JOIN ?
        <br>
        Пока что используем снятие галочки 'is_deleted' или стирание
-->

</body>
</html>