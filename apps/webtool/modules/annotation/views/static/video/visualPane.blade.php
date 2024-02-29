<script>
    let visualPane = {
            template: '#visual-pane',
            components: {
                'video-pane': videoPane,
                'grid-pane': gridPane,
                'dialog-pane': dialogPane
            },
            props: [],
            data() {
                return {
                    title: 'Video Annotation',
                    videoTitle: this.$store.state.model.documentMM.videoTitle,
                    currentState: this.$store.state.currentState,
                    player: null,
                    playerData: null,
                    ignore_timeupdate: false,
                    reDrawFrame: false,
                    updateGrid: false,
                    options: {},
                    objects: [],
                    object: null,
                    newObject: {
                        index: 0,
                        frame: -1
                    },
                }
            },
            computed: {},
            created() {
            },
            mounted() {
            },
            methods: {
            },
        }
</script>

<script type="text/x-template" id="visual-pane">
    <Panel id="visualAnnotation" :border="false"
           style="width:100%;height:100%;padding:16px;background-color:#263238;" v-on:keyup.left="keyLeft" v-on:keyup.right="keyRight">
        <div style="display:flex; flex-direction: column; height: 100%; width: 100%;align-items: flex-start;background-color:#263238;">
            <div id="videoTtile" style="background-color:#263238;padding-bottom:8px;">
                <h3 style="color:white">@{{videoTitle}}</h3>
            </div>
            <div v-if="currentState != 'initing'"  id="videoPane" style="display:flex; flex-direction: column; width:auto">
                <video-pane></video-pane>
                <div id="dialogObject" style="display:none">
                    <dialog-pane></dialog-pane>
                </div>
            </div>
            <div id="gridPane" style="width:{{$data->documentMM->videoWidth}}px">
                <grid-pane></grid-pane>
            </div>
        </div>
    </Panel>
</script>

