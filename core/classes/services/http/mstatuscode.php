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

class MStatusCode
{

    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const PARTIAL_INFO = 203;
    const NO_RESPONSE = 204;
    const MOVED = 301;
    const FOUND = 302;
    const METHOD = 303;
    const NOT_MODIFIED = 304;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIERED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const INTERNAL_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const OVERLOADED = 502;
    const GATEWAY_TIMEOUT = 503;

    public static function success($code)
    {
        return $code / 100 == 2;
    }

    public static function redirect($code)
    {
        return $code / 100 == 3;
    }

    public static function error($code)
    {
        return $code / 100 == 4 || $code / 100 == 5;
    }
}
