<?php

class MLookupLemma extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/lemma/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idLemma',
            textField:'fullname',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idLemma', hidden:true},
                {field:'fullname', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

