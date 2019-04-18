/**
 * theme.js
 * 
 * Script para inicializações 
 */

// Obtem a URL base do tema
var baseURL = manager.loader.getBase('theme.js');
console.log(manager.version + ' - Theme in: ' + baseURL);

// Define o elemento default para o conteudo obtido via Ajax-GET 
manager.contentElement = 'centerPane';

// Define o nome da aplicação, caso não esteja na URL
manager.defaultApp = 'webtool';

jQuery(function ($) {
    // Remove a mensagem de loading
    $("#loader").hide();
});
