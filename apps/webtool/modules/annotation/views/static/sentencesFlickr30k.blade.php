@php
$url = $manager->getAppFileURL($manager->getApp()) . '/modules/annotation/views/static';
@endphp

<script type="text/javascript" src="{{$url . '/scripts/easyui/datagrid-filter.js'}}"></script>

<table id="sentencesMM"  style="width:100%" >
</table>

<script type="text/javascript">
    $(function () {

        annotation.idDocument = {{$data->idDocument}};
        annotation['flickr30k'] = {{$data->flickr30k}};
        var columns = [];
        if (annotation['flickr30k'] === 2) {
            columns = [
                {
                    field: 'idStaticSentenceMM',
                    title: 'idStaticSentenceMM',
                    sortable: true,
                },
                {
                    field: 'idSentence',
                    title: 'idSentence',
                    sortable: true,
                },
                {
                    field: 'image',
                    title: 'Image',
                    align: 'left'
                },
                {
                    field: 'status',
                    width: 56,
                    title: 'Status',
                    formatter: function (value, row, index) {
                        if (value == 'green') {
                            return "<i style='color:" + value + "' class='fas fa-check'></i>";
                        } else if (value == 'yellow') {
                            return "<i style='color:gold' class='fas fa-exclamation-triangle'></i>";
                        }
                        return "<i style='color:" + value + "' class='fas fa-ban'></i>";
                    },
                },
            ];
        } else {
            columns = [
                {
                    field: 'idStaticSentenceMM',
                    title: 'idStaticSentenceMM',
                    sortable: true,
                },
                {
                    field: 'idSentence',
                    title: 'idSentence',
                    sortable: true,
                },
                {
                    field: 'image',
                    title: 'Image',
                    align: 'left'
                },
                {
                    field: 'text',
                    title: 'Sentence',
                    align: 'left'
                },
                {
                    field: 'status',
                    width: 56,
                    title: 'Status',
                    formatter: function (value, row, index) {
                        if (value == 'green') {
                            return "<i style='color:" + value + "' class='fas fa-check'></i>";
                        } else if (value == 'yellow') {
                            return "<i style='color:gold' class='fas fa-exclamation-triangle'></i>";
                        }
                        return "<i style='color:" + value + "' class='fas fa-ban'></i>";
                    },
                },
            ];
        }

        $('#sentencesMM').datagrid({
            title: "{{$data->documentName}}",
            fit: true,
            nowrap: false,
            pagination: true,
            pageSize: 20,
            idField: 'idSentenceMM',
            columns: [
                columns
            ],
            data:[],
            url: '{{$manager->getURL('annotation/static/imageSentenceMultimodal')}}' + '/' + annotation.idDocument,
            method: 'get',
            onSelect: function (rowIndex, rowData) {
                window.open('{{$manager->getURL('annotation/static/annotationFlickr30k')}}' + '/' + rowData.idStaticSentenceMM, '_blank');
            }
        });

        $('#sentencesMM').datagrid('enableFilter', [{
            field:'image',
            type:'numberbox',
            options:{precision:0},
        }]);
    });
</script>
