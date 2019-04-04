<?php

class MLookupLU extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/lu/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:250,
            url: '{$url}',
            idField:'idLU',
            textField:'fullname',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idLU', hidden:true},
                {field:'fullname', title:'Name', width:252}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}
?>
