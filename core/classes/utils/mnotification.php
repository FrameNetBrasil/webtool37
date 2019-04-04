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
 * Created by PhpStorm.
 * User: diego
 * Date: 01/02/16
 * Time: 15:31
 */
class MNotification
{
    private $infos = [];
    private $warnings = [];
    private $errors = [];

    public function addError($message)
    {
        $this->errors[] = $message;
    }

    public function addInfo($message)
    {
        $this->infos[] = $message;
    }

    public function addWarning($message)
    {
        $this->warnings[] = $message;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    public function hasInfos()
    {
        return count($this->infos) > 0;
    }

    public function hasWarnings()
    {
        return count($this->warnings) > 0;
    }

    public function getErrors($glue = ', ', $prefix = '')
    {
        return $this->implodeNotifications($this->errors, $glue, $prefix);
    }

    public function getInfos($glue = ', ', $prefix = '')
    {
        return $this->implodeNotifications($this->infos, $glue, $prefix);
    }

    public function getWarnings($glue = ', ', $prefix = '')
    {
        return $this->implodeNotifications($this->warnings, $glue, $prefix);
    }

    /**
     * @param $glue
     * @return string
     */
    private function implodeNotifications($notifications, $glue = ', ', $prefix = '')
    {
        if (!empty($prefix)) {
            array_walk($notifications, array($this, 'applyPrefix'), $prefix);
        }

        return implode($glue, $notifications);
    }

    private function applyPrefix(&$item, $key, $prefix)
    {
        $item = "{$prefix} {$item}";
    }
}