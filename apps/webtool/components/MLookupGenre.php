<?php

class MLookupGenre extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/genre/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:180,
            url: '{$url}',
            idField:'idGenre',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idGenre', hidden:true},
                {field:'name', title:'Name', width:162}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        return $this->getPainter()->mtextField($this);
    }

}
