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
 * Brief Class Description.
 * Complete Class Description.
 */
class MTrace
{

    /**
     * Attribute Description.
     */
    private $trace;

    /**
     * Attribute Description.
     */
    private $log;

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public function __construct()
    {
        $this->log = Manager::getLog();
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $msg (tipo) desc
     * @param $file (tipo) desc
     * @param $line =0 (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function trace($msg, $file = '', $line = 0)
    {
        $message = $msg;
        if ($file != '')
            $message .= " [file: $file] [line: $line]";
        $this->trace[] = $message;
        $this->log->logMessage('[TRACE]' . $message);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public function traceDump($msg, $file = '', $line = 0, $tag = null)
    {
        $message = print_r($msg, true);
        if ($file != '')
            $message .= " [file: $file] [line: $line]";
        $this->trace[] = $message;

        if (!$tag) {
            $tag = Manager::getConf('logs')['tag'];
        }

        if (strlen($tag) > 0) {
            $this->log->logMessage('[' . strtoupper($tag) . ']' . $message);
        } else {
            $this->log->logMessage('[CUSTOM]' . $message);
        }
    }

    public function traceStack($file = '', $line = 0)
    {
        try {
            throw new Exception;
        } catch (Exception $e) {

            $strStack = $e->getTraceAsString();
        }

        $this->trace($strStack, $file, $line);
        return $strStack;
    }

}
