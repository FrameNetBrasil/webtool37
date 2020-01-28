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

class MTemplateLocator
{
    public static function fetch(MTemplate $template, $folder, $file)
    {
        //$folder = Manager::getConf("options.varPath") . '/templates/' . $folder;
        $folder = Manager::getThemePath() . '/templates/' . $folder;
        $template->setPath($folder);
        return $template->fetch($file);
    }

    private static function buildPath($folder, $file)
    {
        $language = Manager::getOptions('language');
        $ds = DIRECTORY_SEPARATOR;
        return "$folder$ds$language$ds$file";
    }

    private static function appTemplateExists($path)
    {
        $file = self::getAppTemplatePath() . DIRECTORY_SEPARATOR . $path;
        return file_exists($file);
    }

    private static function getAppTemplatePath()
    {
        return Manager::getThemePath() . DIRECTORY_SEPARATOR . 'templates';
    }

}