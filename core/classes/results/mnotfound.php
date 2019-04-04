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
 * Retorna template preenchido com dados sobre o erro.
 * Objeto JSON = {'id':'error', 'type' : 'page', 'data' : '$html'}
 */
class MNotFound extends MResult
{

    private $message;

    public function __construct($message = '')
    {
        parent::__construct();
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function apply($request, $response)
    {
        $response->setStatus(MStatusCode::NOT_FOUND);
        $format = $request->getFormat();
        try {
            $template = new MTemplate();
            $template->context('result', $this);
            $errorHtml = MTemplateLocator::fetch($template, 'errors', '404.html');//$template->fetch("errors/{$language}/404.html");
            if ($request->isAjax()) {
                $this->ajax->setId('error');
                $this->ajax->setType('page');
                $this->ajax->setData($errorHtml);
                $out = $this->ajax->returnData();
            } else {
                $out = $errorHtml;
            }
            $response->setOut($out);
        } catch (EMException $e) {

        }
    }
}

