<?php
require_once('core.php');
require_once('core.db.php');
require_once('core.kwt.php');
require_once('core.login.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) {
    redirectToLogin();
}

/*
Rooms reference module

Rooms table: {
id PK AI
room_name default 0
room_group default `NULL`
comment varchar 64
}

*/


$reference = 'rooms';
$return = '';

if (true) {
    $action = isset($_GET['action']) ? $_GET['action'] : 'no-action';
    $link = ConnectDB();

    switch ($action) {
        case 'insert':
        {
            $q = array(
                'room_name' => trim(mysql_real_escape_string($_GET['room_name'])),
                'room_group' => trim(mysql_real_escape_string($_GET['room_group'])),
                'room_comment' => mysql_real_escape_string($_GET['room_comment']),
            );
            $qstr = MakeInsert($q, $reference);
            $res = mysql_query($qstr, $link) or Die("Unable to insert data to DB!".$qstr);
            $new_id = mysql_insert_id() or Die("Unable to get last insert id! Last request is [$qstr]");

            $result['message'] = $qstr;
            $result['error'] = 0;
            $return = json_encode($result);
            break;
        } // case 'insert'
        case 'update':
        {
            $id = $_GET['id'];
            $q = array(
                'room_name' => trim(mysql_real_escape_string($_GET['room_name'])),
                'room_group' => trim(mysql_real_escape_string($_GET['room_group'])),
                'room_comment' => mysql_real_escape_string($_GET['room_comment']),
            );

            $qstr = MakeUpdate($q, $reference, "WHERE id=$id");
            $res = mysql_query($qstr, $link) or Die("Unable update data : ".$qstr);

            $result['message'] = $qstr;
            $result['error'] = 0;
            $return = json_encode($result);
            break;
        } // case 'update
        case 'remove':
        {
            $id = $_GET['id'];
            $reference = getTablePrefix() . $reference;
            $q = "DELETE FROM {$reference} WHERE (id={$id})";
            if ($r = mysql_query($q)) {
                // запрос удаление успешен
                $result["error"] = 0;
                $result['message'] = 'Удаление успешно';

            } else {
                // DB error again
                $result["error"] = 1;
                $result['message'] = 'Ошибка удаления!';
            }
            $return = json_encode($result);
            break;
        } // case 'remove
        case 'load':
        {
            $id = $_GET['id'];
            $reference = getTablePrefix() . $reference;
            $query = "SELECT * FROM {$reference} WHERE id={$id}";
            $res = mysql_query($query) or die("Невозможно получить содержимое справочника! ".$query);
            $ref_numrows = mysql_num_rows($res);

            if ($ref_numrows != 0) {
                $result['data'] = mysql_fetch_assoc($res);
                $result['error'] = 0;
                $result['message'] = '';
            } else {
                $result['error'] = 1;
                $result['message'] = 'Ошибка базы данных!';
            }
            $return = json_encode($result);
            break;
        } // case 'load'
        case 'list':
        {
            $reference = getTablePrefix() . $reference;
            $query = "SELECT * FROM {$reference} ORDER BY room_group, room_name";
            $res = mysql_query($query) or die("mysql_query_error: ".$query);

            $ref_numrows = @mysql_num_rows($res) ;
            $return = <<<TABLE_START
<table border="1" width="100%">
    <tr>
        <th width="5%">(id)</th>
        <th>Помещение</th>
        <th width="10%">Группа</th>
        <th>Комментарий</th>
        <th width="7%">Управление</th>
    </tr>
TABLE_START;
            if ($ref_numrows > 0) {
                while ($ref_record = mysql_fetch_assoc($res))
                {
                    $return.= <<<TABLE_EACHROW
<tr>
    <td>{$ref_record['id']}</td>
    <td>{$ref_record['room_name']}&nbsp;</td>
    <td>{$ref_record['room_group']}&nbsp;</td>
    <td>{$ref_record['room_comment']}&nbsp;</td>
    <td class="centred_cell"><button class="actor-edit button-edit" name="{$ref_record['id']}">Edit</button></td>
</tr>
TABLE_EACHROW;
                }
            } else {
                $return .= <<<TABLE_IS_EMPTY
<tr><td colspan="5">Справочник пуст!</td></tr>
TABLE_IS_EMPTY;
            }
            break;
        } // case 'list'
        case 'no-action': {
        ?>
<html>
<head>
    <title>Справочник: помещения [<?php echo $reference ?>]</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="jq/jquery-1.10.2.min.js"></script>
    <script src="jq/jquery-ui-1.10.3.custom.min.js"></script>
    <link rel="stylesheet" type="text/css" href="jq/jquery-ui-1.10.3.custom.min.css">

    <style type="text/css">
        body {
            font-family: "Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";
            font-size: 62.5%;
        }
        label, input { display:block; }
        input.text {
            margin-bottom:12px;
            width:95%;
            padding: .4em;
        }
        fieldset {
            padding:0;
            border:0;
            margin-top:25px;
        }
        h1 {
            font-size: 1.2em;
            margin: .6em 0;
        }
        .ui-dialog .ui-state-error {
            padding: .3em;
        }
        #ref_list {
            height: 500px;
            width: 99%;
            border: 1px solid gray;
            overflow-y: scroll;
        }
        .centred_cell, th {
            text-align: center;
        }
        .button-large {
            height: 60px;
        }
    </style>
    <script type="text/javascript">
        var ref_name = 'rooms';
        var button_id = 0;

        function ShowErrorMessage(message)
        {
            alert(message);
        }

        function Abstract_CallAddItem(source, id)
        {
            var $form = $(source).find('form');
            url = $form.attr("action");
            var getting = $.get(url, {
                room_name: $form.find("input[name='add_room_name']").val(),
                room_group: $form.find("input[name='add_room_group']").val(),
                room_comment: $form.find("input[name='add_room_comment']").val(),
                ref: 'rooms'
            } );
            getting.done(function(data){

                result = $.parseJSON(data);
                if (result['error']==0) {
                    $("#ref_list").empty().load("?action=list&ref="+ref_name);
                    $( source ).dialog( "close" );
                } else {
                    $( source ).dialog( "close" );
                }
            });
        }
        function Abstract_CallUpdateItem(source, id)
        {
            var $form = $(source).find('form');
            var getting = $.get($form.attr("action"), {
                room_name: $form.find("input[name='edit_room_name']").val(),
                room_group: $form.find("input[name='edit_room_group']").val(),
                room_comment: $form.find("input[name='edit_room_comment']").val(),
                ref: 'rooms',
                id: id
            } );
            getting.done(function(data){
                result = $.parseJSON(data);
                if (result['error']==0) {
                    $("#ref_list").empty().load("?action=list&ref="+ref_name);
                    $( source ).dialog( "close" );
                } else {
                    $( source ).dialog( "close" ); // Some errors, show message!
                }
            });


        }
        function Abstract_CallRemoveItem(target, id)
        {
            var getting = $.get('?action=remove', {
                ref: ref_name,
                id: id
            });
            getting.done(function(data){
                result = $.parseJSON(data);
                if (result['error'] == 0) {
                    $('#ref_list').empty().load("?action=list&ref="+ref_name);
                    $( target ).dialog( "close" );
                } else {
                    ShowErrorMessage(result['message']);
                    $( target ).dialog( "close" );
                }
            });

        }
        function Abstract_CallLoadItem(target, id)
        {
            var getting = $.get('?action=load', {
                id: id,
                ref: ref_name
            });
            var $form = $(target).find('form');
            getting.done(function(data){
                var result = $.parseJSON(data);
                if (result['error'] == 0) {
                    $form.find("input[name='edit_room_name']").val( result['data']['room_name'] );
                    $form.find("input[name='edit_room_group']").val( result['data']['room_group'] );
                    $form.find("input[name='edit_room_comment']").val( result['data']['room_comment'] );
                } else {
                    // ошибка загрузки
                }
            });
        }

        $(document).ready(function () {
            $.ajaxSetup({cache: false, async: false });

            $("#ref_list").load("?action=list&ref="+ref_name);

            /* вызов и обработчик диалога ADD-ITEM */
            $("#actor-add").on('click',function() {
                $('#add_form').dialog('open');
            });

            $( "#add_form" ).dialog({
                autoOpen: false,
                height: 300,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Добавить",
                        click: function() {
                            Abstract_CallAddItem(this);
                            $(this).find('form').trigger('reset');
                            // логика добавления
                            $( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "Сброс",
                        click: function() {
                            $(this).find('form').trigger('reset');
                        }
                    },
                    {
                        text: "Отмена",
                        click: function() {
                            $(this).find('form').trigger('reset');
                            // просто отмена
                            $( this ).dialog( "close" );
                        }

                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });

            /* вызов и обработчик диалога редактирования */

            $('#ref_list').on('click', '.actor-edit', function() {
                button_id = $(this).attr('name');

                Abstract_CallLoadItem("#edit_form", button_id);
                $('#edit_form').dialog('open');
            });
            $( "#edit_form" ).dialog({
                autoOpen: false,
                height: 300,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Принять и обновить данные",
                        click: function() {
                            Abstract_CallUpdateItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    },
                    {
                        text: "Удалить значение из базы",
                        click: function() {
                            Abstract_CallRemoveItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });

            $("#actor-exit").on('click',function(event){
                location.href = '<?=$CONFIG['basepath']?>/core/';
            });

        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="actor-exit"><strong> <<< НАЗАД </strong></button>
<button type="button" class="button-large" id="actor-add"><strong>Добавить запись в справочник</strong></button><br>

<div id="add_form" title="Добавить запись в справочник [<?php echo $reference ?>]">
    <form action="?action=insert">
        <fieldset>
            <label for="add_room_name">Название помещения: </label>
            <input type="text" name="add_room_name" id="add_room_name" class="text ui-widget-content ui-corner-all">

            <label for="add_room_group">Относится к группе:</label>
            <input type="text" name="add_room_group" id="add_room_group" class="text ui-widget-content ui-corner-all">

            <label for="add_room_comment">Комментарий:</label>
            <input type="text" name="add_room_comment" id="add_room_comment" class="text ui-widget-content ui-corner-all">
        </fieldset>
    </form>
</div>
<div id="edit_form" title="Изменить запись в справочнике [<?php echo $reference ?>]">
    <form action="?action=update">
        <fieldset>
            <label for="edit_room_name">Название помещения: </label>
            <input type="text" name="edit_room_name" id="edit_room_name" class="text ui-widget-content ui-corner-all">

            <label for="edit_room_group">Относится к группе:</label>
            <input type="text" name="edit_room_group" id="edit_room_group" class="text ui-widget-content ui-corner-all">

            <label for="edit_room_comment">Комментарий:</label>
            <input type="text" name="edit_room_comment" id="edit_room_comment" class="text ui-widget-content ui-corner-all">
        </fieldset>
    </form>
</div>
<hr>
<fieldset class="result-list">
    <div id="ref_list">
    </div>
</fieldset>

</body>
</html>
        <?php
            break;
        }
    } //switch
    CloseDB($link);

    print($return);
} else {
    Die('При вызове не указан идентификатор справочника! Работать не с чем! ');
}
?>