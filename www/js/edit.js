var rooms_selector, status_selector, owners_selector;

function jMessage(message, delay)
{
    var d = delay || 2000;
    $.jGrowl(message,d);
}

function reloadSelectors_forEdit(status, owner, room)
{
    var result = true;
    var st = status || 0;
    var own = owner || 0;
    var rm = room || 0;
    if (status_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_status')) {
        BuildSelector('a_inv_status', status_selector, 'выбрать!', st);
        console.log('Категории качества загружены; ');
    } else {$.jGrowl('Ошибка загрузки категорий качества'); result=false; }

    if (owners_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_owners')) {
        BuildSelector('a_inv_owner', owners_selector, 'выбрать!', own);
        console.log('Владельцы загружены; ');
    } else {$.jGrowl('Ошибка загрузки владельцев '); result=false; }

    if (rooms_selector = preloadOptionsList('core/ref.rooms.getoptionslist.php')) {
        BuildSelectorExtended('a_inv_rooms', rooms_selector, 'выбрать!', rm);
        console.log('Помещения загружены; ');
    } else {$.jGrowl('Ошибка загрузки помещений '); result=false; }
    return result;
}

function db_SaveItem(target_form)
{
    var data_is_valid = true;
    var $this = $(target_form);

    if ( $this.find("select[name='a_inv_rooms'] option:selected").val() == 0 ) {
        data_is_valid = false;
        jMessage('Не указан кабинет!');
    }

    if ( $this.find("select[name='a_inv_status'] option:selected").val() == 0 ) {
        data_is_valid = false;
        jMessage('Не выбран статус!');
    }

    if ( $this.find("select[name='a_inv_owner'] option:selected").val() == 0 ) {
        data_is_valid = false;
        jMessage('Не выбран владелец!');
    }

    if ( !(($this.find("input[name='a_inv_mytitle']").val().fulltrim() != '') ||
        ($this.find("input[name='a_inv_dbtitle']").val().fulltrim()  != '' )) ) {
        jMessage('Не указано хотя бы одно название предмета ');
        data_is_valid = false;
    }

    if (!data_is_valid) return false;

    $.get( $this.attr('action') , {
        inv_code        : $this.find("input[name='a_inv_code']").val(),
        inv_id          : $this.find("input[name='a_inv_id']").val(),
        inv_mytitle     : $this.find("input[name='a_inv_mytitle']").val(),
        inv_dbtitle     : $this.find("input[name='a_inv_dbtitle']").val(),
        inv_room        : getSelectedOptionValue($this, 'a_inv_rooms'),     // select rooms
        inv_status      : getSelectedOptionValue($this, 'a_inv_status'),   // select status
        inv_owner       : getSelectedOptionValue($this, 'a_inv_owner'),     // select owner
        inv_date_income_str : $this.find("input[name='a_inv_date_income_str']").val(),
        inv_price       : $this.find("input[name='a_inv_price']").val(),
        inv_comment     : $this.find("input[name='a_inv_comment']").val()
    }).done(function(data){
            var res = $.parseJSON(data);
            if (res['state'] != 'updated') {
                $.jGrowl('Ошибка обновления данных!')
            } else {
                window.history.back();
            }
        });
    event.preventDefault();
    return false;
}

function db_RemoveItem()
{
    if (confirm("Точно удалить? Да/нет")) {
        // alert('ajax/db.item.delete.php?id='+window.i_id);
        $.get('ajax/db.item.delete.php?id='+window.i_id)
            .done(function(data){
                var res = $.parseJSON(data);
                if (res['state'] != 'deleted') {
                    $.jGrowl('Ошибка удаления данных!')
                } else {
                    window.history.back();
                }
            });

    }
}

$(document).ready(function(){
    var $working_fieldset = $("#fieldset_working_form");
    $.ajaxSetup({cache: false});
    /* onload */

    !reloadSelectors_forEdit(i_status, i_owner, i_room) ? $.jGrowl('Ошибка загрузки справочников') : {};

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

    /* перезагрузить справочники */
    $("#actor-reload-references").on('click',function(){
        reloadSelectors() ? $.jGrowl('Справочники обновлены') : $.jGrowl('Ошибка загрузки справочников');
    });

    /* назад */
    $("#actor-back-to-list").on('click', function(){
        // document.location.href = document.referrer;
        window.history.back();
    });

    /* тут вопрос юзерфрендовости:
    * а) удалив/обновив, можно убрать форму редактирования, нарисовать "все ок" и сделать кнопку "назад"
    * б) удалив/обновив, можно сразу идти назад */

    $("#action-delete-item").on('click', function(){
        db_RemoveItem();
    });

    $('#form_item_edit').on('click', '#action-save-item', function(event){
        db_SaveItem('#form_item_edit');
    });

    // Keyboard bindings (shortcuts)
    $("*")
        .on('keypress', null, 'f8', function(){     // delete
            db_RemoveItem();
            return false;
        })
        .on('keypress', null, 'f3', function(){     // setup new item from
            db_SaveItem('#form_item_edit');
            return false;
        });


});