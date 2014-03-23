/* php strpos() аналог.
* Параметры: (стог сена, иголка, смещение поиска) */
function strpos (haystack, needle, offset) {
    var i = (haystack+'').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}

/* прототипные функции trim */
String.prototype.trim = String.prototype.trim || function(){return this.replace(/^\s+|\s+$/g, ''); };
String.prototype.ltrim = String.prototype.ltrim || function(){return this.replace(/^\s+/,''); };
String.prototype.rtrim = String.prototype.rtrim || function(){return this.replace(/\s+$/,''); };
String.prototype.fulltrim = String.prototype.fulltrim || function(){return this.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,' ');};

/* Разделить строку по параметрам © http://a2x.ru/?p=140 */
/* возвращает массив вида 'valuename' => 'valuedata' */
function getQuery( queryString , limiter)
{
    var vars = queryString.split((limiter || '&')); //делим строку по & - parama1=1
    var arr = [];
    for (var i=0 , vl = vars.length; i < vl; i++)
    {
        var pair = vars[i].split("="); //делим параметр со значением по =, и пишем в ассоциативный массив arr['param1'] = 1
        arr[pair[0]] = pair[1];
    }
    return arr;
}

/*
установка URL-хэша на основе нескольких селектов (выпадающих списков), имеющих
определенный class (по умолчанию ".search_selector") и name (которое превращается
в элемент hash-строки: для <select name="letter" class="search_selector">
соответствующая запись в URL будет: /...#letter=..[&something=value]
*/
function setHashBySelectors(search_selector)
{
    // see also http://stackoverflow.com/a/5340658
    // для русских букв возможно потребуется экранирование
    var selector = search_selector || ".search_selector";
    var hashstr = '';
    var val, name;
    $.each( $(".search_selector") , function(id, data) {
        val = $(data).val();
        name = $(data).attr('name');
        if (val != '0')
            hashstr += name + "=" + val + "&";
        // stackoverflow рекомендует экранирование символов, но пока что везде где
        // тестировали прекрасно работало без экранирования (в адресную строку пишется русская буква)
        // hashstr += encodeURIComponent(key) + "=" + encodeURIComponent(arr[key]) + "&";
    } );
    // хэш будет выглядеть '#...&', поэтому при установке надо отрезать последний символ.
    // пустой хэш - '#&' - его не нужно устанавливать.
    // window.location.hash = (hashstr.length > 2) ? hashstr.substring(0, hashstr.length-1) : '';
    // но лучше попробуем использовать HTML5 API
    if (hashstr.length > 2) {
        window.location.hash = hashstr.substring(0, hashstr.length-1)
    } else {
        if ('pushState' in history) {
            history.pushState('', window.title, window.location.pathname + window.location.search);
        } else {
            window.location.hash = '';
        }
    }
}

/* устанавливает селекторы с указанным классом на основе URL-хэша */
function setSelectorsByHash(target)
{
    var sel_name;
    var sel_value;
    var hashes_arr = getQuery((window.location.hash).substr(1));

    $.each( $(target), function(id, data) {
        sel_name = $(data).attr('name'); // selector's name attribute
        sel_value = hashes_arr[sel_name] != 'undefined' ? hashes_arr[sel_name] : 0;
        $(target+"[name="+sel_name+"] option[value="+sel_value+"]").prop("selected",true);
    } );
}

/* работа с куки */
function getCookie(name){
    var pattern = new RegExp(name + "=.[^;]*");
    var matched = document.cookie.match(pattern);
    if(matched){
        var cookie = matched[0].split('=');
        return cookie[1]
    }
    return false
}

function setCookie (name, value, expires, path, domain, secure) {
    document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}