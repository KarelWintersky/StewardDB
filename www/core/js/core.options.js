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

// формирует SELECTOR/OPTIONS list с текущим элементом равным [currentid]
// target - ИМЯ селектора
function BuildSelectorExtended(target, data, currentid) // currentid is 0 for ANY
{
    var not_a_first_option_group = 0;
    var ret = '';
    var last_group = '';
    var curr_id = currentid || 0;
    // var currentid = (typeof currentid != 'undefined') ? currentid : 0;
    if (data['error'] == 0) {
        var _target = "select[name='"+target+"']";

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
            // alert(ret);
            $(_target).append ( ret );
        });
        $("select[name="+target+"] option[value="+ curr_id +"]").prop("selected",true);
    } else {
        $("select[name="+target+"]").prop('disabled',true);
    }
}

function strpos (haystack, needle, offset) {
    var i = (haystack+'').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}