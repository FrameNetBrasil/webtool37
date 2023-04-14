<script type="text/javascript">
// layers/vars.js

var annotation = {
        chronometer: 0,
        dataIsSaved: true,
        topDialog: '',
        timeout: {{$data->sessionTimeout|noescape}},
        type: {{$data->type}},
        canSave: {{$data->canSave}},
        isMaster: {{$data->isMaster|noescape}},
        isSenior: {{$data->isSenior|noescape}},
        rgbColors: {{$data->colors|noescape}},
        layerType: {{$data->layerType|noescape}},
        instantiationType: {{$data->instantiationType|noescape}},
        instantiationTypeObj: {{$data->instantiationTypeObj|noescape}}
    };

var UDTree = {
    element: 'UDTreeCanvas',
    UDTreeCurrent: null
}


$(function () {

        annotation.idSubCorpus = {{$data->idSubCorpus}};

        annotation.e = null;
        $(document).on('keydown', function(e) {
            annotation.e = e;
        });
        $(document).on('keyup', function(e) {
            annotation.e = null;
        });

        annotation.coreIcon = {"cty_core": "fa-icon fa fa-circle", "cty_core-unexpressed": "fa-icon fa fa-circle", "cty_peripheral": "fa-icon fa fa-dot-circle-o", "cty_extra-thematic": "fa-icon fa fa-circle-o"};

        annotation.idSentence = {{$data->idSentence}};

        annotation.idAnnotationSet = {{$data->idAnnotationSet}};

        annotation.words = {{$data->layers['words']|noescape}};

        annotation.chars = {{$data->layers['chars']|noescape}};

        annotation.annotationSets = {{$data->layers['annotationSets']|noescape}};

        annotation.layers = {{$data->layers['layers']|noescape}};

        annotation.labelTypes = {{$data->layers['labelTypes']|noescape}};

        annotation.layerLabels = {{$data->layers['layerLabels']|noescape}};

        annotation.nis = {{$data->layers['nis']|noescape}};

        annotation.UDTreeLayer = {{$data->layers['UDTreeLayer']|noescape}};

        annotation.data = {{$data->layers['data']|noescape}};

        annotation.lus = {};

        annotation.currentSelection = {
            rowIndex: -1,
            fields: {}
        }

        annotation.cursor = {
            rowIndex: 1,
            field: 0
        }

        annotation.rows = {};
    });

</script>