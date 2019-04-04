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
 * Controller destinado ao gerenciamento flow de formulários (wizard - assistente)
 *
 */
class mFlowController extends MController
{

    private $nextAction;
    private $previousAction;
    private $confirmAction;
    private $cancelAction;


    public function __construct()
    {
        parent::__construct();
    }

    protected function setNextAction($action)
    {
        $this->nextAction = $action;
    }

    public function getNextAction()
    {
        return $this->nextAction;
    }

    protected function setPreviousAction($action)
    {
        $this->previousAction = $action;
    }

    public function getPreviousAction()
    {
        return $this->previousAction;
    }

    protected function setConfirmAction($action)
    {
        $this->confirmAction = $action;
    }

    public function getConfirmAction()
    {
        return $this->confirmAction;
    }

    protected function setCancelAction($action)
    {
        $this->cancelAction = $action;
    }

    public function getCancelAction()
    {
        return $this->cancelAction;
    }
}
