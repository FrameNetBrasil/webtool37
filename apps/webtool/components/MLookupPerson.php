<?php

class MLookupPerson extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'auth/person/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:180,
            url: '{$url}',
            idField:'idPerson',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idPerson', hidden:true},
                {field:'name', title:'Name', width:162}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        return $this->getPainter()->mtextField($this);
    }

}
?>
