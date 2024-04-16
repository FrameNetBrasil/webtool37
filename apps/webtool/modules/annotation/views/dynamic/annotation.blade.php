@php
    $url = $manager->getAppFileURL($manager->getApp()) . '/modules/annotation/views/dynamic';
    $panelWestWidth = $data->documentMM->videoWidth + 24;
    $panelVideoHeight = $data->documentMM->videoHeight + 90;
    $videoWidth = $data->documentMM->videoWidth;
    $timeWidth = 140;
    $controlsWidth = 286;
    $sliderWidth = $videoWidth;// - (2 * $timeWidth);
    $videoWidth = $data->documentMM->videoWidth;
    $leftWidth = 340;
    $rightWidth = 340;//$videoWidth - ($leftWidth);
    $rootFolder = $manager->getConf('charon.rootFolder')

@endphp

<link rel="stylesheet" type="text/css" href="{{$url . '/scripts/simple-notify/simple-notify.min.css'}}">
<link rel="stylesheet" type="text/css" href="{{$url . '/dynamic.css'}}">
<link rel="stylesheet" type="text/css" href="{{$url . '/annotationVideo.css'}}">

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

<script type="text/javascript" src="{{$url . '/dynamicStore.js'}}"></script>
<script type="text/javascript" src="{{$url . '/dynamicAPI.js'}}"></script>
<script type="text/javascript" src="{{$url . '/dynamicObjects.js'}}"></script>

<script type="text/javascript" src="{{$url . '/titlePane.js'}}"></script>
<script type="text/javascript" src="{{$url . '/objectPane.js'}}"></script>
<script type="text/javascript" src="{{$url . '/workPane.js'}}"></script>
<script type="text/javascript" src="{{$url . '/sentencePane.js'}}"></script>
<script type="text/javascript" src="{{$url . '/gridPane.js'}}"></script>
<script type="text/javascript" src="{{$url . '/videoPane.js'}}"></script>

<script type="text/javascript">
    $(function () {
        window.annotationVideoModel = {
            documentMM: @json($data->documentMM),
            document: @json($data->document),
            corpus: @json($data->corpus),
            fps: 25,
            flickr30kMode: 0, // image annotation compatibility
            videoPath: '',
            api: dynamicAPI,
            time: {
                end: 0,
            },
            framesRange: {
                first: 1,
                last: 1
            },
            currentIdObjectMM: -1,
            boxesContainer: null,
            currentScale: 1.0
        };
        Vue.use(EasyUI);
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
                annotationVideoModel.videoPath = annotationVideoModel.documentMM.videoPath.replace('{{$rootFolder}}', '');
            }
        })

        $('#annotationVideoPane').layout({
            fit: false
        });

        $('#annotationVideoPaneTitle').panel({
            title: 'Annotation: Dynamic Mode  |  Corpus: ' + annotationVideoModel.corpus.name + '  |  Document: ' + annotationVideoModel.document.name
        });

    });
</script>


<div id="annotationVideoPane" style="width:100%;height:810px;">
    <div id="annotationVideoPaneTitle"
         data-options="region:'north', collapsible:false, title:'Annotation: Dynamic Mode', border:0" >
        <title-pane></title-pane>
    </div>
    <div data-options="region:'west',border:0,split:true" style="padding:5px 0px 0px 8px;width:{{$panelWestWidth}}px;">
        <div style="display:flex; flex-direction: column;">
            <div id="annotationVideoPaneVideo" style="width:100%;height:{{$panelVideoHeight}}px">
                <video-pane></video-pane>
            </div>
        </div>
    </div>
    <div data-options="region:'center',border:0">
        <div id="annotationVideoPaneCenter" style="width:100%;">
            <div style="display:flex; flex-direction: column;">
                <div id="annotationVideoPaneGridObject" style="width:100%;height:550px;padding:5px;">
                    <grid-pane></grid-pane>
                </div>
            </div>
        </div>
    </div>
    <div data-options="region:'south',border:0,split:true" style="padding:5px">
        <div id="annotationVideoPaneSouth" style="width:100%;">
            <div style="display:flex; flex-direction: row;">
                <div id="annotationVideoPaneObject" style="width:700px;height:100%;padding:0px 5px">
                    <object-pane></object-pane>
                </div>
                <div id="annotationVideoPaneGridSentence" style="width:100%;">
                    <sentence-pane></sentence-pane>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/x-template" id="title-pane">
    <h3 class="title">@{{videoTitle}}</h3>
</script>

<script type="text/x-template" id="object-pane">
    <div id="objectPane" style="height:150px">
        <div id="objectPaneLayout" style="display:flex; flex-direction:row;">
            <div id="objectPaneLeft" style="width:100%;padding:8px">
                <div id="objectPaneForm" style="display:flex; flex-direction:column;">
                    <div style="display:flex; flex-direction:row;width:100%">
                        <div style="margin-right:8px"><input id="startFrame"/></div>
                        <div style="margin-right:8px"><input id="endFrame"/></div>
                        <div style="margin-right:8px"><input id="lookupFrame"/></div>
                        <div><input id="lookupFE"/></div>
                    </div>
                    <div style="display:flex; flex-direction:row;width:100%">
                        <div style="margin-right:8px"><input id="lookupLU"/></div>
                        <div style="margin-top:8px"><div style="height:14px">&nbsp;</div><div><a href="#" id="btnSubmit"/></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/x-template" id="work-pane">
    <div style="width:{{$videoWidth}}px; display:flex; flex-direction: column">

        <div id="timePane" style="width:100%;">
            <div id="sliderContainer" style="width:{{$sliderWidth}}px">
                <input id="sliderVideo">
            </div>
        </div>
        <div id="toolsPane" style="width:100%; height:32px;display:flex; flex-direction: row">
            <div id="timePaneLeft" style="width:{{$timeWidth}}px">
                @{{slider.value}} [@{{displayTime}}]
            </div>
            <div id="toolsPaneLeft" style="width:{{$controlsWidth}}px">
                <LinkButton
                        id="btnBeginning"
                        :plain="true"
                        @click="onBeginningClick"
                        :disabled="isEditing || isPlayback"
                ><i class="fas fa-fast-backward"></i>
                </LinkButton>
                <LinkButton
                        id="btnBackward"
                        :plain="true"
                        @click="onBackwardClick"
                ><i class="fas fa-step-backward"></i>
                </LinkButton>
                <LinkButton
                        id="btnPlay"
                        :plain="true"
                        @click="onPlayClick"
                        :disabled="isEditing"
                ><i class="fas fa-play"></i>
                </LinkButton>
                <LinkButton
                        id="btnPause"
                        :plain="true"
                        @click="onPauseClick"
                        :disabled="isEditing"
                ><i class="fas fa-pause"></i>
                </LinkButton>
                <LinkButton
                        id="btnForward"
                        :plain="true"
                        @click="onForwardClick"
                ><i class="fas fa-step-forward"></i>
                </LinkButton>
                <LinkButton
                        id="play2"
                        iconCls="material wt-icon-playarrow"
                        iconAlign="right"
                        :plain="true"
                        @click="onPlay2"
                        title="Play 0.2"
                        :disabled="isEditing"
                >0.2
                </LinkButton>
                <LinkButton
                        id="play5"
                        iconCls="material wt-icon-playarrow"
                        iconAlign="right"
                        :plain="true"
                        @click="onPlay5"
                        title="Play 0.5"
                        :disabled="isEditing"
                >0.5
                </LinkButton>
                <LinkButton
                        id="play8"
                        iconCls="material wt-icon-playarrow"
                        iconAlign="right"
                        :plain="true"
                        @click="onPlay8"
                        title="Play 0.8"
                        :disabled="isEditing"
                >0.8
                </LinkButton>
            </div>
            <div id="toolsPaneRight" style="width:{{$controlsWidth}}px">
                <LinkButton
                        id="btnShowBoxes"
                        :iconCls="showBoxes ? 'faTool material-outlined wt-icon-show' : 'faTool material-outlined wt-icon-hide'"
                        :plain="true"
                        @click="onShowBoxes"
                        title="Show/Hide All Boxes in current Frame"
                        :disabled="isEditing || isPlayback"
                ></LinkButton>
                <LinkButton
                        id="btnObjectVisible"
                        iconCls="faTool far fa-image"
                        :plain="true"
                        @click="onObjectVisible"
                        title="Visible"
                        :disabled="!hasObjectSelected || isPlayback"
                ></LinkButton>
                <LinkButton
                        id="btnObjectBlocked"
                        iconCls="faTool far fa-images"
                        :plain="true"
                        @click="onObjectBlocked"
                        title="Blocked"
                        :disabled="!hasObjectSelected || isPlayback"
                ></LinkButton>
                <LinkButton
                        id="btnObjectDelete"
                        iconCls="faTool material  wt-icon-delete"
                        :plain="true"
                        @click="onObjectDelete"
                        title="Delete Object"
                        :disabled="!hasObjectSelected || isPlayback || isCreating"
                ></LinkButton>
                <!--
                <LinkButton
                        id="btnStopEdit"
                        iconCls="faTool material-outlined  wt-icon-stop"
                        :plain="true"
                        @click="onStopEdit"
                        title="Stop Edit"
                        :disabled="(!(isEditing || isPaused)) || isCreating"
                ></LinkButton>
                -->
                <LinkButton
                        id="btnObjectEdit"
                        iconCls="faTool material-outlined  wt-icon-edit"
                        :plain="true"
                        @click="onObjectEdit"
                        title="Edit Object"
                        :disabled="!hasObjectSelected"
                ></LinkButton>
                <LinkButton
                        id="btnEndObject"
                        iconCls="faTool material-outlined  wt-icon-end"
                        :plain="true"
                        @click="onEndObject"
                        title="End Object"
                        :disabled="(!(isEditing || isPaused)) || isCreating"
                ></LinkButton>
                <LinkButton
                        id="btnPauseAnno"
                        iconCls="faTool material-outlined  wt-icon-pauseTracking"
                        :plain="true"
                        @click="onPauseAnnoClick"
                        :disabled="(!isTracking)"
                        title="Pause Tracking"
                >
                </LinkButton>
                <LinkButton
                        id="btnPlayAnno"
                        iconCls="faTool material-outlined  wt-icon-startTracking"
                        :plain="true"
                        @click="onPlayAnnoClick(true)"
                        :disabled="(!(isEditing || isPaused)) || isCreating"
                        title="Start Tracking"
                >
                </LinkButton>
                <LinkButton
                        id="btnNewObject"
                        iconCls="faTool material-outlined  wt-icon-create"
                        :plain="true" @click="onNewObject"
                        title="New Object"
                        :disabled="isEditing || isCreating"
                ></LinkButton>
            </div>
            <div id="timePaneRight" style="width:{{$timeWidth}}px">
                @{{slider.max}} [@{{totalDuration}}]
            </div>
        </div>
        <!--
        <div style="width:100%; height:32px;">
            <div style="width:220px">
                @{{currentState}} @{{currentFrame}}
            </div>
        </div>
        -->
    </div>
</script>

<script type="text/x-template" id="sentence-pane">
    <div ref="gridSentences" id="gridSentences">
    </div>
</script>

<script type="text/x-template" id="grid-pane">
    <div ref="gridObjects" id="gridObjects">
    </div>
</script>

<script type="text/x-template" id="video-pane">
    <div style="display:flex; flex-direction: column; width:auto">
        <div ref="doodle" id="doodle" @mousemove="onMouseMove" @click="onMouseClick">
            <div id="jquery_jplayer_1" class="jp-jplayer"></div>
            <canvas ref="canvas" id="canvas" style="display:none">
            </canvas>
        </div>
        <div ref="jp_container" id="jp_container" role="application" aria-label="media player" style="text-align:left">
        </div>
        <div id="workPane">
            <work-pane v-if="showControls"></work-pane>
        </div>
        <div ref="boxesContainer" id="boxesContainer">

        </div>
    </div>
</script>

