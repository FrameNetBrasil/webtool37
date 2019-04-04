<script type="text/javascript">
    // layers/save.js
    $(function () {
        {{if ($data->canSave) }}

        annotation.save = function() {
            var panel = $('#layers').datagrid("getPanel");
            annotation.showMessage(panel, "Saving AnnotationSets...");
            $('#type').val(annotation.type);
            var data = annotation.getDataToPost();
            $('#dataLayers').val(data);
            manager.doPostBack('formLayers');
            annotation.cleanData();
            annotation.hideMessage(panel);
        }
        {{/if}}

        annotation.refresh = function() {
            $('#dlgValidation').dialog('destroy');
            $('#dlgNI').dialog('destroy');
            $('#dlgMWE').dialog('destroy');
            $('#dlgASComments').dialog('destroy');
            $('#dlgASInfo').dialog('destroy');
            $('#dlgSubCorpus').dialog('destroy');
            $('#dlgAS').dialog('destroy');
            $('#dlgASRemove').dialog('destroy');
            $('#dlgCxn').dialog('destroy');
            $('#layersPane').panel('refresh');
        }

        annotation.getDataToPost = function() {
            var data = [];
            var i = 0;
            var rows = $('#layers').datagrid('getRows');
            for (r in rows) {
                var row = rows[r];
                var line = {};
                line['ni'] = {};
                for (field in row) {
                    if (field == 'ni') {
                        if (annotation.nis[row['idLayer']]) {
                            line['ni'][row['idLayer']] = {};
                            for (idLabel in annotation.nis[row['idLayer']]) {
                                line['ni'][row['idLayer']][idLabel] = {
                                    idInstantiationType: annotation.nis[row['idLayer']][idLabel]['idInstantiationType'],
                                    idSentenceWord: annotation.nis[row['idLayer']][idLabel]['idSentenceWord']
                                };
                            }
                        }
                    }
                    else {
                        line[field] = row[field];
                    }
                }
                data[i++] = line;
            }
            //console.log(data);
            return JSON.stringify(data);
        }

        annotation.checkSavedData = function() {
            console.log('checkSavedData: ' + (annotation.dataIsSaved ? 'true' : 'false'));
            if (!annotation.dataIsSaved) {
                $.messager.alert('Warning','Save your data before this operation!','warning');
                return false;
            }
            return true;
        }

        annotation.dirtyData = function () {
            console.log('dirtyData');
            annotation.dataIsSaved = false;
            $('#layersPane .datagrid-header-inner').css('background-color','#ffcccc');
        }

        annotation.cleanData = function () {
            console.log('cleanData');
            annotation.dataIsSaved = true;
            $('#layersPane .datagrid-header-inner').css('background-color','#efefef');
        }
    });
</script>