<?php

class formNewLexemeFields extends MVContainer
{

    public function generate()
    {
        $lexeme = $this->data->lexeme;
        $hidden = new MHiddenField(['id'=>'lexeme_listWordform','name'=>'lexeme_listWordform','value' =>'']);
        $this->addControl($hidden);
        $code = <<<EOT

        var listWordform = [];
                
        $('#newWordForm').textbox({
            buttonIcon: 'icon-add',
            iconAlign:'right',
            prompt: 'Wordform',
            onClickButton: function() {
                console.log(listWordform);
                var value = $('#newWordForm').textbox('getValue');
                console.log(value);
                listWordform[listWordform.length] = {wordform: value, del: value};
                $('#lexeme_listWordform').attr('value', 'json:' + JSON.stringify(listWordform));
                $('#{$lexeme}Wordforms').datagrid({data: listWordform});
            }
        });             

        $('#{$lexeme}Wordforms').datagrid({
            idField:'idLexeme',
            width: 190,
            height: 120,
            data: [],
            showHeader:false,
            showFooter:false,
            checkOnSelect: false,
            singleSelect: true,
            toolbar: '#divNewWordForm',
            onClickCell: function( index,field,value) {
                var newListWordform = [];
                for(var i = 0; i < listWordform.length; i++) {
                    if (listWordform[i].del != value) {
                        newListWordform[newListWordform.length] = listWordform[i];
                    }
                }
                listWordform = newListWordform;
                $('#lexeme_listWordform').attr('value', 'json:' + JSON.stringify(listWordform));
                $('#{$lexeme}Wordforms').datagrid({data: listWordform});
            },
            columns:[[
                {field:'del', width:20, formatter: function(value,row,index){return "<span id='del" + value + "' class='fa fa-close'></span>"}},
                {field:'wordform', title:'{$this->data->lexeme} [{$this->data->language}]', width:170}
            ]]
        });
                
EOT;
        $toolbar = new MDiv(['id'=>'divNewWordForm']);
        $input = new MTextField(['id'=>'newWordForm']);
        $toolbar->addControl($input);
        $this->addControl($toolbar);
        $dg = new MHtml(['tag' => 'table', 'id'=>"{$lexeme}Wordforms"]);
        $this->addControl($dg);
        
        $onload .= $code;
        $this->page->onLoad($onload);
        $this->addStyle('padding-left','0px');
        return $this->painter->mvcontainer($this);
    }

}
