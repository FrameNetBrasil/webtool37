<?php

class MLookupBFF extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/typeinstance/lookupBFF');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idBFF',
            textField:'description',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idBFF', hidden:true},
                {field:'description', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

