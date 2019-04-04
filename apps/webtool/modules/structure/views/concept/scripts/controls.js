<script type="text/javascript">

$(function () {


    $('#formAddConceptElement').dialog({
        toolbar:'#formAddConceptElementTools',
        border:true,
        modal:true,
        closed:true,
        doSize:true,
        onClose:function() {
            structure.reloadConcept();
            //$('#formAddConceptElement').dialog('destroy', true);
        }
    });

    $('#concept_idConceptElement').combogrid({
        panelWidth:220,
        url: {{$manager->getURL('data/concept/lookupData')}},
        idField:'idConcept',
        textField:'name',
        mode:'remote',
        fitColumns:true,
        columns:[[
            {field:'idConcept', hidden:true},
            {field:'name', title:'Name', width:202}
        ]]
    });

    $('#formAddConceptElementSave').linkbutton({
        iconCls:'icon-save',
        plain:true,
        size:null,
        onClick: structure.formAddConceptElementSave
    });

    $('#formAddConceptElementClose').linkbutton({
        iconCls:'icon-cancel',
        plain:true,
        size:null,
        onClick: function() {
            $('#formAddConceptElement').dialog('close');
        }
    });

    $('#formAddConceptElementForm').form({
        url:{{$manager->getURL('structure/concept/addConceptElement')}},
        onSubmit: function(){
            // do some check
            // return false to prevent submit;
        },
        success:function(data){
            var data = eval('(' + data + ')');
            if (data.success){
                $.messager.alert('Info','Concept Element created!','info');
            }
        }
    });


    $('#menuRootConcepts').menu({});
    $('#menuConcept').menu({});
    $('#menuConceptElement').menu({});

    $('#type').textbox({
        buttonIcon: 'icon-search',
        iconAlign:'right',
        prompt: {{_'Search Type'}},
        onClickButton: function() {
            $('#conceptsTree').tree({queryParams: {type: $('#type').textbox('getValue')}});
        }
    });

    $('#conceptsTree').tree({
        url: {{$manager->getURL('structure/concept/conceptTree')}},
        onContextMenu: structure.contextMenuConcept
    });

});

</script>