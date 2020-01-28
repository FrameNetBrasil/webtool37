<?php

class MLookupDocumentMultiModal extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/document/lookupDataMultiModal');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:250,
            url: '{$url}',
            idField:'idDocument',
            textField:'name',
            prompt: 'minimum 2 letters or * for all',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idDocument', hidden:true},
                {field:'name', title:'Name', width:235}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

