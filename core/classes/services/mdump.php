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
class MDump extends MService
{

    /**
     * Attribute Description.
     */
    private $dump;

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
        parent::__construct();
        $this->log = $this->manager->log;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public function get()
    {
        return $this->dump;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public function usesDump()
    {
        $uses = $this->manager->uses;

        if ($uses) {
            $total = 0;

            $html = "<p><b>Uses Information:</b>\n" . "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

            foreach ($uses as $u) {
                list($name, $size) = $u;

                $total += $size;

                $html .= "<tr><td>&nbsp;&nbsp;$name:</td><td align=\"right\">&nbsp;$size&nbsp;bytes</td></tr>\n";
            }

            $html .= "<tr><td align=\"right\">Total:</td><td align=\"right\">&nbsp;$total&nbsp;bytes</td></tr>\n"
                . "</table>\n";
        }

        return $html;
    }

    /**
     *
     */

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $var (tipo) desc
     * @param $file (tipo) desc
     * @param $line =false (tipo) desc
     * @param $info =false (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function dump($var, $file = false, $line = false, $info = false)
    {
        global $REMOTE_ADDR;

        $dump = false;

        if (is_array($this->dump)) {
            $dump = in_array($REMOTE_ADDR, $this->dump['peer']);
        } else {
            $dump = ($this->dump['peer'] == $REMOTE_ADDR);
        }

        if ($dump) {
            echo "<pre>\n";

            if ($info)
                echo $info . "\n" . str_repeat('-', strlen($info)) . "\n";

            var_dump($var);

            echo "</pre>\n";
        }

        ob_start();
        var_dump($var);
        $ob = ob_get_contents();
        ob_end_clean();

        // show file name and line from where the dump is generated
        if ($file) {
            $this->log->logMessage("[VARDUMP]file:$file:$line:$info");
        } else {
            $this->log->logMessage(
                '[DEPRECATED] Deprecated usage of $Manager->dump(): Filename and line number are missing -- use $Manager->dump($var,__FILE__,__LINE__) instead.');
        }

        foreach (explode("\n", $ob) as $line) {
            $this->log->logMessage('[VARDUMP]' . $line);
        }
    }

}
