<?php

class MLookupColor extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/color/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idColor',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idColor', hidden:true},
                {field:'name', hidden:true},
                {field:'decorated', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

