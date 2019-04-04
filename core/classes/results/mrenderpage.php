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
 * MRenderPage.
 * Retorna conteúdo HTML puro gerado a partir da renderização da página.
 */
class MRenderPage extends MResult
{

    public function __construct($content = '')
    {
        mtrace('Executing MRenderPage');
        parent::__construct();
        //if ($content != '') {
        //    $this->page->setContent($content);
        //}
        //$this->content = $this->page->render();
        $this->content = $content;
    }

    public function apply($request, $response)
    {
        $response->setOut($this->content);
        $response->setHeader('Content-type', 'Content-type: text/html; charset=UTF-8');
    }

}
