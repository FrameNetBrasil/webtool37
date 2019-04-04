<?php

class MLookupConstructionElement extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/construction/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idConstruction',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idConstruction', hidden:true},
                {field:'name', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

