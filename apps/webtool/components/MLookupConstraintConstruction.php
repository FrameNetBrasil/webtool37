<?php

class MLookupConstraintConstruction extends MControl
{

    public function generate()
    {
        $idConstraint = $this->data->idConstraint;
        $url = Manager::getAppURL('', 'data/constraint/lookupDataConstruction/' . $idConstraint);
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:180,
            url: '{$url}',
            idField:'entry',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'name', title:'Name', width:162}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

