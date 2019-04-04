<?php

class MLookupLayerGroup extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/layergroup/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:180,
            url: '{$url}',
            idField:'idLayerGroup',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'name', title:'Name', width:162}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        return $this->getPainter()->mtextField($this);
    }

}
