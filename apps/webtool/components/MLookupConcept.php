<?php

class MLookupConcept extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/concept/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idConcept',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idConcept', hidden:true},
                {field:'name', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

