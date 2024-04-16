<?php

class MLookupWordformLexeme extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/wordform/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idWordForm',
            textField:'fullname',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idWordForm', hidden:true},
                {field:'fullname', title:'Form', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

