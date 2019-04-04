<?php

class MLookupCorpus extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/corpus/lookupData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:180,
            url: '{$url}',
            idField:'idCorpus',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idCorpus', hidden:true},
                {field:'name', title:'Name', width:162}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        return $this->getPainter()->mtextField($this);
    }

}
?>
