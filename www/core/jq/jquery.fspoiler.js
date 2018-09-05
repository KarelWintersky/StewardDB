(function($){
    jQuery.fn.fSpoiler = function(options){
        // extend options
        options = $.extend({
            'opened'                : ' (скрыть)',
            'closed'                : ' (показать)',
            'usebutton'             : 0,
            'legend_is_selectable'  : 0,
            'visible'               : false
        }, options);
        var defaultStyles = {
            'legend_notselectable' : {
                '-moz-user-select'      : 'none',
                '-ms-user-select'       : 'none',
                '-khtml-user-select'    : 'none',
                '-webkit-user-select'   : 'none',
                'user-select'           : 'none'
            },
            'legend_isselectable'   : {}
        };
        var _inner, _this, _legend;
        var _legend_text = '';
        var _opened, _closed;

        function init(element)
        {
            _this = $(element);
            _legend = _this.find('legend');
            _legend_text = _legend.html();
            _inner = _this.find('fieldset');
            _inner.hide();

            _opened = (options['usebutton'] == 1) ? '<button>'+ options['opened'] +'</button>' : options['opened'];
            _closed = (options['usebutton'] == 1) ? '<button>'+ options['closed'] +'</button>' : options['closed'];

            _legend
                .append(_closed)
                .css(options['legend_is_selectable'] == 1 ? defaultStyles['legend_isselectable'] : defaultStyles['legend_notselectable']);

            return _legend;
        }

        var fn = function(){
            init(this).on('click', function(){
                var _legend_content = $(_inner).is(':visible')
                                    ? _legend_text + _closed
                                    : _legend_text + _opened;
                _legend.html(_legend_content);
                _inner.toggle();
            })
        };

        return this.each(fn)
    }
})(jQuery);