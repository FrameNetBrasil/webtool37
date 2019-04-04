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
 * Maestro 2.0 - Manager Parser Plugin
 */

manager.parser = {
    version: 'Manager 2.0',
    auto: true,
    //onComplete: function (context) {
    //    console.log('pq!!!!');
    //},
    onComplete: function (context) {
        console.log('Executando external parser. context = ' + context);
        //manager.externalParse(context);
    },
    plugins: ['melement', 'maction', 'mgrid', 'mprompt', 'mnavbar'
    ],
    parse: function (context) {
        console.log('manager.parse.parse context = ' + context);
        var aa = [];
        for (var i = 0; i < manager.parser.plugins.length; i++) {
            var name = manager.parser.plugins[i];
            var r = $('.' + name, context);
            if (r.length) {
                if (r[name]) {
                    r[name]();
                } else {
                    aa.push({name: name, jq: r});
                }
            }
        }
        if (aa.length && window.manager.loader.base) {
            var names = [];
            for (var i = 0; i < aa.length; i++) {
                names.push(aa[i].name);
            }
            manager.loader.load(names, function () {
                for (var i = 0; i < aa.length; i++) {
                    var name = aa[i].name;
                    var jq = aa[i].jq;
                    jq[name]();
                }
                manager.parser.onComplete.call(manager.parser, context);
            });
        } else {
            manager.parser.onComplete.call(manager.parser, context);
        }
    },
    parseValue: function (property, value, parent, delta) {
        delta = delta || 0;
        var v = $.trim(String(value || ''));
        var endchar = v.substr(v.length - 1, 1);
        if (endchar == '%') {
            v = parseInt(v.substr(0, v.length - 1));
            if (property.toLowerCase().indexOf('width') >= 0) {
                v = Math.floor((parent.width() - delta) * v / 100.0);
            } else {
                v = Math.floor((parent.height() - delta) * v / 100.0);
            }
        } else {
            v = parseInt(v) || undefined;
        }
        return v;
    },
    /**
     * parse options, including standard 'data-manager' attribute.
     * 
     * calling examples:
     * manager.parser.parseOptions(target);
     * manager.parser.parseOptions(target, ['id','title','width',{fit:'boolean',border:'boolean'},{min:'number'}]);
     */
    parseOptions: function (target, properties) {
        var t = $(target);
        var options = {};
        var s = $.trim(t.attr('data-manager'));
        if (s) {
            if (s.substring(0, 1) != '{') {
                s = '{' + s + '}';
            }
            options = (new Function('return ' + s))();
        }
        return options;
    }
};
