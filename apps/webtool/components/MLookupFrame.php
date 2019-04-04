<?php

class MLookupFrame extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/frame/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idFrame',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idFrame', hidden:true},
                {field:'name', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

