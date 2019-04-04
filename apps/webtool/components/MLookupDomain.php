<?php

class MLookupDomain extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/domain/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:180,
            url: '{$url}',
            idField:'idDomain',
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
