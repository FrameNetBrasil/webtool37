<?php

class MLookupConceptType extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/typeinstance/lookupConceptType');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idConceptType',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idConceptType', hidden:true},
                {field:'name', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}
?>
