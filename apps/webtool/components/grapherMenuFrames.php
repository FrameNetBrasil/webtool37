<?php

class GrapherMenuFrames extends MBaseGroup {
    
    public function onCreate() {
        parent::onCreate();
        $data = Manager::getData();
        $db = $data->db;

        $frame = new fnbr\models\Frame();
        $initials = $frame->listInitialsForReport()->asQuery()->chunkResult(0,0);        
        
        $i = 0;
        $p = new MContentPane('p');
        $p->addControl(new MLabel('Índice de Frames [Frame Index]','black',false));
        $p->addControl(new MRawText('<br>'));
        foreach($initials as $char) {
            $p->addControl(new MLink($char,'','grapher/listFrames?db=' . $db . '#'.$char,$char,'listFrames'));
            $p->addControl(new MLabel('&nbsp;&nbsp'));
            if(++$i > 12) {
                $i = 0;
                $p->addControl(new MRawText('<br>'));
            }
        }
        
        $this->addControl($p);
        $this->addControl(new MSpacer());
        $this->setFieldset(false);
    }
    
}
