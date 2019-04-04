<?php

class MColor extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/lu/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'IDLU',
            textField:'FULLNAME',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'IDLU', hidden:true},
                {field:'FULLNAME', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

