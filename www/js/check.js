var rooms_selector, status_selector, owners_selector;
var log_added = 0;
var log_updated = 0;

function Crawler_Message(message)
{
    $("#flow-error-line").show().append(message).fadeOut(2000);
}

function jMessage(message, delay)
{
    var d = delay || 2000;
    $.jGrowl(message,d);
}

function RestartSearch(search_value)
{
    var sv = search_value || '';
    $("#fieldset_working_form").trigger('reset').hide();
    $('#fieldset_report').trigger('reset').hide();
    $("#check_this_code").prop('value',sv).focus();
}

function reloadSelectors()
{
    var result = true;
    if (status_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_status')) {
        BuildSelector('a_inv_status', status_selector, 'выбрать!', 1);
        console.log('Категории качества загружены; ');
    } else {$.jGrowl('Ошибка загрузки категорий качества'); result=false; }

    if (owners_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_owners')) {
        BuildSelector('a_inv_owner', owners_selector, 'выбрать!');
        console.log('Владельцы загружены; ');
    } else {$.jGrowl('Ошибка загрузки владельцев '); result=false; }

    if (rooms_selector = preloadOptionsList('core/ref.rooms.getoptionslist.php')) {
        BuildSelectorExtended('a_inv_rooms', rooms_selector, 'выбрать!');
        BuildSelectorExtended('add-use-this-room', rooms_selector, 'Никакой');
        console.log('Помещения загружены; ');
    } else {$.jGrowl('Ошибка загрузки помещений '); result=false; }
    return result;
}

function setEditForm_foundItem(form, data)
{
    form.trigger('reset');
    SetNamedField(form, 'a_inv_id', data['inv_id']);
    SetNamedField(form, 'a_inv_code', data['inv_code']);
    SetNamedField(form, 'a_inv_dbtitle', data['inv_dbtitle']);
    SetNamedField(form, 'a_inv_mytitle', data['inv_mytitle']);
    SetNamedField(form, 'a_inv_date_income_str', data['inv_date_income_str']);
    SetNamedField(form, 'a_inv_comment', data['inv_comment']);
    return {
        'current_task_action'                   : 'ajax/db.item.update.php',
        'current_task_legend'                   : 'Обновление объекта с инвентарным номером',
        'biv_prediction'                        : 0,
        'biv_copy'                              : 1,
        'biv_final'                             : 0,
        'task_mode'                             : 'edit',
        'a_inv_rooms'                           : parseInt(data['inv_room']),
        'a_inv_status'                          : 1,
        'a_inv_owner'                           : parseInt(data['inv_owner'])
    };
}

function setEditForm_notfoundItem(form, data)
{
    form.trigger('reset');
    SetNamedField(form, 'a_inv_id', 0);
    SetNamedField(form, 'a_inv_code', data['inv_code']);
    SetNamedField(form, 'a_inv_date_income_str', data['inv_date_income_str']);
    return {
        'current_task_action'                   : 'ajax/db.item.insert.php',
        'current_task_legend'                   : 'Добавление объекта с инвентарным номером',
        'biv_prediction'                        : 1,
        'biv_copy'                              : 0,
        'biv_final'                             : 0,
        'task_mode'                             : 'add',
        'a_inv_rooms'                           : getSelectedOptionValue($("#db_check_form"), 'add-use-this-room'),
        'a_inv_status'                          : 1,
        'a_inv_owner'                           : 1
    }; // комнату возьмем из выпадающего списка справа от формы проверки
}

/*
* form - передаваемый $-объект формы
* data - данные
* */
function ShowInputForm_for_NewItem(form, data)
{
    $('#fieldset_report').trigger('reset').hide();
    $("#check_this_code").val('');
    // form.find('form').trigger('reset');
    form.trigger('reset');
    SetNamedField(form, 'a_inv_code', '');
    SetNamedField(form, 'a_inv_id', 0 );
    SetNamedField(form, 'a_inv_date_income_str', new Date().format("dd.MM.yyyy"));
    return {
        'current_task_action'                   : 'ajax/db.item.insert.php',
        'current_task_legend'                   : 'Найден объект без инвентарного номера, добавляем в базу',
        'biv_prediction'                        : 1,
        'biv_copy'                              : 0,
        'biv_final'                             : 0,
        'task_mode'                             : 'new',
        'a_inv_rooms'                           : getSelectedOptionValue($("#db_check_form"), 'add-use-this-room'),             // $("select[name='add-use-this-room'] option:selected").val(),
        'a_inv_status'                          : 1,
        'a_inv_owner'                           : 3
    }
}

function setEditForm_reportItem(form, data)
{
    SetNamedField(form, 'a_inv_code', data['r_code']);
    SetNamedField(form, 'a_inv_mytitle', unslash(data['r_mytitle']));
    SetNamedField(form, 'a_inv_dbtitle', unslash(data['r_dbtitle']));
    SetNamedField(form, 'a_room', data['r_room']);
    SetNamedField(form, 'a_owner', data['r_owner']);
    SetNamedField(form, 'a_status', data['r_status']);
    SetNamedField(form, 'a_inv_date_income_str', data['r_date_income_str']);
    SetNamedField(form, 'a_inv_comment', unslash(data['r_comment']));
    return {
        'current_task_action'                   : '',
        'current_task_legend'                   : 'Обновлено: id = ' + data['r_id'],
        'biv_prediction'                        : 0,
        'biv_copy'                              : 0,
        'biv_final'                             : 1,
        'task_mode'                             : 'report',
        'a_inv_rooms'                           : 0,
        'a_inv_status'                          : 0,
        'a_inv_owner'                           : 0
    }
}

function setEditForm_Update(fieldset, form_state)
{
    // var form = fieldset.find('form');
    if (typeof form_state != 'undefined') {
        Selector_SetOption('a_inv_rooms',       form_state['a_inv_rooms']);
        Selector_SetOption('a_inv_status',      form_state['a_inv_status']);
        Selector_SetOption('a_inv_owner',       form_state['a_inv_owner']);
        fieldset.find('legend').html(form_state['current_task_legend']);
        fieldset.find('form').prop('action', form_state['current_task_action']);
        (form_state['biv_prediction'] == 1 ? $("#actor-title-prediction").show() : $("#actor-title-prediction").hide());
        (form_state['biv_copy'] == 1 ? $("#actor-title-copy-from-db").show() : $("#actor-title-copy-from-db").hide());
    }
}

$(document).ready(function(){
    $.ajaxSetup({cache: false});
    /* onload */
    var $working_fieldset = $("#fieldset_working_form");

    reloadSelectors() ? $.jGrowl('Справочники загружены') : $.jGrowl('Ошибка загрузки справочников');

    // set focus to invcode search field
    $("#check_this_code")
        .focus()
        .on('click', function(){
//            RestartSearch();
            $("#fieldset_working_form").trigger('reset').hide();
            $('#fieldset_report').trigger('reset').hide();
    });

    // Keyboard bindings (shortcuts)
    $("*")
        .on('keypress', null, 'f2', function(){     // reset search form
            RestartSearch();
        })
        .on('keypress', null, 'f4', function(){     // setup new item from
            setEditForm_Update( $working_fieldset, ShowInputForm_for_NewItem( $working_fieldset.find('form') ) );
            $working_fieldset.show().find("input[name='a_inv_mytitle']").focus();
        });
    // reset search form ( === f2 )
    $("#actor-new-check").on('click',function(){
        RestartSearch();
    });

    // setup new item form ( === f4 )
    $("#actor-item-add").on('click',function(){
        setEditForm_Update( $working_fieldset, ShowInputForm_for_NewItem( $working_fieldset.find('form') ) );
        $working_fieldset.show().find("input[name='a_inv_mytitle']").focus();
    });

    /* активация предсказания названия в базе по названию из описи*/
    $("#actor-title-prediction").on('click', function(){
        $.colorbox({
            href: 'ajax/db.predict.title.php?title='+ encodeURIComponent($working_fieldset.find("input[name='a_inv_mytitle']").val()),
            onClosed: function() { $working_fieldset.find("input[name='a_inv_dbtitle']").focus(); }
        });
        return false;
    });
    /* клик на предсказанном названии */
    $(document).on('click', '.action-insert-this-to-dbtitle-input', function(event){
        SetNamedField($working_fieldset, 'a_inv_dbtitle', $(this).html());
        $.colorbox.close();
    });

    // "скопировать из базы в опись" - click
    $("#actor-title-copy-from-db").on('click',function(event){
        $('input[name="a_inv_mytitle"]').val( $('input[name="a_inv_dbtitle"]').val()).focus();
    });

    // клик на кнопке "ясно" после просмотра добавленных данных
    $("#actor-final").on('click', function(){
        RestartSearch();
    });

    $("#actor-reload-references").on('click',function(){
        reloadSelectors() ? $.jGrowl('Справочники обновлены') : $.jGrowl('Ошибка загрузки справочников');
    });

    /* клик на типассылке в логе */
    $("#log").on('click', '.action-insert-this-to-search-form', function(){
        RestartSearch($(this).html());
    });

    /* клик на типассылке в списке похожих инвентарных кодов */
    /* этот элемент обрабатывается на лету при показе списка возможных инвентарных кодов */
    $(document).on('click', '.action-search-for-this-invcode', function(event){
        RestartSearch($(this).html());
        $.colorbox.close();
    });

    // приятная полезность - после выбора значения в селекте переставляем фокус на кнопку
    // ф4:добавить объект (иначе, пока мы в селекте, ф4 открывает список опций)
    $(document).on('change', '.action-lose-focus-after-select-option', function(){
        $("#actor-item-add").focus();
    });


    $("#db_check_form").on('submit', function(){
        var form_state, result;

        var url = $(this).attr('action');

        // check for empty inventory code
        var check_inv_code = $("#check_this_code").val().trim();

        if (check_inv_code == '' ) {
            jMessage("Необходимо указать инвентарный номер!!! ",3000);
            $("#check_this_code").prop('value','').focus();
            return false;
        }

        var getting = $.get(url+'?inv_code='+check_inv_code, { /* inv_code: encodeURIComponent(check_inv_code)*/ });
        getting.done(function(data){
            result = $.parseJSON(data);

            if (result['state'] == 'error') {
                jMessage("Ошибка проверки инвентарного кода!");
            }

            if (result['state'] == 'multi') {
                // jMessage("Найдено несколько возможных инвентарных кодов!");
                // найдено несколько результатов - показываем мини-список с возможными инвентарными кодами
                $.colorbox({
                    href: 'ajax/db.predict.invcode.php?code='+ check_inv_code,
                    onClosed: function() { $("#check_this_code").focus(); }
                });
                return false;
            } else {
                // var $form = $("#fieldset_working_form");
                var $form = $working_fieldset.find('form');

                if (result['state'] == 'found') {
                    // инвентарный код в БД найден, владелец предмета известен, загрузка полей формы
                    // для редактирования объекта
                    form_state = setEditForm_foundItem($form, result['data']);
                }

                if (result['state'] == 'notfound') {
                    // инвентарный код в БД не найден, владелец неизвестен, загрузка полей формы
                    // для добавления нового объекта с инвентарным кодом
                    form_state = setEditForm_notfoundItem($form, result['data']);
                }

                /* if (result['state'] == 'new') {
                    // добавление совершенно нового объекта в базу, все поля пустые
                    form_state = setEditForm_newItem($form, result['data']);
                } */

                /* */
                setEditForm_Update($working_fieldset, form_state);
                /* */

                $working_fieldset.show();

                $form.find("input[name='a_inv_mytitle']").focus();
            } // event.preventDefault();
        });
        return false;
    });

    $(document).on('submit', "form#form_current_task", function(event){
        var data_is_valid = true;
        // валидация!
        var $this = $(this);

        if ( $this.find("select[name='a_inv_rooms'] option:selected").val() == 0 ) {
            data_is_valid = false;
            jMessage('Не указан кабинет');
        }

        if ( !(($this.find("input[name='a_inv_mytitle']").val().fulltrim() != '') ||
            ($this.find("input[name='a_inv_dbtitle']").val().fulltrim()  != '' )) ) {
            jMessage('Не указано хотя бы одно название предмета ');
            data_is_valid = false;
        }

        if (!data_is_valid) return false;

        $.get( $(this).attr('action'), {
            inv_code        : $this.find("input[name='a_inv_code']").val(),
            inv_id          : $this.find("input[name='a_inv_id']").val(),
            inv_mytitle     : $this.find("input[name='a_inv_mytitle']").val(),
            inv_dbtitle     : $this.find("input[name='a_inv_dbtitle']").val(),
            inv_room        : getSelectedOptionValue($this, 'a_inv_rooms'),     // select rooms
            inv_status      : getSelectedOptionValue($this, 'a_inv_status'),   // select status
            inv_owner       : getSelectedOptionValue($this, 'a_inv_owner'),     // select owner
            inv_date_income_str : $this.find("input[name='a_inv_date_income_str']").val(),
            inv_comment     : $this.find("input[name='a_inv_comment']").val()
        }).done(function(data){
                var res = $.parseJSON(data);
                if (res['state'] == 'added') {
                    log_added++;
                    $("#log-input-added").val(log_added);
                } else if (res['state'] == 'updated') {
                    log_updated++;
                    $("#log-input-updated").val(log_updated);
                }
                var $rf = $("#fieldset_report");

                $rf.find('legend').html(res['message'] + ' id: '+ res['new_id']);

                setEditForm_reportItem($rf, res['data']);

                $('#fieldset_report').show();

                $working_fieldset.hide();

                $("#actor-end").focus();

                var item_desc = '(' + res['new_id'] + ') № <strong class="action-insert-this-to-search-form link-like">' + res['data']['r_code'] + '</strong> / ' + unslash(res['data']['r_dbtitle']) + ' ( ' +unslash(res['data']['r_mytitle']) + ' ), ' + res['data']['r_room']  + ' <br>';

                $("#log").prepend(res['message'] + item_desc);
            });
        //
        return false;
    }); // on submit

});