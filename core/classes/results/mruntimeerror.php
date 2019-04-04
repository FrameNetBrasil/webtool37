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
 * MRuntimeError.
 * Retorna template preenchido com dados sobre o erro.
 * Objeto JSON = {'id':'error', 'type' : 'page', 'data' : '$html'}
 */
class MRuntimeError extends MResult
{
    protected $exception;
    protected $request;
    protected $response;


    public function __construct($exception = NULL)
    {
        parent::__construct();
        $this->exception = $exception;
    }

    public function apply($request, $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->setResponseContentType();
        $this->setResponseStatusCode();
        $this->setResponseOutput();
    }

    private function setResponseContentType()
    {
        $format = $this->request->format;
        if ($this->request->isAjax() && ($format == "html")) {
            $format = "json";
        }
        $this->response->setContentType($this->response->getMimeType("xx." + $format));
    }

    private function setResponseStatusCode()
    {
        if ($this->exception instanceof ELoginException) {
            $this->response->setStatus(MStatusCode::UNAUTHORIZED);
        } else {
            $this->response->setStatus(MStatusCode::INTERNAL_ERROR);
        }
    }

    private function setResponseOutput()
    {
        try {
            $this->response->setOut($this->getOutput());
        } catch (EMException $e) {
        }
    }

    private function getOutput()
    {
        $errorHtml = $this->fetchTemplate();
        if ($this->request->isAjax()) {
            return $this->getAjaxOutput($errorHtml);
        } else {
            return $errorHtml;
        }
    }

    private function fetchTemplate()
    {
        $template = new MTemplate();
        $template->context('result', $this->exception);
        $template->context('redirect', $this->exception->getGoto());

        if ($this->exception instanceof ELoginException) {
            return MTemplateLocator::fetch($template, 'errors', 'auth.html');
        } else {
            return MTemplateLocator::fetch($template, 'errors', 'runtime.html');
        }
    }

    private function getAjaxOutput($html)
    {
        $this->ajax->setId('error');
        $this->ajax->setType('page');
        $this->ajax->setData($html);
        return $this->ajax->returnData();
    }
}
