var rooms_selector, status_selector, owners_selector, family_selector, subfamily_selector;
var current_family = 0;
var url = '/ajax/db.list.all.php';

    function list_reloadSelectors()
{
    var result = true;
    if (status_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_status')) {
        BuildSelector('select_status', status_selector, 'любое!', 1);
        console.log('Категории качества загружены; ');
    } else {$.jGrowl('Ошибка загрузки категорий качества'); result=false; }

    if (owners_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_owners')) {
        BuildSelector('select_owner', owners_selector, 'любой!');
        console.log('Владельцы загружены; ');
    } else {$.jGrowl('Ошибка загрузки владельцев '); result=false; }

    if (rooms_selector = preloadOptionsList('core/ref.rooms.getoptionslist.php')) {
        BuildSelectorExtended('select_rooms', rooms_selector, 'любое!');
        console.log('Помещения загружены; ');
    } else {$.jGrowl('Ошибка загрузки помещений '); result=false; }

    /* if (family_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_family')) {
        BuildSelector('select_family', family_selector, 'любой!');
        console.log('Виды объектов загружены; ');
    } else {$.jGrowl('Ошибка загрузки видов '); result=false; } */

    /* if (subfamily_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_subfamily&group=' + current_family)) {
        BuildSelector('select_subfamily', subfamily_selector, 'любой!');
        console.log('Типы объектов загружены; ');
    } else {$.jGrowl('Ошибка загрузки типов '); result=false; } */

    return result;
}

function list_reloadSubfamilySelector(family)
{
    if (typeof family == undefined) return false;
}

$(document).ready(function(){
    $.ajaxSetup({cache: false});
    /* onload */

    list_reloadSelectors() ? $.jGrowl('Справочники загружены') : $.jGrowl('Ошибка загрузки справочников');

    /* bindings */
    $(".action-reload-subfamily").on('change', function(){
        $("select[name='select_subfamily']").empty().prop('disabled', true);

        current_family = getSelectedOptionValue('panel-search-selectors','select_family');

        if (current_family > 0) {
            if (subfamily_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_subfamily&group=' + current_family)) {
                BuildSelector('select_subfamily', subfamily_selector, 'любой!');
                console.log('Типы объектов загружены; ');
                $.jGrowl('Выбран вид ' + getSelectedOptionText('panel-search-selectors','select_family'));
            } else {
                $.jGrowl('Таких типов нет! ');
            }
        } else {
            $.jGrowl('Выбран любой тип и вид! ');
            // установить в селекте "любой = 0" и заэнейблить список
            BuildSelectorEmpty('select_subfamily', 'Любой', 0);
            $("select[name='select_subfamily']").prop('disabled', false);
        }
    });

    $('#actor-show-selected').on('click', function(){
        var room = getSelectedOptionValue('panel-search-selectors', 'select_rooms');
        var family = getSelectedOptionValue('panel-search-selectors', 'select_family');
        var subfamily = getSelectedOptionValue('panel-search-selectors', 'select_subfamily', 0);
        var status = getSelectedOptionValue('panel-search-selectors', 'select_status');
        var owner = getSelectedOptionValue('panel-search-selectors', 'select_owner');

        var query = (status == 2) ? '?get_total_cost=1' : '?get_total_cost=0';
        if (subfamily === 'undefined') { subfamily = 0 }       // if selector[subfamily] disabled
        if (family === 'undefined') { family = 0 }             // if selector[family] disabled

        query += "&room="+room;
        query += "&family="+family;
        query += "&subfamily="+subfamily;
        query += "&status="+status;
        query += "&owner="+owner;

        console.log(url + query + '<br>');
        $("#panel-output-span").empty().load(url + query);
    });

    $("#actor-show-all").on('click', function(){
        var query = '?summary=0';
        console.log(url + query + '<br>');
        $("#panel-output-span").empty().load(url + query);
    });

    $("#actor-reset-selectors").on('click', function(){
        $(".search-selector").each(function(i, element) {
            Selector_SetOption($(this).attr('name'), 0)
        });
    });

    $(document).on('click', '.action-show-extended-info-for-id', function(){
        var id = $(this).attr('data-id');
        $.colorbox({
            href: 'ajax/db.list.item.php?id='+id,
            width: 800
        });
    });


});