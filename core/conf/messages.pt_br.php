<?php
/* Copyright [2011, 2013, 2017] da Universidade Federal de Juiz de Fora
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

return [
    'Test' => 'Teste',
    "App: [%s], Module: [%s], Controller: [%s] : Not found!" => "App: [%s], Modulo: [%s], Controller: [%s] : Não encontrado!",
    "Login required!" => "É necessário fazer login para acessar a aplicação.",
    'currencyExtension' => [
	    'classSingular' => ["centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"],
	    'classPlural' => ["centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões"],
	    'orderHundred' => ["", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"],
	    'orderDozen' => ["", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa"],
	    'firstDozen' => ["dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove"],
	    'orderUnit' => ["", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove"],
	    'other' => ["zero", "cento", "e", "de"],
	]
];

