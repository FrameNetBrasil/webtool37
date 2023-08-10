<?php

class MLookupCxnCE extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/construction/lookupDataInhCE?idConstructionElement='. $this->data->idConstructionElement);
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idConstructionElement',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idConstructionElement', hidden:true},
                {field:'name', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

