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
 * MNotFound.
 * Retorna objeto JSON ou emite header(Location).
 * Objeto JSON = {'id':'$pageName', 'type' : 'redirect', 'data' : '$url'}
 */
class MRedirect extends MResult
{

    public $view;

    public function __construct($view, $content)
    {
        parent::__construct();
        $this->content = $content;
        $this->view = $view;
    }

    public function apply($request, $response)
    {
        $response->setStatus(MStatusCode::OK);
        if (Manager::isAjaxCall()) {
            $id = 'redirect' . uniqid();
            $this->ajax->setId($id);
            $this->ajax->setType('redirect');
            $this->ajax->setData($this->content);
            $this->ajax->setResponseType('JSON');
            $this->content = $this->ajax->returnData();
            $response->setOut($this->content);
        } else {
            $response->setHeader('Location', 'Location:' . $this->content);
        }
    }

}
