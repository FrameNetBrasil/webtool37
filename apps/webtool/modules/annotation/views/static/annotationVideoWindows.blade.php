@php
    $url = $manager->getAppFileURL($manager->getApp()) . '/modules/annotation/views/multimodal';
    $panelWestWidth = $data->documentMM->videoWidth + 24;
    $panelVideoHeight = $data->documentMM->videoHeight + 80 + 40;
@endphp

<link rel="stylesheet" type="text/css" href="{{$url . '/scripts/simple-notify/simple-notify.min.css'}}">
<link rel="stylesheet" type="text/css" href="{{$url . '/video/annotationVideo.css'}}">

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
<script type="text/javascript" src="{{$url . '/scripts/vatic/AnnotatedObject.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vatic/AnnotatedObjectsTracker.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/simple-notify/simple-notify.min.js'}}"></script>

<!-- jp-player -->
<script type="text/javascript" src="{{$url . '/scripts/jplayer/jquery.jplayer.min.js'}}"></script>
<link rel="stylesheet" type="text/css" href="{{$url . '/scripts/jplayer/blue.monday/css/jplayer.blue.monday.css'}}">

<!-- Vue -->
<script type="text/javascript" src="{{$url . '/scripts/vue/vue.min.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vue/vuex.min.js'}}"></script>
<script type="text/javascript" src="{{$url . '/scripts/vue/easyui.js'}}"></script>

<script type="text/javascript" src="{{$url . '/video/annotationVideoStore.js'}}"></script>
<script type="text/javascript" src="{{$url . '/video/annotationVideoAPI.js'}}"></script>

@include('video/titlePane')
@include('video/objectPane')
@include('video/timePane')
@include('video/toolsPane')
@include('video/workPane')

@include('video/sentencePane')
@include('video/gridPane')
@include('video/videoPane')


<script type="text/javascript">
    $(function () {
        window.annotationVideoModel = {
            documentMM: @json($data->documentMM),
            {{--objects: @json($data->objects), --}}
            {{--urlPutObjects: "{{$data->urlPutObjects}}",--}}
            {{--swfPath: '{{$data->swfPath}}',--}}
            fps: 25,
            flickr30kMode: 0, // image annotation compatibility
            url: {
                lookupFE: 'webtool/data/frameelement/lookupDataDecorated',
                lookupLU: 'webtool/data/lu/lookupdata?idLanguage=2',
                sentences: 'webtool/annotation/multimodal/annotationSetMultimodal'
            },
            api: dynamicAPI,
            objectsTracker: {
                tracker: null,
                framesManager: null,
                init: () => {
                    console.log('initing objectsTracker');
                    annotationVideoModel.objectsTracker.framesManager = new FramesManager();
                    annotationVideoModel.objectsTracker.tracker = new AnnotatedObjectsTracker(annotationVideoModel.objectsTracker.framesManager);
                },
                add: (annotatedObject) => {
                    annotatedObject.idObject = annotationVideoModel.objectsTracker.tracker.getLength() + 1;
                    annotationVideoModel.objectsTracker.tracker.add(annotatedObject);
                },
                push: (annotatedObject) => {
                    annotationVideoModel.objectsTracker.tracker.add(annotatedObject);
                },
                get: (idObject) => {
                    return annotationVideoModel.objectsTracker.tracker.annotatedObjects.find(o => o.idObject === idObject);
                },
                clear: (annotatedObject) => {
                    annotationVideoModel.objectsTracker.tracker.clear(annotatedObject);
                },
                clearAll: () => {
                    annotationVideoModel.objectsTracker.tracker.clearAll();
                }
            },
            time: {
                end: 0,
            },
            framesRange: {
                first: 1,
                last: 1
            }
        };
//        store.commit('model', model);
        Vue.use(EasyUI);
        //console.log(model);
        const vapp = new Vue({
            el: "#annotationVideoPane",
            components: {
                'title-pane': titlePane,
                'object-pane': objectPane,
                'grid-pane': gridPane,
                'sentence-pane': sentencePane,
                'video-pane': videoPane,
            },
            store: dynamicStore,
            created() {
                //this.$store.commit('model', model);
            }
        })

        $('#annotationVideoPane').layout({
            fit: true,
        });

        $('#win01').window({
            width:{{$panelWestWidth}},
            height:{{$panelVideoHeight}},
            modal:false,
            inline: true,
            closable: false,
            minimizable:false,
            maximizable: false,
        });

        $('#win02').window({
            width:{{$panelWestWidth}},
            height:275,
            constraint:true,
            modal:false,
            inline: true,
            closable: false,
            minimizable:false,
            maximizable: false,
        });
        $('#win03').window({
            width:{{$panelWestWidth}},
            height:{{$panelVideoHeight}},
            modal:false,
            inline: true,
            closable: false,
            minimizable:false,
            maximizable: false,
        });
        $('#win04').window({
            width:{{$panelWestWidth}},
            height:275,
            modal:false,
            inline: true,
            closable: false,
            minimizable:false,
            maximizable: false,
        });
    });
</script>


<div id="annotationVideoPane">
    <div id="annotationVideoPaneTitle" data-options="region:'north', collapsible:false, title:'Multimodal Video Annotation', border:0">
        <title-pane></title-pane>
    </div>
    <div data-options="region:'center',border:0" >
        <div>
            <div id="win01" style="position: relative;top:0; left: 0;padding:8px">
                <div id="annotationVideoPaneVideo" style="width:100%;height:100%">
                    <video-pane></video-pane>
                </div>
            </div>
            <div id="win02" style="top:0; left: 880px">
                <div id="annotationVideoPaneObject" style="width:100%;height:260px;">
                    <object-pane></object-pane>
                </div>
            </div>
            <div id="win03" style="top:280px; left: 880px">
                <div id="annotationVideoPaneGridObject" style="width:100%;height:260px;">
                    <grid-pane></grid-pane>
                </div>
            </div>
            <div id="win04" style="top:600px; left: 0">
                <div id="annotationVideoPaneGridSentence" style="width:100%;height:260px;">
                    <sentence-pane></sentence-pane>
                </div>
            </div>
            <!--
            <div id="annotationVideoPaneWest" style="width:{{$panelWestWidth}}px;">
                <div style="display:flex; flex-direction: column;">
                    <div id="annotationVideoPaneVideo" style="width:100%;height:{{$panelVideoHeight}}px">
                        <video-pane></video-pane>
                    </div>

                    <div id="annotationVideoPaneObject" style="width:100%;height:260px;">
                        <object-pane></object-pane>
                    </div>

                </div>
            </div>
            <div id="annotationVideoPaneCenter" style="width:1200px;">
                <div style="display:flex; flex-direction: column;">
                    <div id="annotationVideoPaneGridObject" style="width:100%;height:{{$panelVideoHeight}}px">
                        <grid-pane></grid-pane>
                    </div>
                    <div id="annotationVideoPaneGridSentence" style="width:100%;height:260px;">
                        <sentence-pane></sentence-pane>
                    </div>

                </div>
            </div>
            -->
        </div>
    </div>
</div>
