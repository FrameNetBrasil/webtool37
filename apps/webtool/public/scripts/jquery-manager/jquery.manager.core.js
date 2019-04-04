/* Copyright [2011, 2012, 2013] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; você pode redistribuí-lo e/ou 
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada 
 * pela Fundação do Software Livre (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, 
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL 
 * em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a 
 * Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

/**
 * Maestro 2.0 - Manager Core
 */
var manager = {
    version: 'Manager 3.0',
    contentElement: '',
    baseURL: window.location.href.replace(/\/index\.php\/(.*)/, "/index.php/"),
    getApp: function() {
        var re = new RegExp(/\/index\.php\/([^\/]*)\//, "g");
        var matches = re.exec(window.location.href);
        return matches[1];
    },
    addAppToAction: function  (action) {
        var app = manager.getApp();
        var appRE = "^(\/?)" + app + "\/";
        var re = new RegExp(appRE, "gi");
        var actionBase = action.substr(1).replace(re, '');
        return action.charAt(0) + app + '/' + actionBase;
    },
    onLoad: [],
    onSubmit: [],
    onBeforePost: function () {
    },
    onBeforeGet: function () {
    },
    onAfterGet: function () {
    },
    onAfterPost: function () {
    },
    beforeRequest: function () {
    },
    afterRequest: function () {
    },
    ready: function () {
    },
    events: {},
    type: function (o) {
        return !!o && Object.prototype.toString.call(o).match(/(\w+)\]/)[1];
    },
    registerEvents: function (events) {
        for (id in events) {
            jQuery.each(events[id], function (index, event) {
                manager.registerEvent(event);
            });
        }
        manager.events = events;
    },
    registerEvent: function (event) {
        try {
            if (manager.type(event.handler) == 'Function') {
                var eventHandler = event.handler;
            } else {
                var eventHandler = new Function("event", (event.preventDefault ? "event.preventDefault();" : "") + event.handler);
            }
            node = manager.byId(event.id);
            if (!node) {
                console.error('registerEvent ' + event.id + ':' + event.event + '. Error: node not found!');
                return null;
            }
            //console.log('registerEvent ' + event.id + ':' + event.event);
            node.on(event.event, eventHandler);
        } catch (e) {
            console.error('registerEvent ' + event.id + ':' + event.event + '. Error: ' + e);
        }
    },
    _jq: function (id) {
        return "#" + id.replace(/(:|\.|\[|\])/g, "\\$1");
    },
    byId: function (id) {
        return jQuery(manager._jq(id));
    },
    getLayoutElement: function (/*String|DOMNode*/startNode) {
        var node = manager.byId(startNode);
        return node.closest("[class*=mElement]").attr("id") || jQuery(document);
    },
    getParentForm: function (/*String|DOMNode*/startNode) {
        var node = manager.byId(startNode);
        return node.closest("form").attr("id") || jQuery(document);
    },
    alert: function (msg) {
        alert(msg);
    },
    copyValue: function (idFrom, idTo) {
        manager._jq(idTo).attr('value', manager._jq(idFrom).attr('value'));
    },
    parse: function (id, onComplete) {
        //console.log('Executando parser. id = ' + id);
        manager.parser.parse(id);
    },
    _getDataType: function (header) {
        var re = /(plain|html|json|xml)/g;
        var dt = header.match(re);
        return (dt ? dt[0] : '');
    },
    _handleResponse: {
        success: function (response, textStatus, jqXHR, context) {
            //console.log(response);
            if (typeof jqXHR === 'object') {
                var dataType = manager._getDataType(jqXHR.getResponseHeader('Content-Type')); // html | json | plain | xml
            } else {
                var dataType = jqXHR;
            }
			if (dataType == 'plain') {
				dataType = 'html';
			}
            context['response'] = response;
            manager._handleResponse[dataType](context);
        },
        error: function (jqXHR, textStatus, errorThrown, context) {
            var dataType = manager._getDataType(jqXHR.getResponseHeader('Content-Type')); // html | json | plain | xml
            manager.alert('Ajax Error: ' + textStatus + ' ' + dataType);
        },
        html: function (context) {
            //console.log(context);
            var html = context.response;
            if (context.element) {
                element = '#' + context.element;
            } else {
                element = (manager.contentElement != '') ? '#' + manager.contentElement : 'body';
            }
            //console.log('element = ' + element);
            $(element).html(html);
            manager._handleResponse.parse(element);
        },
        parse: function (element) {
            manager.parse(element);
            var $scripts = $(element).find(".mScripts");
            if ($scripts.notempty()) {
                var pageId = $scripts.attr('id');
                if (typeof manager.onLoad[pageId] === 'function') {
                    manager.onLoad[pageId]();
                }
            }
        },
        json: function (context) {
            var response = context.response;
            //console.log(response);
            if (response.type == 'page') {
                var element = '#' + context.element;
                var html = response.data;
                $(element).html(html);
                manager._handleResponse.parse(element);
            }
            if (response.type == 'prompt') {
                manager.doPrompt(response.data);
            }
            if (response.type == 'redirect') {
                manager.doRedirect(response.data);
            }
            if (response.type == 'window') {
                manager.doWindow(response.data);
            }
            if (response.type == 'file') {
                manager.doGetFile(response.data);
            }
        }
    },
    doPostBack: function (idForm) {
        //Callback handler for form submit event
        //console.log(idForm);
        var $form = $('#' + idForm);
        $form.submit(function (e) {
            //console.log(e);
            var canSubmit = true;
            if (manager.onSubmit[idForm]) {
                canSubmit = manager.onSubmit[idForm]();
            }
            if (canSubmit) {
                var element = 'manager-post-response';
                if ($('#' + element).notempty()) {
                    $('#' + element).remove();
                }
                var $element = $("<div id='" + element + "' style='overflow: hidden; display: none;'/>");
                $("body").append($element);
                var context = {
                    element: element
                };
                var formObj = $(this);
                var formURL = formObj.attr("action");
                var formData = new FormData(this);
                manager.doRequest({
                    url: formURL,
                    type: 'POST',
                    data: formData,
                    //dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data, textStatus, jqXHR) {
                        manager._handleResponse.success(data, textStatus, jqXHR, context);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        manager._handleResponse.error(jqXHR, textStatus, errorThrown, context);
                    }
                });
            }
            e.preventDefault(); //Prevent Default action.
            $form.unbind(e);
        });
        $form.submit(); //Submit the form
    },
    doPost: function (element, url, idForm) {
        if (typeof idForm == 'undefined') {
            idForm = manager.getParentForm(element);
        }
        if (url) {
            $('#' + idForm).attr('action', url);
        }
        if (idForm) {
            manager.doPostBack(idForm);
        } else {
            manager.doGet(url, '');
        }
    },
    doGet: function (url, element, wrapper) {
        //console.log(url);
        //console.log(wrapper);
        if (wrapper) {
            //console.log(wrapper);
            if ($('#' + wrapper).notempty()) {
                $('#' + wrapper).remove();
            }
            var $wrapper = $("<div id='" + wrapper + "' style='overflow: hidden; display: none;'/>");
            $("body").append($wrapper);
            element = wrapper;
        }
        //console.log(element);
        var context = {
            element: element
        };
        manager.doRequest({
            async: false,
            type: "GET",
            url: url,
            success: function (data, textStatus, jqXHR) {
                manager._handleResponse.success(data, textStatus, jqXHR, context);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                manager._handleResponse.error(jqXHR, textStatus, errorThrown, context);
            }
        });
    },
    doAction: function (action, id) {
        var maction = new manager.action;
        maction.exec(action, id);
    },
    doAjaxText: function (url, element, idBase) {
		//console.log(url + ' ' + element + ' ' + idBase);
        var idForm = manager.getParentForm(idBase);
        var form = '#' + idForm;
        manager.doRequest({
            type: "POST",
            url: url,
            data: $(form).serialize(),
            dataType: "html",
            success: function (data, textStatus, jqXHR) {
                manager._handleResponse.html({response: data, element: element});
            },
            error: function (jqXHR, textStatus, errorThrown) {
                manager._handleResponse.error(jqXHR, textStatus, errorThrown, context);
            }
        });
    },
    doAjax: function (url, callback, idBase_data, asyncMode) {
        var async = asyncMode || false;
        var data = {};
        var processData = true;
        var contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
        if (typeof idBase_data === "string") {
            var idForm = manager.getParentForm(idBase_data);
            var form = '#' + idForm;
            data = new FormData($(form)[0]);
            processData = contentType = false;
        } else {
            data = idBase_data;
        };
        manager.doRequest({
            type: "POST",
            url: url,
            data: data,
            contentType: contentType,
            async: async,
			//encoding:"UTF-8",
            //cache: false,
            processData: processData,
            success: function (data, textStatus, jqXHR) {
                if (typeof callback == 'function') {
                    callback(data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                manager._handleResponse.error(jqXHR, textStatus, errorThrown, context);
            }
        });
    },
    doRedirect: function (url) {
        window.location = url;
    },

    doRequest: function (options) {
        options.async = options.async || false;
        manager.beforeRequest(options);
        $.ajax(options);
        manager.afterRequest();
    },
    doPrompt: function (data) {
        if ($('#manager-prompt').notempty()) {
            var $managerPrompt = $('#manager-prompt');
        } else {
            var $managerPrompt = $("<div id='manager-prompt' style='overflow: hidden; display: none;'/>");
            $("body").append($managerPrompt);
        }
        $managerPrompt.html(data);
        manager._handleResponse.html({response: data, element: 'manager-prompt'});
    },
    doWindow: function (url, target) {
        if ((target == null) || (target == '')) {
            target = 'managerWindow';
        }
        var w = screen.width * 0.95;
        var h = screen.height * 0.80;
        var wnd = window.open(url, target,
            'toolbar=no,width=' + w + ',height=' + h + ',scrollbars=yes,' +
            'top=0,left=0,statusbar=yes,resizeable=yes');
    },
    doGetFile: function (url) {
        //console.log('===' + url);
        $.fileDownload(url), {
            preparingMessageHtml: "We are preparing your report, please wait...",
            failMessageHtml: "There was a problem generating your report, please try again."
        };
    },
    doGetObject: function (url) {
        var object = null;
        manager.doAjax(url, function (data) {
            object = data;
        });
        return object;
    }
};
/**
 * Maestro 3.0 - Manager Loader
 */

(function () {
    var modules = {
        maction: {
            js: 'jquery.manager.action.js'
        },
        melement: {
            js: 'jquery.manager.element.js'
        }
    };

    var locales = {
        'en': 'manager-lang-en.js',
        'pt_BR': 'manager-lang-pt_BR.js',
    };

    var queues = {};

    function loadJs(url, callback) {
        var done = false;
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.language = 'javascript';
        script.src = url;
        script.onload = script.onreadystatechange = function () {
            if (!done && (!script.readyState || script.readyState == 'loaded' || script.readyState == 'complete')) {
                done = true;
                script.onload = script.onreadystatechange = null;
                if (callback) {
                    callback.call(script);
                }
            }
        }
        document.getElementsByTagName("head")[0].appendChild(script);
        /*
         $.ajax({
         async: false,
         url: url,
         dataType: "script",
         success: function (data, textStatus, jqxhr) {
         if (callback) {
         callback.call(script);
         }
         }
         });
         */
    }

    function runJs(url, callback) {
        loadJs(url, function () {
            document.getElementsByTagName("head")[0].removeChild(this);
            if (callback) {
                callback();
            }
        });
    }

    function loadCss(url, callback) {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.type = 'text/css';
        link.media = 'screen';
        link.href = url;
        document.getElementsByTagName('head')[0].appendChild(link);
        if (callback) {
            callback.call(link);
        }
    }

    function loadSingle(name, callback) {
        queues[name] = 'loading';

        var module = modules[name];
        var jsStatus = 'loading';
        var cssStatus = (manager.loader.css && module['css']) ? 'loading' : 'loaded';

        if (manager.loader.css && module['css']) {
            if (/^http/i.test(module['css'])) {
                var url = module['css'];
            } else {
                var url = manager.loader.base + 'themes/' + manager.loader.theme + '/' + module['css'];
            }
            loadCss(url, function () {
                cssStatus = 'loaded';
                if (jsStatus == 'loaded' && cssStatus == 'loaded') {
                    finish();
                }
            });
        }

        if (/^http/i.test(module['js'])) {
            var url = module['js'];
        } else {
            var url = manager.loader.base + 'plugins/' + module['js'];
        }
        loadJs(url, function () {
            jsStatus = 'loaded';
            if (jsStatus == 'loaded' && cssStatus == 'loaded') {
                finish();
            }
        });

        function finish() {
            queues[name] = 'loaded';
            manager.loader.onProgress(name);
            if (callback) {
                callback();
            }
        }
    }

    function loadModule(name, callback) {
        var mm = [];
        var doLoad = false;

        if (typeof name == 'string') {
            add(name);
        } else {
            for (var i = 0; i < name.length; i++) {
                add(name[i]);
            }
        }

        function add(name) {
            if (!modules[name])
                return;
            var d = modules[name]['dependencies'];
            if (d) {
                for (var i = 0; i < d.length; i++) {
                    add(d[i]);
                }
            }
            mm.push(name);
        }

        function finishModule(name) {
            if (doLoad) {
                manager.loader.onLoad(name);
            }
        }

        function finish() {
            if (callback) {
                callback();
            }
            if (doLoad) {
                manager.loader.onAfterLoad(name);
            }
        }

        var time = 0;

        function loadMm() {
            if (mm.length) {
                var m = mm[0]; // the first module
                if (!queues[m]) {
                    doLoad = true;
                    loadSingle(m, function () {
                        finishModule(m);
                        mm.shift();
                        loadMm();
                    });
                } else if (queues[m] == 'loaded') {
                    mm.shift();
                    loadMm();
                } else {
                    if (time < manager.loader.timeout) {
                        time += 10;
                        setTimeout(arguments.callee, 10);
                    }
                }
            } else {
                if (manager.loader.locale && doLoad == true && locales[manager.loader.locale]) {
                    var url = manager.loader.base + 'locale/' + locales[manager.loader.locale];
                    runJs(url, function () {
                        finish();
                    });
                } else {
                    finish();
                }
            }
        }

        loadMm();
    }

    manager.loader = {
        modules: modules,
        base: '.',
        theme: 'default',
        css: true,
        locale: null,
        timeout: 2000,
        load: function (name, callback) {
            if (/\.css$/i.test(name)) {
                if (/^http/i.test(name)) {
                    loadCss(name, callback);
                } else {
                    loadCss(manager.loader.base + name, callback);
                }
            } else if (/\.js$/i.test(name)) {
                if (/^http/i.test(name)) {
                    loadJs(name, callback);
                } else {
                    loadJs(manager.loader.base + name, callback);
                }
            } else {
                loadModule(name, callback);
            }
        },
        loadCss: function (name, callback) {
            loadCss(name, callback);
        },
        loadJs: function (name, callback) {
            loadJs(name, callback);
        },
        onProgress: function (name) {
        },
        onLoad: function (name) {
            if (jQuery.fn[name].onPlugginLoad) {
                jQuery.fn[name].onPlugginLoad();
            }
        },
        onAfterLoad: function (name) {
        },
        getBase: function (scriptName) {
            var scripts = document.getElementsByTagName('script');
            var script = scriptName.replace(/\./g, "\\.");
            var regex = new RegExp('/' + script + '(\W|$)', "i");
            var base = '';
            for (var i = 0; i < scripts.length; i++) {
                var src = scripts[i].src;
                if (!src) {
                    continue;
                }
                var m = src.match(regex);
                if (m) {
                    base = src.substring(0, m.index + 1);
                }
            }
            return base;
        }
    };

    manager.loader.base = manager.loader.getBase('jquery.manager.core.js');

    window.usingm = manager.loader.load;

    if (window.jQuery) {
        jQuery(function () {
            manager.loader.load('jQuery.addObject.js', function () {
                manager.loader.load('jquery.manager.extensions.js', function () {
                    manager.loader.load('jquery.manager.utils.js', function() {
                        manager.loader.load('jquery.fileDownload.js', function() {
                            manager.loader.load('maction', function () {
                                manager.loader.load('jquery.manager.parser.js', function () {
                                    manager.parser.parse();
                                    console.log(manager.action);
                                    manager.ready();
                                });
                            });
                        });
                    });
                });
            });
        });
    }

})();
