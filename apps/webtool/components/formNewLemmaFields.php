<?php
class formNewLemmaFields extends MHContainer
{

    public function generate()
    {
        $posData = [];
        foreach($this->data->POS as $idPOS => $pos) {
            $posData[] = "{value: '{$pos}', text: '{$pos}'}";
        }
        $posString = implode(',', $posData);
        $lemma = $this->data->lemma;
        if (($p = strpos($lemma,'.')) !== false) {
            $POS = strtoupper(substr($lemma, $p+1));
            $lemma = substr($lemma, 0, $p);
        }
        $html = <<<EOT
            <table id="pg" style="width:300px"></table>
EOT;
        $lexemes = explode(' ', $lemma);
        $i = 0; $rows = "";
        foreach($lexemes as $lexeme) {
            $rows .= ($i ? ',' : '');
            $pos = ($i ? 'N' : $POS);
            $hw = ($i ? 'false' : 'true');
            $rows .= "{name:'POS',value:'{$pos}','group':'{$lexeme}',editor: {
                type:'combobox',
		        options: {
                     valueField:'value',
                     textField:'text',
                     data:[
                        {$posString}
                     ],
                     required:true
                }}},";
            $rows .= "{name:'breakBefore',value:false,'group':'{$lexeme}',editor: {
                type: 'checkbox',
		        options: {'on': true, 'off': false}
		    }},";
            $rows .= "{name:'headWord',value:{$hw},'group':'{$lexeme}',editor: {
                type: 'checkbox',
		        options: {'on': true, 'off': false}
		    }}";
            $i++;
        }
        $data = "{total: {$i},rows:[ ". $rows . "]}";

        $onLoad = <<<EOT

	    $('#pg').propertygrid({
	        data: {$data},
            showGroup: true,
            scrollbarSize: 0
    });

EOT;

        $app = Manager::getApp();
        $code = <<<EOT
        function saveLemma() {
			var s = '';
			var rows = $('#pg').propertygrid('getRows');
			var lexemes = {};
			for(var i = 0; i < rows.length; i++){
				lexemes[rows[i].group] = {};
			}
			for(var i = 0; i < rows.length; i++){
				lexemes[rows[i].group][rows[i].name]= rows[i].value;
			}
			console.log(lexemes);
			$('#lexemes').attr('value','json:' + JSON.stringify(lexemes));

            manager.doAction('@structure/frame/newLemma|formNewLemma');
        }

EOT;
        $this->page->onLoad($onLoad);
        $this->page->addJsCode($code);
        return $html;
    }

}
