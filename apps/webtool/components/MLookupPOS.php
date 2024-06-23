<?php

class MLookupPOS extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/lexeme/lookupPOSData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combobox({
            url:'{$url}',
            valueField:'idPOS',
            textField:'POS'
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        return $this->getPainter()->mtextField($this);
    }

}
