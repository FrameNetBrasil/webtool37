<?php

class MLanguage extends MControl
{

    public function generate()
    {
        $url = Manager::getAppURL('', 'data/language/comboData');
        $onLoad = <<<EOT
        
        $('#{$this->property->id}').combobox({
            url:'{$url}',
            valueField:'idLanguage',
            textField:'language'
        });

EOT;
        $this->getPage()->onLoad($onLoad);
        $this->style->width = '45px';
        return $this->getPainter()->mtextField($this);
    }

}

