(function ($) {
    $.fn.datagrid.methods.filter = function (jq, e) {
        jq.each(function (index, element) {
            var target = e.data ? e.data.target : (e.currentTarget ? e.currentTarget : '#' + e);
            var data = manager.utils.deserialize($(target).parent().find("input, textarea, select").serialize());
            $(element).datagrid('load', data);
        });
    };

    $.extend($.fn.validatebox.defaults.rules, {
        regexp: {
            validator: function (value, param) {
                return manager.adapters.easyui.validate(param[0], param[1], value);
            },
            message: 'Este valor não é válido.'
        },
        range: {
            validator: function (value, param) {
                return manager.adapters.easyui.validate(param[0], param[1], value);
            },
            message: 'Número fora da faixa válida.'
        },
        date: {
            validator: function (value, param) {
                return manager.adapters.easyui.validate(param[0], param[1], value);
            },
            message: 'Data inválida.'
        },
        daterange: {
            validator: function (value, param) {
                return manager.adapters.easyui.validate(param[0], param[1], value);
            },
            message: 'Data inválida.'
        }
    });

})(jQuery);

/*
 * Customizações necessárias para os controles MTextField
 */

/*
 * Adaptação do formato de datas de EasyUI para DD/MM/YYYY em MCalendarField
 */

$.fn.datebox.defaults.formatter = function(date){
	var y = date.getFullYear();
	var m = date.getMonth()+1;
	var d = date.getDate();
	return d+'/'+m+'/'+y;
};

$.fn.datebox.defaults.parser = function(s){
    if (s != '') {
        var date = s.split('/');
        var y = parseInt(date[2]);
        var m = parseInt(date[1]) - 1;
        var d = parseInt(date[0]);
        var dt = new Date(y,m,d,0,0,0,0);
    } else {
        var dt = new Date();
    }    
    return dt;
};

/*
 * Opções default para o plugin 'jQuery Mask Plugin'
 */
$.jMaskGlobals = {
    nonInput: 'td,span,div', 
    dataMask: true, 
    watchInterval: 300,
    watchInputs: false, 
    watchDataMask: false,
    byPassKeys: [9, 16, 17, 18, 36, 37, 38, 39, 40, 91],
    translation: {
      '0': {pattern: /\d/},
      '9': {pattern: /\d/, optional: true},
      '#': {pattern: /\d/, recursive: true},
      '*': {pattern: /[a-zA-Z0-9]/},
      'A': {pattern: /[a-zA-Z0-9]/},
      'a': {pattern: /[a-zA-Z]/},
      'S': {pattern: /[a-zA-Z]/}
    }
  };

$.fn.inView = function(element){
    //Window Object
    var win = $(element);
    //Object to Check
    obj = $(this);
    //the top Scroll Position in the page
    var scrollPosition = win.scrollTop();
    //the end of the visible area in the page, starting from the scroll position
    var visibleArea = win.scrollTop() + win.height();
    //the end of the object to check
    var objEndPos = (obj.offset().top + obj.outerHeight());
    //var objEndPos = (obj.offset().top + win.height());
    return(visibleArea >= objEndPos && scrollPosition <= objEndPos ? true : false)
};

$.fn.scrollView = function () {
    return this.each(function () {
        $('html, body').animate({
            scrollLeft: $(this).offset().left
        }, 500);
    });
}
