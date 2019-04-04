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

/**
 * MResult.
 * Classe abstrata, base para as classes de geração da resposta à requisição.
 */
abstract class MResult
{

    protected $ajax;
    protected $content;

    public function __construct()
    {
        $this->ajax = Manager::getAjax();
        $this->content = null;
    }

    public abstract function apply($request, $response);

    protected function setContentTypeIfNotSet($response, $contentType)
    {
        $response->setContentTypeIfNotSet($contentType);
    }

    protected function nocache($response)
    {
        // headers apropriados para evitar caching
        $response->setHeader('Expires', 'Expires: Fri, 14 Mar 1980 20:53:00 GMT');
        $response->setHeader('Last-Modified', 'Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        $response->setHeader('Cache-Control', 'Cache-Control: no-cache, must-revalidate');
        $response->setHeader('Pragma', 'Pragma: no-cache');
        $response->setHeader('X-Powered-By', 'X-Powered-By: ' . Manager::version() . '/PHP ' . phpversion());
    }

}

