/* Важно!!! Абстрактный справочник можно расширить полем "группа" (или использовать
* поле int_data как поле группа.
* Если во всех этих полях пусто - селект отлично выстраивается без optgroup*/

function preloadOptionsList(url) // Загружает данные (кэширование в переменную)
{
    var ret;
    $.ajax({
        url: url,
        async: false,
        cache: false,
        type: 'GET',
        success: function(data){
            ret = $.parseJSON(data);
            // ret = data;
        }
    });
    return ret;
}

/* формирует SELECTOR/OPTIONS list с текущим элементом равным [currentid]
data format:
{
    state: ok, error: 0,
    data:   {
            n:  {
                    type:   group       | option
                    value:  (useless)   | item id in reference
                    text:   group title | option text
                    comment:        comment
                }
            }
}
 */

//@todo: описать параметры функции
function BuildSelectorExtended(target, data, currentid) // currentid is zero for ANY
{
    var not_a_first_option_group = 0;
    var ret = '';
    var last_group = '';
    var curr_id = currentid || 0;
    /* var first_opt = first_option || ''; */
    // var currentid = (typeof currentid != 'undefined') ? currentid : 0;
    if (data['error'] == 0) {
        var _target = "select[name='"+target+"']";
        /* if (first_opt != '') {
            $(_target).empty();
            $(_target).append ( first_opt );
        } */

        $.each(data['data'] , function(id, value){
            ret = '';
            if (value['type'] == 'group') {
                // add optiongroup
                if (last_group != value['text']) {
                    last_group = value['text'];
                    ret += '<optgroup label="'+ value['text'] +'" title="' + value['comment'] + '">';
                    not_a_first_option_group++;
                }
            }
            if (value['type'] == 'option') {
                // add option
                ret = '<option value="'+value['value']+'" data-group="'+ last_group +'">'+value['text']+'</option>';
            }

            if (not_a_first_option_group > 0) {
                ret += '</optiongroup>';
            }
            $(_target).append ( ret );
        });
        // $("select[name="+target+"] option[value="+ curr_id +"]").prop("selected",true);
        Selector_SetOption(target, curr_id);
    } else {
        $("select[name="+target+"]").prop('disabled',true);
    }
}

// формирует SELECTOR/OPTIONS list с текущим элементом равным [currentid]
// target - ИМЯ селектора
// СТАРАЯ версия (для абстрактного справочника)
function BuildSelector(target, data, currentid) // currentid is 1 for NEW
{
    if (data['error'] == 0) {
        var _target = "select[name='"+target+"']";
        $.each(data['data'], function(id, value){
            $(_target).append('<option value="'+id+'">'+value+'</option>');
        });
        if (typeof  currentid != 'undefined') {
            Selector_SetOption(target, currentid);
        }
    } else {
        // $("select[name="+target+"]").prop('disabled',true);
    }
    $("select[name="+target+"]").prop('disabled',!(data['error']==0));
}

function Selector_SetOption(name, option_value)
{
    var cid = option_value || 0;
    $("select[name="+name+"] option[value="+ cid +"]").prop("selected",true);
}
