<?php

class MLookupCoreType extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/typeinstance/lookupCoreType');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idCoreType',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idCoreType', hidden:true},
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
