<?php

class MGridFieldLU extends MVContainer
{

    public function generate()
    {
        $hidden = new MHiddenField(['id'=>'gridfieldlu_listLU','name'=>'gridfieldlu_listLU','value' =>'']);
        $this->addControl($hidden);
        
        $id = $this->property->id;
        $url = Manager::getAppURL('', $this->property->action) . '/' . $this->data->id;
        $onLoad = <<<EOT
                
        // obtem dados
                
        manager.doAjax('{$url}', function (data) {
            if (data) {
                listLU = data;
            }
        }, 'gridfieldlu_listLU');
                
        console.log(listLU);

        $('#gridfieldlu_datagrid').datagrid({
            idField:'idLU',
            width: 310,
            height: 120,
            data: listLU,
            showHeader:false,
            showFooter:false,
            checkOnSelect: false,
            singleSelect: true,
            toolbar: '#divNewLU',
            onClickCell: function( index,field,value) {
        console.log(index);
        console.log(field + ' = ' + value);
                var newListLU = [];
                for(var i = 0; i < listLU.length; i++) {
                    if (listLU[i].del != value) {
                        newListLU[newListLU.length] = listLU[i];
                    }
                }
                listLU = newListLU;
                $('#gridfieldlu_listLU').attr('value', 'json:' + JSON.stringify(listLU));
                $('#gridfieldlu_datagrid').datagrid({data: listLU});
            },
            columns:[[
                {field:'del', width:20, formatter: function(value,row,index){return "<span id='del" + value + "' class='fa fa-close'></span>"}},
                {field: 'idLU'},
                {field:'fullname', title:'LU', width:170}
            ]]
        });
        
EOT;

        $code = <<<EOT
        var constraints = null;
                
        var listLU = [];
        
        function lbAddLU_click() {
            console.log(listLU);
            var text = $('#{$id}_lookupLU').combogrid('getText');
            var value = $('#{$id}_lookupLU').combogrid('getValue');
            console.log(value);
            listLU[listLU.length] = {idLU: value, fullname: text, del: value};
            $('#gridfieldlu_listLU').attr('value', 'json:' + JSON.stringify(listLU));
            $('#gridfieldlu_datagrid').datagrid({data: listLU});
        }          
                
EOT;

        $toolbar = new MDiv(['id'=>'divNewLU']);
        $lookup = new MLookupLU();
        $lookup->setId("{$id}_lookupLU");
        $toolbar->addControl($lookup);
        $lbAdd = new MLinkButton(['id' => "lbAddLU", 'action' => "!lbAddLU_click();", 'iconCls' => "icon-add", 'plain' => true]);
        $toolbar->addControl($lbAdd);
        $toolbar->addControl($input);
        $this->addControl($toolbar);
        $dg = new MHtml(['tag' => 'table', 'id'=>"gridfieldlu_datagrid"]);
        $this->addControl($dg);
        
        $this->page->onLoad($onLoad);
        $this->page->addJsCode($code);
        $this->addStyle('padding-left','0px');
        return $this->painter->mvcontainer($this);
    }
}

