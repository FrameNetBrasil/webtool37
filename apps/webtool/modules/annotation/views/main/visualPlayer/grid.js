<script type="text/javascript">
    $(function () {
        $('#objectsGrid').datagrid({
            data: [],
            title: 'Objects',
            showHeader: true,
            width: '100%',
            columns: [[
                {field:'idObject', title:'ID'},
                {field:'visible', title:'Visible', editor:{type:'checkbox',options:{on:'True',off:'False'}}},
                {field:'hide', title:'Hide Others', editor:{type:'checkbox',options:{on:'True',off:'False'}}},
                {field:'idFE', title:'idFE'},
                {field:'fe', title:'FE'},
                {field:'startTime', title:'Start Time'},
                {field:'endTime', title:'End Time'},
            ]],
            onClickRow: function(index,row) {
                console.log(row);
                annotation.editObject = row;
                $('#formObject').form('load', row);
                $('#dlgObject').dialog('open');
                document.getElementById('currentTime').innerHTML = config.currentTime;
            },
            onBeforeSelect: function() {return false;},
    });

    annotation.updateObjectsGrid = () => {
        let data = [];
        for (let i = 0; i < annotatedObjectsSet.annotatedObjects.length; i++) {
            let annotatedObject = annotatedObjectsSet.annotatedObjects[i];
            console.log(annotatedObject);
            let row = {
                idObject: annotatedObject.id,
                visible: annotatedObject.visible,
                hide: annotatedObject.hide,
                idFE: annotatedObject.idFE,
                fe: annotatedObject.fe,
                startTime: annotatedObject.startTime,
                endTime: annotatedObject.endTime,
            }
            data.push(row);
        }
        $('#objectsGrid').datagrid({
            data: data
        })
    }
});
</script>