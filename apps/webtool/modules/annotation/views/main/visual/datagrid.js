<script type="text/javascript">
        var columns = [
            {field:'idObject', title:'ID'},
            {field:'startFrame', title:'start'},
            {field:'endFrame', title:'end'},
            {field:'frameElement', title:'FE'},
        ];

        console.log(columns);

        $('#objectsGrid').datagrid({
            url:{{$manager->getURL('annotation/main/objectsData')}} + "/" + {{$data->idAnnotationSetMM}} + "/" + {{$data->type}},
            method:'get',
            //collapsible:true,
            //fitColumns: false,
            //autoRowHeight: false,
            //nowrap: true,
            //rowStyler: annotation.rowStyler,
            showHeader: true,
            onBeforeSelect: function() {return false;},
            //onSelect: annotation.onSelect,
            //onClickCell: annotation.onClickCell,
            //onRowContextMenu: annotation.onRowContextMenu,
            //onHeaderContextMenu: annotation.onHeaderContextMenu,
            //toolbar: tb,
            //frozenColumns: [frozenColumns],
            columns: [columns],
            //onLoadSuccess: function() {
            //    annotation.initCursor();
            //}
        });


</script>