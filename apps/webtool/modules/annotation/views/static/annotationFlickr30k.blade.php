@php
    $url = $manager->getAppFileURL($manager->getApp()) . '/modules/annotation/views/static';
    $panelWestWidth = $data->imageMM->width + 24;
    $panelImageHeight = $data->imageMM->height + 40;
@endphp

<link rel="stylesheet" type="text/css" href="{{$url . '/image/annotationImage.css'}}">

<script type="text/javascript" src="{{$url . '/scripts/vatic/dist/compatibility.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/dist/jszip.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/dist/StreamSaver.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/dist/polyfill.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/dist/jsfeat.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/dist/nudged.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/dist/pouchdb.min.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/vatic.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/FramesManager.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/OpticalFlow.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/BoundingBox.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/AnnotatedFrame.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/AnnotatedBox.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/AnnotatedObject.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/AnnotatedObjectsTracker.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/simple-notify/simple-notify.min.js'}}"></script>

<!-- Vue -->
<script type="text/javascript" src="{{$url . '/scripts/vue/vue.min.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vue/vuex.min.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vue/easyui.js'}}"></script>

<script type="text/javascript" src="{{$url . '/image/annotationImageStore.js'}}"></script>

@include('flickr30k/titlePane')

@include('flickr30k/objectPane')
@include('flickr30k/boxPane')

@include('flickr30k/toolsPane')
@include('flickr30k/workPane')

@include('flickr30k/sentencePane')
@include('flickr30k/gridPane')
@include('flickr30k/imagePane')


<script type="text/javascript">
    $(function () {
        let model = {
            sentenceMM: @json($data->staticSentenceMM),
            sentence: @json($data->sentence),
            imageMM: @json($data->imageMM),
            document: @json($data->document),
            documentMM: @json($data->documentMM),
            objects: @json($data->objects),
            //sentenceObjects: @json($data->sentenceObjects),
            urlLookupFrame: '{{$data->urlLookupFrame}}',
            urlLookupFE: '{{$data->urlLookupFE}}',
            flickr30kMode: 1,
        };
        Vue.use(EasyUI);
        console.log(model);
        const vapp = new Vue({
            el: "#annotationImagePane",
            components: {
                'title-pane': titlePane,
                'object-pane': objectPane,
                'box-pane': boxPane,
                'grid-pane': gridPane,
                'sentence-pane': sentencePane,
                'image-pane': imagePane,
            },
            store: annotationImageStore,
            created() {
                this.$store.commit('model', model);
                this.$store.commit('objectsTrackerState', 'clean');
            },
            data: () => {
                return {
                    flickr30k: model.documentMM.flickr30k
                }
            }
        })

        $('#annotationImagePane').layout({
            fit: true,
        });

    });
</script>


<div id="annotationImagePane">
    <div id="annotationImagePaneTitle"
         data-options="region:'north', collapsible:false, title:'Static Mode Annotation', border:0">
        <title-pane></title-pane>
    </div>
    <div data-options="region:'center',border:0">
        <div style="display:flex; flex-direction: row;">
            <div id="annotationImagePaneWest" style="width:{{$panelWestWidth}}px;">
                <div style="display:flex; flex-direction: column;">
                    <div id="annotationImagePaneImage" style="width:100%;height:{{$panelImageHeight}}px">
                        <image-pane></image-pane>
                    </div>
                    <div id="annotationImagePaneBox" style="width:100%;height:400px">
                        <box-pane></box-pane>
                    </div>
                </div>
            </div>
            <div id="annotationImagePaneCenter" style="width:900px;">
                <div id="annotationImagePaneEntity" style="display:flex; flex-direction:row;">
                    <div id="annotationImagePaneObjects" style="width:770px;height:320px;">
                        <grid-pane></grid-pane>
                    </div>
                    <div id="annotationImagePaneObject" style="width:250px;height:320px">
                        <object-pane></object-pane>
                    </div>
                </div>
                <div v-if="flickr30k=='1'" id="annotationImagePaneSentence" style="width:100%;height:500px">
                    <sentence-pane></sentence-pane>
                </div>
            </div>
        </div>
    </div>
</div>
