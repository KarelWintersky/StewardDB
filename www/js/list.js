var rooms_selector, status_selector, owners_selector, family_selector, subfamily_selector;
var current_family = 0;
var backend_url = '/ajax/db.list.all.php';

    function list_reloadSelectors()
{
    var result = true;
    if (status_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_status')) {
        BuildSelector('status', status_selector, 'любое!', 1);
        console.log('Категории качества загружены; ');
    } else {$.jGrowl('Ошибка загрузки категорий качества'); result=false; }

    if (owners_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_owners')) {
        BuildSelector('owner', owners_selector, 'любой!');
        console.log('Владельцы загружены; ');
    } else {$.jGrowl('Ошибка загрузки владельцев '); result=false; }

    if (rooms_selector = preloadOptionsList('core/ref.rooms.getoptionslist.php')) {
        BuildSelectorExtended('room', rooms_selector, 'любое!');
        console.log('Помещения загружены; ');
    } else {$.jGrowl('Ошибка загрузки помещений '); result=false; }

    /* if (family_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_family')) {
        BuildSelector('family', family_selector, 'любой!');
        console.log('Виды объектов загружены; ');
    } else {$.jGrowl('Ошибка загрузки видов '); result=false; } */

    /* if (subfamily_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_subfamily&group=' + current_family)) {
        BuildSelector('subfamily', subfamily_selector, 'любой!');
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

    setSelectorsByHash(".search-selector");
    $(".action-hash-selectors").on('change', '.search-selector', function(){
        console.log( $(this).attr('name') + ' = ' + $(this).val()  );
        setHashBySelectors('.search-selector');
    });

    // если хэш установлен - нужно загрузить статьи согласно выбранным позициям
    var query = (window.location.hash).substr(1);
    if (query !== '') {
        console.log(backend_url + query + '<br>');
        $("#panel-output-span").empty().load(backend_url + '?' + query);
    }


    /* bindings */
    $(".action-reload-subfamily").on('change', function(){
        $("select[name='subfamily']").empty().prop('disabled', true);

        current_family = getSelectedOptionValue('panel-search-selectors','family');

        if (current_family > 0) {
            if (subfamily_selector = preloadOptionsList('core/ref.abstract.getoptionslist.php?ref=ref_subfamily&group=' + current_family)) {
                BuildSelector('subfamily', subfamily_selector, 'любой!');
                console.log('Типы объектов загружены; ');
                $.jGrowl('Выбран вид ' + getSelectedOptionText('panel-search-selectors','family'));
            } else {
                $.jGrowl('Таких типов нет! ');
            }
        } else {
            $.jGrowl('Выбран любой тип и вид! ');
            // установить в селекте "любой = 0" и заэнейблить список
            BuildSelectorEmpty('subfamily', 'Любой', 0);
            $("select[name='subfamily']").prop('disabled', false);
        }
    });

    $('#actor-show-selected').on('click', function(){
        var room = getSelectedOptionValue('panel-search-selectors', 'room');
        var family = getSelectedOptionValue('panel-search-selectors', 'family');
        var subfamily = getSelectedOptionValue('panel-search-selectors', 'subfamily', 0);
        var status = getSelectedOptionValue('panel-search-selectors', 'status');
        var owner = getSelectedOptionValue('panel-search-selectors', 'owner');

        var query = (status == 2) ? '?get_total_cost=1' : '?get_total_cost=0';
        if (subfamily === 'undefined') { subfamily = 0 }       // if selector[subfamily] disabled
        if (family === 'undefined') { family = 0 }             // if selector[family] disabled

        query += "&room="+room;
        query += "&family="+family;
        query += "&subfamily="+subfamily;
        query += "&status="+status;
        query += "&owner="+owner;

        console.log(backend_url + query + '<br>');
        $("#panel-output-span").empty().load(backend_url + query);
    });

    //$("#timecounter")
    $(document)
        .on('ajaxSend', function(){
            $("#ajax-spinner").show();
            console.log('ajaxSend');
        })
        .on('ajaxComplete', function(){
            $("#ajax-spinner").hide();
            console.log('ajaxComplete');
        });

    $("#actor-show-all").on('click', function(){
        var query = '?summary=0';
        console.log(backend_url + query + '<br>');
        $("#panel-output-span").empty().load(backend_url + query);
    });

    $("#actor-reset-selectors").on('click', function(){
        $(".search-selector").each(function(i, element) {
            Selector_SetOption($(this).attr('name'), 0)
        });
        setHashBySelectors();
    });

    $("#actor-excel-export").on('click', function(){
        tableToExcel('exportable', 'export');
    });

    $(document).on('click', '.action-show-extended-info-for-id', function(){
        var id = $(this).attr('data-id');
        $.colorbox({
            href: 'ajax/db.list.item.php?id='+id,
            width: 800
        });
    });

    $(document).on('click', '.action-edit-by-id', function(){
        var id = $(this).attr('data-id');
        document.location.href = 'edit.php?id='+id;
    });

});