var rooms_selector = preloadOptionsList('core/ref.rooms.getoptionslist.php');
var quality_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_quality');
var task_mode = 'onload';

function ResetTask()
{
    task_mode = '';
    $("#fieldset_form").trigger('reset').hide();
    $("#check_this_code").prop('value','').focus();
}

$(document).ready(function(){
    $.ajaxSetup({cache: false});
    BuildSelectorExtended('a_inv_rooms', rooms_selector);

    BuildSelector('a_inv_quality', quality_selector, 1);
    // onload
    $("#inv_code").focus();
    // bind check button
    $("#check_this_code").on('click', function(){
        $("#error_message").fadeOut(600);
    });

    $("#db_check_form").on('submit', function(event){
        var url = $(this).attr('action');
        var check_inv_code = $("#check_this_code").val().trim();
        if (check_inv_code == '' ) {
            $("#error_message").text("Необходимо указать инвентарный номер!!! ").fadeIn(600);
            $("#check_this_code").prop('value','').focus();
            return false;
        }

        var getting = $.get(url, { inv_code: check_inv_code });
        var current_task_legend = '';
        var current_task_action = '';
        var prediction_button_is_visible;
        getting.done(function(data){
            var result = $.parseJSON(data);
            if (result['state'] == 'error') { /* error */
            } else {
                $form = $("#form_current_task");
                $form.trigger('reset');

                if (result['state'] == 'found') {
                    // edit
                    $form.find("input[name='a_inv_id']").val( result['data']['inv_id'] );
                    $form.find("input[name='a_inv_code']").val(result['data']['inv_code']);
                    $form.find("input[name='a_inv_dbtitle']").val(result['data']['inv_title']);
                    $form.find("input[name='a_inv_date_income']").val(result['data']['inv_date_income']);
                    $form.find("input[name='a_inv_comment']").val(result['data']['inv_comment']);
                    // select a_inv_rooms
                    $form.find("input[name='a_inv_rooms'] option[value='" + parseInt(result['data']['inv_room']) + "']").prop("selected", true);
                    // select a_inv_quality
                    // $form.find("input[name='a_inv_quality'] option[value='" + parseInt(result['data']['inv_quality']) + "']").prop("selected", true);

                    Selector_SetOption('a_inv_quality', parseInt(result['data']['inv_quality']));

                    // messages and actors
                    current_task_action = '';
                    current_task_legend = 'Обновление объекта в базе';
                    prediction_button_is_visible = 0;
                    task_mode = 'edit';
                }

                if (result['state'] == 'notfound') {
                    // add
                    $form.find("input[name='a_inv_code']").val(result['data']['inv_code']);
                    $form.find("input[name='a_inv_id']").val( 0 );
                    Selector_SetOption('a_inv_quality', 1);
                    // messages and actors
                    current_task_action = '';
                    current_task_legend = 'Добавление объекта в базу';
                    prediction_button_is_visible = 1;
                    task_mode = 'add';
                }
                $("#fieldset_form_current_task").html(current_task_legend);
                $form.attr('action', current_task_action);
                (prediction_button_is_visible == 1 ? $("#actor-title-prediction").show() : $("#actor-title-prediction").hide());
                $("#fieldset_form").show();
                $form.find("input[name='a_inv_mytitle']").focus();
            } // event.preventDefault();
        });
        return false;
    }); // db_check_form
    $("#actor-title-prediction").on('click', function(){
        /* $("#form_current_task").trigger('reset'); */
        alert( $("#form_current_task").find("input[name='a_inv_id']").val() );
    });
    // Keyboard bindings
    $("*").on('keypress', null, 'f2', function(){ResetTask()});
    $("#actor-new-check").on('click', function(){ResetTask()});

    // submit update/edit form
    //@todo: last
    $("#form_current_task").on('submit', function(){
        // test 'task_mode' for action (хотя реально эта переменная не нужна, мы базируемся на экшене,
        // он же нам и возвращает результаты вставки/обновления
    });
});


