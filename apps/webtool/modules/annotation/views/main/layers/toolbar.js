<script type="text/javascript">
    // layers/toolbar.js
    $(function () {

        annotation.labelHelp = function () {
            window.open({{$manager->getURL('annotation/main/labelhelp')}}, '_blank');
        }

        annotation.toolbarNull = [

        ];

        {{if ($data->canSave) }}
        annotation.toolbar = [
            {
                text:'<u>S</u>ave',
                iconCls:'fa fa-folder-o fa16px',
                handler: function(){
                    annotation.save();
                }
            },
            {
                text:'<u>R</u>efresh',
                iconCls:'fa fa-refresh fa16px',
                handler: function(){
                    annotation.refresh();
                }
            },
            /*
            {
                text:'Doubt',
                iconCls:'fa fa-frown-o fa16px',
                handler: function(){
                    var data = JSON.stringify(annotation.annotationSets);
                    $('#annotationSets').val(data);
                    $('#validation').val('-1');
                    manager.doPostBack('formValidation');
                }
            },
            {
                text:'Ignore',
                iconCls:'fa fa-ban fa16px',
                handler: function(){
                    var data = JSON.stringify(annotation.annotationSets);
                    $('#annotationSets').val(data);
                    $('#validation').val('-2');
                    manager.doPostBack('formValidation');
                }
            },
            */
            {
                text:'Remove AS',
                iconCls:'fa fa-remove fa16px',
                handler: function(){
                    annotation.dlgASOpenRemove();
                }
            },
            {
                text:'Label Help',
                iconCls:'fa fa-question-circle-o fa16px',
                handler: function(){
                    annotation.labelHelp();
                }
            }
        ];

        annotation.toolbarSenior = [
            {
                text:'<u>S</u>ave',
                iconCls:'fa fa-folder-o fa16px',
                handler: function(){
                    annotation.save();
                }
            },
            {
                text:'<u>R</u>efresh',
                iconCls:'fa fa-refresh fa16px',
                handler: function(){
                    annotation.refresh();
                }
            },
            /*
            {
                text:'Approve',
                iconCls:'fa fa-thumbs-o-up fa16px',
                handler: function(){
                    var data = JSON.stringify(annotation.annotationSets);
                    $('#annotationSets').val(data);
                    $('#validation').val('1');
                    manager.doPostBack('formValidation');
                }
            },
            {
                text:'Disapprove',
                iconCls:'fa fa-thumbs-o-down fa16px',
                handler: function(){
                    var data = JSON.stringify(annotation.annotationSets);
                    $('#annotationSets').val(data);
                    $('#validation').val('0');
                    //manager.doPostBack('formValidation');
                    annotation.dlgValidationOpen();
                }
            },
            {
                text:'Ignore',
                iconCls:'fa fa-ban fa16px',
                handler: function(){
                    var data = JSON.stringify(annotation.annotationSets);
                    $('#annotationSets').val(data);
                    $('#validation').val('-2');
                    manager.doPostBack('formValidation');
                }
            },
            */
            {
                text:'Label Help',
                iconCls:'fa fa-question-circle-o fa16px',
                handler: function(){
                    annotation.labelHelp();
                }
            }
        ];

        annotation.toolbarMaster = [
            {
                text:'<u>S</u>ave',
                iconCls:'fa fa-folder-o fa16px',
                handler: function(){
                    annotation.save();
                }
            },
            {
                text:'<u>R</u>efresh',
                iconCls:'fa fa-refresh fa16px',
                handler: function(){
                    annotation.refresh();
                }
            },
            /*
            {
                text:'Approve',
                iconCls:'fa fa-thumbs-o-up fa16px',
                handler: function(){
                    var data = JSON.stringify(annotation.annotationSets);
                    $('#annotationSets').val(data);
                    $('#validation').val('1');
                    manager.doPostBack('formValidation');
                }
            },
            {
                text:'Disapprove',
                iconCls:'fa fa-thumbs-o-down fa16px',
                handler: function(){
                    var data = JSON.stringify(annotation.annotationSets);
                    $('#annotationSets').val(data);
                    $('#validation').val('0');
                    //manager.doPostBack('formValidation');
                    annotation.dlgValidationOpen();
                }
            },
            {
                text:'Ignore',
                iconCls:'fa fa-ban fa16px',
                handler: function(){
                    var data = JSON.stringify(annotation.annotationSets);
                    $('#annotationSets').val(data);
                    $('#validation').val('-2');
                    manager.doPostBack('formValidation');
                }
            },
            {
                text:'Release',
                iconCls:'fa fa-unlock fa16px',
                handler: function(){
                    var data = JSON.stringify(annotation.annotationSets);
                    $('#annotationSets').val(data);
                    $('#validation').val('-3');
                    manager.doPostBack('formValidation');
                }
            },
            */
            {
                text:'Hide AS',
                iconCls:'fa fa-minus-square-o fa16px',
                handler: function(){
                    annotation.dlgASOpen();
                }
            },
            {
                text:'Remove AS',
                iconCls:'fa fa-remove fa16px',
                handler: function(){
                    annotation.dlgASOpenRemove();
                }
            },
            {
                text:'Add Cxn',
                iconCls:'fa fa-plus-square-o fa16px',
                handler: function(){
                    annotation.dlgCxnOpen();
                }
            },
            {
                text:'Label Help',
                iconCls:'fa fa-question fa16px',
                handler: function(){
                    annotation.labelHelp();
                }
            }
        ];

        {{/if}}

    });
</script>