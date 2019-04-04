<?php

class MLookupFE extends MControl
{

    public function generate()
    {
        $idFrame = $this->data->idFrame;
        $url = Manager::getAppURL('', 'data/frameelement/lookupData/' . $idFrame);
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idFrameElement',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idFrameElement', hidden:true},
                {field:'name', title:'Name', width:202}
            ]],
            onChange: function (newValue, oldValue) {
                if (newValue == '') {
                    $('#{$this->property->id}').combogrid('setValue', '');
                }
            }
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}
?>
