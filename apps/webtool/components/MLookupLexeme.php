<?php

class MLookupLexeme extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/lexeme/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idLexeme',
            textField:'fullname',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idLexeme', hidden:true},
                {field:'fullname', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

