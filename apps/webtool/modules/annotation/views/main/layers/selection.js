<script type="text/javascript">
    // layers/selection.js
    $(function () {

        annotation.onSelect = function (rowIndex, rowData) {
        }

        annotation.onClickCell = function (rowIndex, field, value) {
            var rows=$('#layers').datagrid("getRows");
            var row = rows[rowIndex];
            console.log(row);
            console.log(field);
            if (row.idLayerType == 0){
                return;
            }
            if ((row.idLayerType == 25) && (field == 'layer')) {
                return;
            }
            if (row.layerTypeEntry.substr(0,8) == 'lty_cefe'){
                return;
            }
            if (field == 'ni') {
                annotation.clearSelection(annotation.currentSelection.rowIndex);
                //console.log(annotation.layerType[row.idLayerType]);
                if (annotation.layerType[row.idLayerType]['entry'] == 'lty_fe') {
                    annotation.currentSelection.rowIndex = rowIndex;
                    var comboEditor = {
                        type:'combobox',
                        options:{
                            valueField:'idInstantiationType',
                            textField:'instantiationType',
                            data: annotation.instantiationType
                        }
                    };
                    var labels = annotation.layerLabels[row.idLayer];
                    console.log(labels);
                    var fes = []; var i = 0; var j = 0;
                    jQuery.each(labels, function (i, idLabel) {
                        var label = annotation.labelTypes[idLabel];
                        console.log(label);
                        if ((label['coreType'] == 'cty_core') || (label['coreType'] == 'cty_core-unexpressed')) {
                            var value = '';
                            if (typeof annotation.nis[row.idLayer] != 'undefined') {
                                if (typeof annotation.nis[row.idLayer][idLabel] != 'undefined') {
                                    value = annotation.nis[row.idLayer][idLabel]['idInstantiationType'];
                                }
                            }
                            fes[j++] = {idLayer: row.idLayer, idLayerType:row.idLayerType, name:idLabel, value:value, editor:comboEditor};
                        }
                    });
                    console.log(fes);
                    $('#pg').propertygrid({data: fes});
                    $('#pg').propertygrid({idLayer: row.idLayer});
                    $('#pg').propertygrid({
                        columns:[[
                            {
                                field:'name', width:200, title:'Element',
                                styler: annotation.cellStyler,
                                formatter: annotation.cellFormatter
                            },
                            {
                                field: 'value', width:70, title: 'IT',
                                formatter: function(value,row,index){
                                    var r = '';
                                    jQuery.each(annotation.instantiationType, function (i, it) {
                                        if (value == it.idInstantiationType) {
                                            r = it.instantiationType;
                                        }
                                    });
                                    return r;
                                }
                            }
                        ]]
                    });
                    $('#dlgNI').dialog('doLayout');
                    $('#dlgNI').dialog('open');
                    annotation.pushTopDialog('#dlgNI');
                }
                return;
            } else {
                if (annotation.chars[field]['char'] == ' ') {
                    return;
                }
                var shift = false;
                //console.log(annotation.e);
                if (annotation.e) {
                    shift = (annotation.e.which == 16);
                }
                if (!shift) {
                    annotation.clearSelection(annotation.currentSelection.rowIndex);
                }
                //console.log(rowIndex);
                //console.log(field);
                annotation.markSelection(rowIndex, field);
                document.getSelection().removeAllRanges();
            }
        }

        annotation.markSelection = function(rowIndex, field) {
            if (annotation.currentSelection.rowIndex > 0 ) {
                if (rowIndex != annotation.currentSelection.rowIndex) {
                    annotation.clearSelection(annotation.currentSelection.rowIndex);
                }
            }
            console.log(rowIndex);
            console.log(field);
            var cursorRowIndex = rowIndex;
            var rows = $('#layers').datagrid('getRows');
            var row = rows[rowIndex];
            //console.log(row);

            var columns = $('#layers').datagrid('getColumnFields');
            var start = end = -1;
            // se já tiver anotação nesta camada na coluna escolhida, usa os limites da anotação
            var idLabel = row[field];
            if (idLabel) {
                pstart = pend = parseInt(field.substr(2,5));
                console.log(pstart);
                while (row['wf' + pstart] == idLabel) {
                    start = pstart--;
                }
                while (row['wf' + pend] == idLabel) {
                    end = pend++;
                }
            }

            if (start == -1) {
                if ((row.layerTypeEntry == 'lty_gf') || (row.layerTypeEntry == 'lty_pt')) {
                    // se já tiver anotação na camada FE na coluna escolhida, usa os limites do FE
                    var tempCursorRowIndex = rowIndex;
                    if (row.idLayerType != 1) {
                        for (r in rows) {
                            var tempRow = rows[r];
                            if ((tempRow.idLayerType == 1) && (tempRow.idAnnotationSet == row.idAnnotationSet)) {
                                tempCursorRowIndex = r;
                                var idLabel = tempRow[field];
                                console.log('idLabel = ' + idLabel);
                                if (idLabel) {
                                    //console.log(tempRow);
                                    pstart = pend = parseInt(field.substr(2, 5));
                                    while (tempRow['wf' + pstart] == idLabel) {
                                        start = pstart--;
                                    }
                                    while (tempRow['wf' + pend] == idLabel) {
                                        end = pend++;
                                    }
                                }
                            }
                        }
                    }
                    if (start == -1) {
                        cursorRowIndex = tempCursorRowIndex;
                    }
                }
            }

            if (start == -1) {
                // se não existe anotação nesta camada na coluna escolhida, usa os limites da palavra correspondente à coluna
                for (var column = 0; column < columns.length; column++) {
                    var f = columns[column];
                    if ((annotation.currentSelection.fields[f]) || (f == field)) {
                        var word = annotation.words[annotation.chars[f]['word']];
                        if (start == -1) {
                            start = word['startChar'];
                            end = word['endChar'];
                        }
                        if (f == field) {
                            end = word['endChar'];
                        }
                    }
                }
            }
            if (start > -1) {
                annotation.clearSelection(cursorRowIndex);
                for (var i = start; i <= end; i++) {
                    var f = 'wf' + i;//columns[i];
                    $( "tr[datagrid-row-index|='" + cursorRowIndex +  "'] > td[field=" + f + "]" ).addClass( "cellSelected" );
                    annotation.currentSelection.fields[f] = true;
                }
                annotation.currentSelection.start = start;
                annotation.currentSelection.end = end;
            }
            annotation.currentSelection.rowIndex = cursorRowIndex;
            annotation.cursor.rowIndex = cursorRowIndex;
            annotation.cursor.field = annotation.currentSelection.start;
        }

        annotation.clearSelection = function(rowIndex) {
            $( "tr[datagrid-row-index|='" + rowIndex +  "'] > td").removeClass( "cellSelected" );
            annotation.currentSelection.rowIndex = -1;
            annotation.currentSelection.fields = {};
        }


    });
</script>