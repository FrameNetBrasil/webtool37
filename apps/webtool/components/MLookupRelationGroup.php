<?php

class MLookupRelationGroup extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'structure/relationgroup/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:180,
            url: '{$url}',
            idField:'idRelationGroup',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idDomain', hidden:true},
                {field:'name', title:'Name', width:162}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        return $this->getPainter()->mtextField($this);
    }

}
?>
