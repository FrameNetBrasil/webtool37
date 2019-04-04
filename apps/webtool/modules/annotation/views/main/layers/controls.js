<script type="text/javascript">
    // layers/controls.js
    $(function () {

        $('#dlgNI').dialog({
            modal:true,
            closed:true,
            toolbar:'#dlgNI_tools',
            border:true,
            doSize:true
        });


        $('#dlgNISave').linkbutton({
            iconCls:'icon-save',
            plain:true,
            size:null,
            onClick: annotation.dlgNISave
        });

        $('#dlgNIClose').linkbutton({
            iconCls:'icon-cancel',
            plain:true,
            size:null,
            onClick: function() {
                $('#dlgNI').dialog('close');
            }
        });

        $('#pg').propertygrid({
            showGroup:false,
            scrollbarSize:18,
            data: []
        });

        $('#dlgMWE').dialog({
            modal:true,
            closed:true,
            toolbar:'#dlgMWE_tools',
            idField:'idWord'
        });

        $('#dlgMWESave').linkbutton({
            iconCls:'icon-save',
            plain:true,
            size:null,
            onClick: annotation.dlgMWESave
        });

        $('#dlgMWEClose').linkbutton({
            iconCls:'icon-cancel',
            plain:true,
            size:null,
            onClick: function() {
                $('#dlgMWE').dialog('close');
            }
        });

        $('#mwe').datagrid({
            scrollbarSize:18
        });

        $('#dlgSubCorpus').dialog({
            modal:true,
            closed:true,
            toolbar:'#dlgSubCorpus_tools',
            border:true,
            doSize:true
        });

        $('#dlgSubCorpusSave').linkbutton({
            iconCls:'icon-save',
            plain:true,
            size:null,
            onClick: annotation.dlgSubCorpusSave
        });
        $('#dlgSubCorpusClose').linkbutton({
            iconCls:'icon-cancel',
            plain:true,
            size:null,
            onClick: function() {
                $('#dlgSubCorpus').dialog('close');
            }
        });

        $('#dlgAS').dialog({
            modal:true,
            closed:true,
            toolbar:'#dlgAS_tools',
            border:true,
            doSize:true,
            idField:'idAnnotationSet'
        });

        $('#dlgASSave').linkbutton({
            iconCls:'icon-save',
            plain:true,
            size:null,
            onClick: annotation.dlgASSave
        });
        $('#dlgASClose').linkbutton({
            iconCls:'icon-cancel',
            plain:true,
            size:null,
            onClick: function() {
                $('#dlgAS').dialog('close');
            }
        });

        $('#dlgASRemove').dialog({
            modal:true,
            closed:true,
            toolbar:'#dlgASRemove_tools',
            border:true,
            doSize:true,
            idField:'idAnnotationSet'
        });

        $('#dlgASRemoveSave').linkbutton({
            iconCls:'icon-save',
            plain:true,
            size:null,
            onClick: annotation.dlgASSaveRemove
        });
        $('#dlgASRemoveClose').linkbutton({
            iconCls:'icon-cancel',
            plain:true,
            size:null,
            onClick: function() {
                $('#dlgASRemove').dialog('close');
            }
        });

        $('#dlgASComments').dialog({
            modal:true,
            closed:true,
            toolbar:'#dlgASComments_tools',
            border:true,
            doSize:true,
            idField:'idAnnotationSet',
            onLoad: function() {
                manager._handleResponse['parse']('#dlgASComments');
            }
        });

        $('#dlgASCommentsSave').linkbutton({
            iconCls:'icon-save',
            plain:true,
            size:null,
            onClick: annotation.dlgASCommentsSave
        });

        $('#dlgASCommentsClose').linkbutton({
            iconCls:'icon-cancel',
            plain:true,
            size:null,
            onClick: function() {
                $('#dlgASComments').dialog('close');
            }
        });

        $('#dlgASInfo').dialog({
            modal:true,
            closed:true,
            toolbar:'#dlgASInfo_tools',
            border:true,
            doSize:true,
            idField:'idAnnotationSet',
        });

        $('#dlgASInfoClose').linkbutton({
            iconCls:'icon-cancel',
            plain:true,
            size:null,
            onClick: function() {
                $('#dlgASInfo').dialog('close');
            }
        });

        $('#dlgUDTree').dialog({
            modal:true,
            closed:true,
            toolbar:'#dlgUDTree_tools',
            border:true,
            doSize:true,
            UDTreeCurrent: null,
            onOpen: function() {
                console.log('opening');
                var UDTreeCurrent = UDTree.UDTreeCurrent;
                console.log(UDTreeCurrent);
                if (UDTreeCurrent != null) {
                    console.log(UDTree);
                    if (UDTree.start !== undefined) {
                        console.log('starting');
                        UDTree.start(UDTreeCurrent);
                    }
                }

            },
            onClose: function() {
                UDTree.destroy();
            }
        });

        $('#dlgUDTreeRefresh').linkbutton({
            iconCls:'icon-reload',
            plain:true,
            size:null,
            onClick: function() {
                if (UDTree.refreshTree !== undefined) {
                    UDTree.refreshTree();
                }
            }
        });

        $('#dlgUDTreeSave').linkbutton({
            iconCls:'icon-save',
            plain:true,
            size:null,
            onClick: function() {
                if (UDTree.saveTree !== undefined) {
                    console.log('saving');
                    UDTree.saveTree();
                }
            }
        });

        $('#dlgUDTreeClose').linkbutton({
            iconCls:'icon-cancel',
            plain:true,
            size:null,
            onClick: function() {
                $('#dlgUDTree').dialog('close');
            }
        });

        $('#dlgValidation').dialog({
            modal:true,
            closed:true,
            toolbar:[{
                text:'Send',
                iconCls:'fa fa-envelope-o fa16px',
                handler: function(){
                    manager.doPostBack('formValidation');
                    $('#dlgValidation').dialog('close');
                }
            }],
            onClose: function() {
                //$('#dlgValidation').dialog('destroy', true);
            }
        });

        $('#asGrid').datagrid({
            scrollbarSize:18
        });

        $('#asGridRemove').datagrid({
            scrollbarSize:18
        });

        $('#dlgCxn').dialog({
            modal:true,
            closed:true,
            toolbar:'#dlgCxn_tools',
            idField:'idConstruction'
        });

        $('#dlgCxnSave').linkbutton({
            iconCls:'icon-save',
            plain:true,
            size:null,
            onClick: annotation.dlgCxnSave
        });

        $('#dlgCxnClose').linkbutton({
            iconCls:'icon-cancel',
            plain:true,
            size:null,
            onClick: function() {
                $('#dlgCxn').dialog('close');
            }
        });


        $('#cxnGrid').datagrid({
            scrollbarSize:18
        });

        $('#feedback').textbox({
            multiline:true,
            width: 250,
            height: 100
        });

    });
</script>