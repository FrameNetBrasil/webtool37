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
 * MBowserWindow.
 * Retorno para abrir nova janela do browser, numa requisição AJAX.
 * MPage::window() contém a URL a ser carregada na nova janela.
 * Objeto JSON = {'id':'window...', 'type' : 'window', 'data' : '$windowURL'}
 */
class MBrowserWindow extends MResult
{

    public function __construct($url = '')
    {
        parent::__construct();
        if ($this->ajax->isEmpty()) {
            $id = 'window' . uniqid();
            $this->ajax->setId($id);
            $this->ajax->setType('window');
            $this->ajax->setData($url);
        }
        $this->content = $this->ajax->returnData();
    }

    public function apply($request, $response)
    {
        $this->nocache($response);
        $response->setOut($this->content);
    }


}
