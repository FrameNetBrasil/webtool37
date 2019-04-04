<?php

class MLookupExtraThematicFE extends MControl
{

    public function generate()
    {
        $idFrame = $this->data->idFrame;
        $url = Manager::getAppURL('', 'data/frameelement/lookupDataExtraThematic/' . $idFrame);
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'name',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
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
