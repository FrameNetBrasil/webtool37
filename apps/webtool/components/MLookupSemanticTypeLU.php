<?php

class MLookupSemanticTypeLU extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/semantictype/lookupDataForLU');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:290,
            url: '{$url}',
            idField:'idSemanticType',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idSemanticType', hidden:true},
                {field:'name', title:'Name', width:272}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '300px';
        return $this->getPainter()->mtextField($this);
    }

}

