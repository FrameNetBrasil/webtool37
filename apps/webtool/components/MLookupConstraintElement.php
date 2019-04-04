<?php

class MLookupConstraintElement extends MControl
{

    public function generate()
    {
        $idConstraint = $this->data->idConstraint;
        $url = Manager::getAppURL('', 'data/constraint/lookupDataByCE/' . $idConstraint);
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combogrid({
            panelWidth:220,
            url: '{$url}',
            idField:'idConstruction',
            textField:'name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'idConstruction', hidden:true},
                {field:'name', title:'Name', width:202}
            ]]
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '270px';
        return $this->getPainter()->mtextField($this);
    }

}

