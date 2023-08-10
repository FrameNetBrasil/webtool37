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
  * Maestro 2.0 - Manager Element Plugin
  */

(function ($) {
    manager.element = function (elements, options) {
        console.log(elements);
        this.jQuery = $(elements);
        this.elements = elements;
        this.options = jQuery.extend({}, this.defaults, options);
        this._attach();
        this.load();
    };
    manager.element.prototype = {
        version: 'Manager 2.0',
        onPlugginLoad: function () {
        },
        returnThis: function () {
            return this;
        },
        defaults: {
            href: null
        },
        _attach: function () {
            var self = this;
            this.jQuery.each(function (index, element) {
                element.options = $.extend({}, self.defaults, self.parseOptions(element), element.options)
            });
        },
        parseOptions: function (target) {
            return $.extend({}, manager.parser.parseOptions(target,
			    ['href']
		    ), {});
        },
        load: function () {
            return this.jQuery.each(function (index, element) {
                var opts = element.options;
                var t = $(element).empty();
                if (opts.href) {
                    manager.doGet(opts.href, t.attr('id'));
                }
            });
        }
    };

    // add it to the jQuery API
    $.addObject('melement', manager.element);
    //console.log($.fn.managerElement);

})(jQuery);
