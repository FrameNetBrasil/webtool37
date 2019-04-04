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

/*
 * Painter usando jQuery EasyUI
 */

class MEasyUIPainter extends MBasePainter
{

    public function __construct()
    {
        parent::__construct();
        // Define quais propriedades serão renderizadas como atributos HTML (exceto style)
        $this->attributes = "id,name,readonly,title,value,class,src,alt,enctype,method";
    }

    /**
     * Verifica métodos não existentes.
     * @param type $name Nome do método.
     * @param type $args Argumentos da chamada.
     * @throws \Maestro\Services\Exception\EControlException
     */
    public function __call($name, $args)
    {
        if (!isset($this->$name)) {
            mdump("====> Método {$name} não definido no Painter!");
            throw new EControlException("Método {$name} não definido no Painter!");
        }
    }

    /*
     * Métodos auxiliares para tratamento de controles EasyUI 
     */

    /**
     * Adiciona a classe CSS referente ao plugin.
     * @param object $control
     */
    public function setPluginClass($control)
    {
//        $control->addClass('easyui-' . $control->plugin);
    }

    /**
     * Cria o atributo data-options com base em $control->property->options.
     * Executado a partir de $basePainter->getAttributes().
     * @param object $control
     * @return string
     */
    public function getOptions($control)
    {
//        $value = substr(MJSON::parse($control->options), 1, -1);
//        return ($value != '') ? "data-options=\"{$value}\" " : '';
        if ($control->plugin != '') {
            //$control->addClass('easyui-' . $control->plugin);
            $options = $control->options ? MJSON::parse($control->options) : '{}';
            $code = "$('#{$control->property->id}').{$control->plugin}({$options});";
            $this->page->onLoad($code);
        } else {
            $value = substr(MJSON::parse($control->options), 1, -1);
            return ($value != '') ? "data-options=\"{$value}\" " : '';
        }
    }

    /**
     * Cria o comando javascript para definições de opções do plugin.
     * Usado essencialmente para as opções que são funções javascript (ex. eventos).
     * @param object $control
     */
    /*
    public function createJS($control)
    {
        $jsOptions = $control->property->jsOptions ? MJSON::parse($control->property->jsOptions) : '{}';
        if ($jsOptions != '') {
            $code = "$('#{$control->id}').{$control->plugin}({$jsOptions});";
            $this->page->onLoad($code);
        }
    }
*/
    /**
     * Gera o codigo javascript referente aos eventos de um controle.
     * Adapta o nome dos eventos ao padrão do EasyUI ('click' => 'onClick').
     * @param array[object] $events
     */
    public function generateEvents($control)
    {
        $events = $control->event;
        if (is_array($events) && count($events)) {
            foreach ($events as $event) {
                foreach ($event as $objEvent) {
                    $preventDefault = $objEvent->preventDefault ? "event.preventDefault();" : "";
                    $function = $objEvent->handler[0] == '!' ? substr($objEvent->handler, 1) : "function(event) { {$objEvent->handler} {$preventDefault} }";
                    if ($control->plugin != '') {
                        $objEvent->event = 'on' . ucfirst($objEvent->event);
                        $code = "$('#{$objEvent->id}').{$control->plugin}({ {$objEvent->event}: {$function} } )";
                    } else {
                        $code = "$('#{$objEvent->id}').on('{$objEvent->event}', {$function} )";
                    }
                    $this->page->onLoad($code);
                }
            }
        }
    }

    /*
     * Output
     */

    public function mspan($control)
    {
        $attributes = $this->getAttributes($control);
        $inner = $control->text ? : $control->cdata;
        return <<<EOT
<span {$attributes}>
    {$inner}
</span>
EOT;
    }

    public function mlabel($control)
    {
        return $this->mspan($control);
    }

    public function mfieldlabel($control)
    {
        $label = "";
        if ($control->text) {
            $classes = $control->getClassStr();
            $label = "<label for=\"{$control->id}\" class=\"mFormLabel {$classes}\">{$control->text}</label>";
        }
        return $label;
    }

    public function mimage($control)
    {
        $control->property->src = $control->property->src ?: $control->property->source;
        $control->property->alt = $control->property->alt ?: $control->property->label;
        $attributes = $this->getAttributes($control);
        return <<<EOT
<img {$attributes}>
EOT;
    }

    /*
     * Input
     */

    public function minputfield($control)
    {
        $this->setPluginClass($control);
        if ($control->property->placeholder) {
            $control->options->prompt = $control->property->placeholder;
        }
        $control->options->width = $control->style->width ? : '150px';
        if ($control->property->mask) {
            $maskOptions = $control->property->maskOptions != '' ? ',' . $control->property->maskOptions : '';
            $onLoad = "$('#{$control->property->id}').textbox('textbox').mask('{$control->property->mask}'{$maskOptions});";
            $this->page->onLoad($onLoad);
        }
        // processa os validators e retorna o campo hidden, se necessário
        $hidden = MValidator::process($control);
        $prefix = $control->property->prefix;
        $sufix = $control->property->sufix;
        $attributes = $this->getAttributes($control);
        return <<<EOT
        {$prefix}<input {$attributes}/>{$sufix}{$hidden} 
EOT;
    }

    public function mtextfield($control)
    {
        $control->plugin = 'textbox';
        if ($control->property->type == "search") {
            $icons = "[{ iconCls: 'icon-search', handler: function(e) { " . $control->property->action . "}}]";
            $control->options->icons = (object) $icons;
        }
        $control->property->type = ($control->property->type != 'file') ? $control->property->type : '';
        return $this->minputfield($control);
    }

    public function mhiddenfield($control)
    {
        $attributes = $this->getAttributes($control);
        return <<<EOT
<input type="hidden" {$attributes}/>
EOT;
    }

    public function mpasswordfield($control)
    {
        $control->options->type = "password";
        return $this->mtextfield($control);
    }

    public function mmultilinefield($control)
    {
        $control->property->text = $control->property->value?: $control->property->text;
        $control->options->multiline = true;
        //$attributes = $this->getAttributes($control);
        //return "<textarea {$attributes}>{$control->property->text}</textarea>";
        return $this->mtextfield($control);
    }

    public function mcalendarfield($control)
    {
        $control->plugin = 'datebox';
        $control->property->mask = '00/00/0000';
        $control->property->placeholder = '__/__/____';
        return $this->minputfield($control);
    }

    public function mtimefield($control)
    {
        $control->plugin = 'timespinner';
        return $this->minputfield($control);
    }

    public function mtimestampfield($control)
    {
        $control->plugin = 'datetimespinner';
        $control->property->value = $control->value;
        return $this->minputfield($control);
    }

    public function memailfield($control)
    {
        $control->plugin = 'validatebox';
        if ($control->form) {
            $control->addValidator((object) ['field' => $control->property->id, 'type' => 'email']);
        }
        return $this->mtextfield($control);
    }

    public function mnumberfield($control)
    {
        $control->plugin = 'numberbox';
        MUtil::setIfNull($control->options->decimalSeparator, $control->property->decimalSeparator ? : ',');
        return $this->minputfield($control);
    }

    public function mcurrencyfield($control)
    {
        $control->options->precision = '2';
        $control->options->prefix = 'R$ ';
        $control->options->groupSeparator = '.';
        $control->options->decimalSeparator = ',';
        return $this->mnumberfield($control);
    }

    public function mnumberspinner($control)
    {
        $control->plugin = 'numberspinner';
        return $this->minputfield($control);
    }

    public function mphonefield($control)
    {
        $control->plugin = 'numberbox';
        $control->property->mask = '(00) 0000-0000';
        $control->property->placeholder = '(00) 0000-0000';
        return $this->mtextfield($control);
    }

    public function mcpffield($control)
    {
        $control->plugin = 'numberbox';
        $control->property->mask = '000.000.000-00';
        $control->property->placeholder = '000.000.000-00';
        $control->property->maskOptions = "{reverse: true}";
        return $this->mtextfield($control);
    }

    public function mcnpjfield($control)
    {
        $control->plugin = 'numberbox';
        $control->property->mask = '00.000.000/0000-00';
        $control->property->placeholder = '00.000.000/0000-00';
        $control->property->maskOptions = "{reverse: true}";
        return $this->mtextfield($control);
    }

    public function mnitfield($control)
    {
        $control->plugin = 'numberbox';
        $control->property->mask = '000.00000.00-0';
        $control->property->placeholder = '000.00000.00-0';
        $control->property->maskOptions = "{reverse: true}";
        return $this->mtextfield($control);
    }

    public function msiapefield($control)
    {
        $control->plugin = 'numberbox';
        $control->property->mask = '0000000';
        $control->property->placeholder = '0000000';
        $control->property->maskOptions = "{reverse: true}";
        return $this->mtextfield($control);
    }

    public function mcepfield($control)
    {
        $control->plugin = 'numberbox';
        $control->property->mask = '00000-000';
        $control->property->placeholder = '00000-000';
        $control->property->maskOptions = "{reverse: true}";
        return $this->mtextfield($control);
    }
    
    public function msearchfield($control)
    {
        $control->plugin = 'searchbox';
        $control->options->searcher =(object)"function (value,name) { {$control->property->searcher}; }";
        return $this->minputfield($control);
    }

    public function mbooleanfield($control)
    {
        $checked = ($control->property->value != '1') ? 'checked' : '';
        $control->plugin = 'switchbutton';
        $control->options->onText = "Yes";
        $control->options->offText = "No";
        $control->options->checked = ($control->property->value == 1);
        $control->options->value = '1';
        $attributes = $this->getAttributes($control);
        /*
        $onLoad = <<<EOT
$('#{$control->property->id}').bootstrapSwitch();
EOT;
        $this->page->onLoad($onLoad);

        return <<<EOT
    <input type="checkbox" {$attributes} {$checked} data-size="mini" data-on-text="Yes" data-off-text="No">
EOT;
        */
        return <<<EOT
    <input {$attributes}>
EOT;
    }
    
            
    public function meditor($control)
    {
        $control->property->jId = '#' . $control->property->id;
        $html = $this->fetch('meditor', $control);
        $this->page->onSubmit("{$control->property->id}_submit()", $control->form->property->id);
        return $html;
    }

    public function minputgrid($control)
    {
        $grid = array();
        for ($i = 1; $i <= $control->property->numRows; $i++) {
            $grid[$i] = new MHContainer();
            $grid[$i]->addControl(new MDiv(['inner' => $i . ': ', 'width' => '30px']));
            for ($j = 1; $j <= $control->property->numCols; $j++) {
                $textfield = new MTextField();
                $textfield->property->id = $control->property->id . '[' . $i . '][' . $j . ']';
                $textfield->style->width = '150px';
                $grid[$i]->addControl($textfield);
            }
        }
        return $this->generateToString($grid);
    }

    public function mtext($control)
    {
        return $this->mmultilinefield($control);
    }

    public function mselection($control)
    {
        $control->plugin = 'combobox';
        $data = [];
        foreach ($control->property->options as $value => $label) {
            $option = (object) ['label' => $label, 'value' => $value];
            $data[] = $option;
        }
        $control->options->valueField = 'value';
        $control->options->textField = 'label';
        $control->options->prompt = $control->property->prompt;
        $control->options->data = (object) MJSON::parse($data);
        $this->setPluginClass($control);
        $attributes = $this->getAttributes($control);
        return "<input {$attributes}>";
    }

    public function mlookup($control)
    {
        /*
        $idHidden = new MHiddenField();
        $idHidden->setId($control->id);

        $textHidden = new MHiddenField();
        $textHidden->setId($control->id . '_text');
        */

        //$id = $control->id;
        //$control->property->id = $id . '_lookup';
        //$control->property->name = $id . '_lookup';
        //$control->property->filters = $control->property->filter;
        $control->style->width = $control->style->width ? : '100px';
        //$related = "{$idHidden->id}:{$control->idField},{$textHidden->id}:{$control->textField},";
        //$control->property->related = $related . $control->related;
        //$control->property->filters = $control->filters;
        $control->property->filters .= "," . $control->id;

        $lookup['idField'] = strtoupper($control->idField);
        $lookup['textField'] = strtoupper($control->textField);
        $lookup['url'] = Manager::getAppURL($control->property->action);

        if ($control->hasItems()) {
            foreach ($control->controls as $child) {
                
                if ($child->tag == 'mlookupcolumn') {
                    $columns[] = array(
                        'field' => strtoupper($child->property->field),
                        'hidden' => ($child->property->visible === false),
                        'title' => $child->property->title,
                        'width' => $child->width
                    );
                } /* else {
                    if ($child instanceof mlookupoptions) {
                        $lookup['loadMsg'] = $child->loadMsg ? : "Carregando...";
                        $lookup['minLength'] = $child->minLength;
                        $lookup['panelWidth'] = $child->panelWidth;
                        $lookup['fitColumns'] = $child->fitColumns;
                    }
                }*/
            }
            $lookup['columns'][0] = $columns;
        }
        //$control->property->lookup = urlencode(json_encode($lookup));
        $this->page->addJsCode("$('#{$control->property->id}').data('lookup', '" . MJSON::encode($lookup) . "');\n");
        $this->page->addJsCode("$('#{$control->property->id}').data('related', '" . MJSON::encode($control->property->related) . "');\n");
        $this->page->addJsCode("$('#{$control->property->id}').data('filters', '" . MJSON::encode($control->property->filters) . "');\n");
        
        $this->page->onLoad("mlookup('{$control->property->id}');");
        $attributes = $this->getAttributes($control);
        /*
         {$this->mhiddenfield($idHidden)}
    {$this->mhiddenfield($textHidden)}

         */
        return <<<EOT
    <select {$attributes}></select>
EOT;
    }

    public function mfilefield($control)
    {
        $control->plugin = 'filebox';
        $control->options->prompt = $control->property->text;
        $this->setPluginClass($control);
        $control->form->property->enctype = "multipart/form-data";
        return $this->minputfield($control);
    }
    
    public function mcheckbox($control){
        $attributes = $this->getAttributes($control);
        $text = $control->property->text ?: $control->property->label;
        return "<input type='checkbox' {$attributes}>{$text}";
    }

    public function mradiobutton($control){
        $attributes = $this->getAttributes($control);
        $text = $control->property->text ?: $control->property->label;
        return "<input type='radio' {$attributes}>{$text}";
    }
    /*
     * Actions
     */

    public function mprompt($control)
    {
        $promptType = [
            'information' => 'info',
            'error' => 'error',
            'question' => 'question',
            'confirmation' => 'confirm',
            'warning' => 'warning'
        ];
        $dataJson = MJSON::encode((object)[
            'type' => $promptType[$control->property->type],
            'title' => ucFirst(_M($control->property->type)),
            'msg' => $control->property->msg,
        ]);
        $dataJson = addslashes($dataJson);
        //$control->property->id = 'mprompt';
        $action1 = MAction::parseAction(addslashes($control->property->action1));
        $action2 = MAction::parseAction(addslashes($control->property->action2));
        //$this->page->onLoad("console.log(manager.action);");
        $this->page->onLoad("var {$control->property->id} = theme.prompt('{$control->property->id}','{$dataJson}',\"{$action1}\",\"{$action2}\");");
        $show = ($control->property->show === false) ? false : true;
        if ($show) {
            $this->page->onLoad("{$control->property->id}.show();");
        }
    }

    public function mtree($control)
    {
        if (count($control->property->arrayItems)) {
            $key = '3';
            $data = '0,1,2,3,4,5';
            $tree = MUtil::arrayTree($control->property->arrayItems, $key, $data);
            $control->property->items = $tree;
        }
        $id = $control->property->id;
        $tree = $control->property->items;
        if ($control->property->checkbox != NULL) {
            $control->options->checkbox = $control->property->checkbox;
        }
        $control->plugin = 'tree';
        $internal = $this->mtreeTransverse($tree, $tree['root']);
        $data = "[{$internal}]";
        $control->options->data = (object) $data;
        $event = str_replace('#action#', 'node.action', $event);
        $onSelect = $control->property->onSelect;
        if ($onSelect != '') {
            $onSelect = <<<EOT
    function(node) { 
        {$onSelect}
    }
EOT;
        } else {
            $onSelect = <<<EOT
    function(node) { 
        console.log(node);
        if (node.action != '') {
            console.log(node.action);
            manager.doAction(node.action);
        }
    }
EOT;
        }
        $control->options->onSelect = (object) $onSelect;
        $this->setPluginClass($control);
        //$this->createJS($control);
        $attributes = $this->getAttributes($control);
        $html = "<ul {$attributes}></ul>";
        return $html;
    }

    private function mtreeTransverse($tree, $nodes)
    {
        $text = "";
        if (count($nodes)) {
            foreach ($nodes as $node) {
                $children = $this->mtreeTransverse($tree, $tree[$node[0]]);
                $action = MAction::parseAction($node[2]);
                $state = ($node[4] != '') ? ", state: '{$node[4]}'" : "";
                $check = $node[5] !== null ? ", check: " . ($node[5] ? 'true' : 'false') : "";
                $text .= "{id: '{$node[0]}', text: '{$node[1]}', action: '{$action}', children: [{$children}]{$state}{$check}},";
            }
            $text = substr($text, 0, -1);
        }
        return $text;
    }

    public function mlink($control)
    {
        MAction::generate($control, $control->property->id);
        $control->property->href = $control->property->href ? : "#";
        $attributes = $this->getAttributes($control);
        $inner = $control->property->text ? : $control->property->cdata;
        return <<<EOT
<a {$attributes}>
    {$inner}
</a>
EOT;
    }

    private function glyphclass($control)
    {
        return $control->property->glyph ? "glyphicon glyphicon-{$control->property->glyph}" : "";
    }

    private function glyphicon($control)
    {
        return $control->property->glyph ? "<div style='padding:3px' class='{$this->glyphclass($control)}' aria-hidden='true'></div>" : "";
    }

    public function mlinkbutton($control)
    {
        $control->options->iconCls = $control->property->iconCls ? : $control->property->icon;
        $control->options->plain = $control->property->plain;
        $control->options->size = $control->property->size;
        $glyph = $this->glyphicon($control);
        MAction::generate($control, $control->property->id);
        $control->plugin = 'linkbutton';
        $this->setPluginClass($control);
        $attributes = $this->getAttributes($control);
        return <<<EOT
<a href='#' {$attributes}>
    {$glyph}{$control->property->text}
</a>
EOT;
    }

    public function mbutton($control)
    {
        $control->plugin = 'linkbutton';
        $control->addClass('mFormButton');
        $glyph = $this->glyphicon($control);
        $control->property->iconCls = $control->property->iconCls ? : $control->property->icon;
        $control->property->type = $control->property->type ? : "button";
        MUtil::setIfNull($control->property->action, 'POST');
        MAction::generate($control);
        $this->setPluginClass($control);
        $attributes = $this->getAttributes($control);
        $text = $glyph . (($control->property->value != '') ? "{$control->property->value}" : (($control->property->text != '') ? "{$control->property->text}" : "{$control->property->caption}"));
        return <<<EOT
<button {$type}{$attributes}>
    {$text}
</button>
EOT;
    }

    public function mcontextmenu($control)
    {
        $control->plugin = 'menu';
        $this->setPluginClass($control);
        // captura o evento 'contextmenu' para exibir o menu
        $code = <<<EOT
$('#{$control->property->context}').bind('contextmenu',function(e){
    e.preventDefault();
    $('#{$control->property->id}').menu('show', {
        left: e.pageX,
        top: e.pageY 
    }); 
}); 
EOT;
        $this->page->addJsCode($code);
        return $this->mdiv($control);
    }

    public function mmenu($control)
    {
        return $this->mdiv($control);
    }

    public function mmenubutton($control)
    {
        $control->plugin = 'menubutton';
        $control->addClass('mMenuButton');
        MAction::generate($control, $control->property->id);
        $this->setPluginClass($control);
        $attributes = $this->getAttributes($control);
        return <<<EOT
<a href='#' {$attributes}>
    {$control->property->text}
</a>
EOT;
    }

    public function mmenuitem($control)
    {
        $control->property->text = $control->property->text ? : $control->property->label;
        $control->options->iconCls = $control->property->icon ? : ($control->property->iconCls ? : $this->glyphclass($control));
        MAction::generate($control);
        $control->addControl($control->property->text);
        return $this->mdiv($control);
    }

    public function mmenubar($control)
    {
        return $this->mdiv($control);
    }

    public function mmenubaritem($control)
    {
        $control->property->text = $control->property->label;
        $control->options->iconCls = $control->property->icon ? : ($control->property->iconCls ? : $this->glyphclass($control));
        foreach ($control->controls as $c) {
            if ($c->className == 'mmenu') {
                $control->options->menu = '#' . $c->property->id;
                $menus .= $this->mmenu($c);
            }
        }
        return $this->mmenubutton($control) . $menus;
    }

    public function mmenuseparator($control)
    {
        $control->setClass('menu-sep');
        return $this->mdiv($control);
    }

    public function mtool($control)
    {
        MAction::generate($control, $control->property->id);
        $control->setClass($control->property->icon);
        $attributes = $this->getAttributes($control);
        return <<<EOT
<a {$attributes}></a>
EOT;
    }

    public function mtoolbutton($control)
    {
        return $this->mlinkbutton($control);
    }

    /*
     * Containers
     */

    public function mdiv($control)
    {
        $attributes = $this->getAttributes($control);
        if ($control->hasItems()) {
            $inner = $this->generateToString($control->controls);
        } elseif ($control->property->cdata) {
            $inner = $control->property->cdata;
        } else {
            $inner = $control->inner;
        }
        return <<<EOT
<div {$attributes}>
    {$inner}
</div>
EOT;
    }

    public function mbasegroup($control)
    {
        $control->property->title = $control->property->caption;
        return $this->mdiv($control);
    }

    public function maccordion($control)
    {
        $control->plugin = 'accordion';
        $this->setPluginClass($control);
        return $this->mdiv($control);
    }

    public function mtoolbar($control)
    {
        return $this->mdiv($control);
    }

    public function mtools($control)
    {
        return $this->mdiv($control);
    }

    public function mform($control)
    {
        return $control->generate();
    }

    public function mformdialog($control)
    {
        return $control->generate();
    }

    public function mcontainer($control)
    {
        return $this->mdiv($control);
    }

    public function mhcontainer($control)
    {
        $control->addClass('mHContainer');
        if ($control->hasItems()) {
            foreach ($control->controls as $field) {
                $label = $field->property->label ? : '';
                $inner .= '<div class="cell">';
                $inner .= $label . $field->generate();
                $inner .= "</div>";
            }
            $inner .= '<div class="clear"></div>';
        }
        
        $attributes = $this->getAttributes($control);
        return <<<EOT
<div {$attributes}>
    {$inner}
</div>
EOT;
    }

    public function mvcontainer($control)
    {
        $control->addClass('mVContainer');
        $inner = "";
        if ($control->hasItems()) {
            foreach ($control->controls as $field) {
                $label = $field->property->label ? : '';
                $inner .= '<div class="cell">';
                if ($control->labelHorizontal) {
                    $inner .= $label . $field->generate();
                } else {
                    $inner .= ($label ? '<div class="cell">' . $label . '</div>' : '') . $field->generate();
                }
                $inner .= "</div>";
            }
        }
        $attributes = $this->getAttributes($control);
        return <<<EOT
<div {$attributes}>
    {$inner}
</div>
EOT;
    }

    public function mcontentpane($control)
    {
        return $this->mdiv($control);
    }

    public function mdatagrid($control)
    {
        return $control->generate();
    }

    public function mdialog($control)
    {
        $control->setId($control->id);
        $control->plugin = 'dialog';
        //$control->options->closed = isset($control->property->closed) ? $control->property->closed : true;
        $control->options->modal = ($control->property->modal === false) ? false : true;
        $control->options->doSize = true;
        if ($control->property->file) {
            mdump('---' . $control->property->file);
            $dialog = $control->getControlsFromXML($control->property->file);
            $control->addControls($dialog);
        }
        $onClose = "function() {" . $control->property->onClose . " $('#{$control->property->id}').dialog('destroy', true);}";
        $control->options->onClose = (object) $onClose;
        $state = ($control->property->state == "open") ? 'false' : 'true';
        $onLoad = <<<EOT
$('#{$control->property->id}').dialog({closed: {$state}});
$('#{$control->property->id}').dialog('resize',{width:'auto',height:'auto'});
EOT;
        $this->page->onLoad($onLoad);
        $tools = $control->generateTools();
        $div = $this->mdiv($control);
        return $div . $tools;
    }

    public function mhelp($control)
    {
        return $this->mdialog($control);
    }

    public function mpanel($control)
    {
        $control->options->title = $control->property->title;
        if ($control->property->close != '') {
            $control->options->closable = true;
            // captura o evento 'onClose' para executar action definida em $control->close
            $action = addslashes(MAction::parseAction($control->close));
            $onClose = "function(e) {manager.doAction(\"{$action}\");}";
            $control->options->onClose = (object) $onClose;
        }
        $inner = "";
        if ($control->property->menubar) {
            $inner .= $control->property->menubar->generate();
        }
        if ($control->hasItems()) {
            foreach ($control->controls as $child) {
                $inner .= $child->generate();
            }
        }
        $tools = '';
        if ($control->property->tools) {
            $control->property->tools->id = $control->property->id > '_tools';
            $control->options->tools = "#{$control->tools->id}";
            $tools = $this->mdiv($control->property->tools);
        }
        MUtil::setIfNull($control->style->width, "100%");
        $control->plugin = 'panel';
        $this->setPluginClass($control);
        //$this->createJS($control);
        $attributes = $this->getAttributes($control);
        return <<<EOT
<div {$attributes}>
    {$inner}
</div>
{$tools}
EOT;
    }

    public function mactionpanel($control)
    {
        $actions = Manager::getActions();
        if ($control->property->actions) {
            $selection = explode('.', $control->property->actions);
            do {
                $actions = $actions[current($selection)][5];
            } while (next($selection) !== false);
            foreach ($actions as $action) {
                $link = new MLinkButton();
                $link->property->iconCls = $action[2];
                $link->property->plain = true;
                $link->property->size = 'large';
                $link->property->text = $action[0];
                $link->property->options['iconAlign'] = 'top';
                $link->property->action = MAction::isAction($action[1]) ? $action[1] : '>' . $action[1];
                MAction::generate($link);
                $control->addControl($link);
            }
        }
        return $this->mpanel($control);
    }

}
