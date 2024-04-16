@php
$url = $manager->getAppFileURL($manager->getApp()) . '/modules/annotation/views/multimodal';
@endphp

<script type="text/javascript" src="{{$url . '/scripts/easyui/datagrid-filter.js'}}"></script>

<table id="sentencesMM"  style="width:100%" >
    <thead>
    <tr>
        <th data-options="field:'idSentenceMM',sortable:true" width="5%">idSentence</th>
        <th data-options="field:'image', align:'left'" width="20%">Image</th>
        <th data-options="field:'text'" width="75%">Sentence</th>
    </tr>
    </thead>
</table>

<script type="text/javascript">
    $(function () {

        annotation.idDocumentMM = {{$data->documentMM->idDocumentMM}};

        $('#sentencesMM').datagrid({
            title: '{{$data->documentMM->title}}',
            fit: true,
            nowrap: false,
            idField: 'idSentenceMM',
            url: '{{$manager->getURL('annotation/multimodal/imageSentenceMultimodal')}}' + '/' + annotation.idDocumentMM,
            method: 'get',
            onSelect: function (rowIndex, rowData) {
                window.open('{{$manager->getURL('annotation/multimodal/annotationImageSentence')}}' + '/' + rowData.idSentenceMM, '_blank');
            }
        });

        $('#sentencesMM').datagrid('enableFilter', [{
            field:'image',
            type:'numberbox',
            options:{precision:0},
            //op:['equal','notequal','less','greater']
        }]);
    });
</script>
