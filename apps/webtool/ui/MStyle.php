<?php

/* Copyright [2011, 2012, 2013] da Universidade Federal de Juiz de Fora
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

class MStyle
{

    /**
     * CSS selector.
     */
    public $cssClass;

    /**
     * A list with style attributes.
     */
    public $style;

    /**
     * Is the control absolutely positioned?
     */
    public $cssp;

    public function __construct()
    {
        $this->cssClass = '';
        $this->cssp = false;
        $this->style = new MStringList();
    }

    public static function selector($name)
    {
        switch ($name) {
            case 'color':
            case 'font':
            case 'border':
            case 'overflow':
            case 'cursor':
            case 'padding':
            case 'margin':
            case 'width':
            case 'height':
            case 'float':
            case 'clear':
            case 'visibility':
            case 'display':
            case 'top':
            case 'left':
            case 'position':
                $selector = $name;
                break;
            case 'fontSize':
                $selector = 'font-size';
                break;
            case 'fontStyle':
                $selector = 'font-style';
                break;
            case 'fontFamily':
                $selector = 'font-family';
                break;
            case 'fontWeight':
                $selector = 'font-weight';
                break;
            case 'textAlign':
                $selector = 'text-align';
                break;
            case 'textIndent':
                $selector = 'text-indent';
                break;
            case 'lineHeight':
                $selector = 'line-height';
                break;
            case 'zIndex':
                $selector = 'z-index';
                break;
            case 'backgroundColor':
                $selector = 'background-color';
                break;
            case 'verticalAlign':
                $selector = 'vertical-align';
                break;
            case 'borderCollapse':
                $selector = 'border-collapse';
                break;
            case 'borderWidth':
                $selector = 'border-width';
                break;
            case 'borderSpacing':
                $selector = 'border-spacing';
                break;
            case 'borderTop':
                $selector = 'border-top';
                break;
            case 'borderRight':
                $selector = 'border-right';
                break;
            case 'borderBottom':
                $selector = 'border-bottom';
                break;
            case 'borderLeft':
                $selector = 'border-left';
                break;
            case 'emptyCells':
                $selector = 'empty-cells';
                break;
            default:
                $selector = '';
                break;
        }
        return $selector;
    }

    public function __set($name, $value)
    {
        if ($name == '')
            mtracestack();
        switch ($name) {
            case 'color':
            case 'font':
            case 'border':
            case 'overflow':
            case 'cursor':
                $this->addStyle($name, $value);

                break;

            case 'padding':
            case 'margin':
            case 'width':
            case 'height':
            case 'float':
            case 'clear':
            case 'visibility':
                $this->addStyle($name, $value);
                //$this->addStyle('display', 'block');

                break;

            case 'top':
            case 'left':
            case 'position':
                $this->addStyle($name, $value);
                $this->cssp = true;

                break;

            case 'fontSize':
                $this->addStyle('font-size', $value);

                break;

            case 'fontStyle':
                $this->addStyle('font-style', $value);

                break;

            case 'fontFamily':
                $this->addStyle('font-family', $value);

                break;

            case 'fontWeight':
                $this->addStyle('font-weight', $value);

                break;

            case 'textAlign':
                $this->addStyle('text-align', $value);

                break;

            case 'textIndent':
                $this->addStyle('text-indent', $value);

                break;

            case 'lineHeight':
                $this->addStyle('line-height', $value);

                break;

            case 'zIndex':
                $this->addStyle('z-index', $value);

                break;

            case 'backgroundColor':
                $this->addStyle('background-color', $value);

                break;

            case 'verticalAlign':
                $this->addStyle('vertical-align', $value);

                break;

            case 'borderCollapse':
                $this->addStyle('border-collapse', $value);

                break;

            case 'borderWidth':
                $this->addStyle('border-width', $value);

                break;

            case 'borderSpacing':
                $this->addStyle('border-spacing', $value);

                break;

            case 'borderTop':
                $this->addStyle('border-top', $value);

                break;
            case 'borderRight':
                $this->addStyle('border-right', $value);

                break;
            case 'borderBottom':
                $this->addStyle('border-bottom', $value);

                break;
            case 'borderLeft':
                $this->addStyle('border-left', $value);

                break;
            case 'emptyCells':
                $this->addStyle('empty-cells', $value);

                break;

            default:
                //mdump($name . ' - ' . $value);

                break;
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'top':
            case 'left':
            case 'width':
            case 'height':
            case 'padding':
            case 'float':
            case 'position':
                return $this->style->get($name);
                break;
        }
    }

    /**
     * The clone method.
     * It is used on clone of controls, avoiding references to same styles.
     */
    public function __clone()
    {
        $this->style = clone $this->style;
    }

    /**
     * The setter method.
     */
    public function set($name, $value)
    {
        if ($value != '') {
            $this->__set($name, $value);
        }
    }

    public function get($name)
    {
        return ( $name != '' ) ? $this->style->get($name) : '';
    }

    public function addStyle($name, $value)
    {
        if ($value != '') {
            $this->style->addValue($name, $value);
        }
    }

    public function setClass($cssClass, $add = true)
    {
        if ($add) {
            $this->cssClass .= MUtil::ifNull($this->cssClass, '', ' ') . $cssClass;
        } else {
            $this->cssClass = $cssClass;
        }
    }

    public function insertClass($cssClass)
    {
        $this->cssClass = $cssClass . MUtil::ifNull($this->cssClass, '', ' ' . $this->cssClass);
    }

    public function addStyleFile($styleFile)
    {
        $this->page->addStyle($styleFile);
    }

    public function getClass()
    {
        return $this->cssClass;
    }

    /* TODO: tokenizer */

    public function setStyle($style)
    {
        $this->style->items = $style;
    }

    public function getStyle()
    {
        return $this->style->hasItems() ? ' style="' . $this->style->getText(':', ';') . '"' : '';
    }

    public function setPosition($left, $top, $position = 'absolute')
    {
        $this->addStyle('position', $position);
        $this->addStyle('left', "{$left}px");
        $this->addStyle('top', "{$top}px");
    }

    public function setWidth($value)
    {
        if (!$value) {
            return;
        }
        $this->addStyle('width', $value);
    }

    public function setHeight($value)
    {
        if (!$value) {
            return;
        }
        $this->addStyle('height', $value);
    }

    public function setColor($value)
    {
        $this->addStyle('color', $value);
    }

    public function setVisibility($value)
    {
        $value = ($value ? 'visible' : 'hidden');
        $this->addStyle('visibility', $value);
    }

    public function setFont($value)
    {
        $this->addStyle('font', $value);
    }

}
