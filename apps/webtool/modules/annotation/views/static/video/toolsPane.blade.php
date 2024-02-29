@php
    $videoWidth = $data->documentMM->videoWidth;
    $leftWidth = 340;
    $rightWidth = $videoWidth - ($leftWidth);
@endphp
<script>
    let toolsPane = {
        template: '#tools-pane',
        props: [],
        data() {
            return {
                // urlPutObjects: this.$store.state.model.urlPutObjects,
                framesRange: annotationVideoModel.framesRange,
                currentTime: 0,
                currentFrame: 0,
                currentVideoState: '',
                currentState: 'paused',
                currentMode: 'video',
                hasObjectSelected: false,
                isEditing: false,
                isPlayback: false,
            }
        },
        computed: {},
        created() {
            window.addEventListener('keyup', this.doKeyCommand);
        },
        methods: {
            onNewObject() {
                if (this.currentState === 'paused') {
                    this.$store.dispatch('newObject');
                    this.$store.commit('currentMode', 'object');
                    this.$store.commit('currentState', 'creating');
                }
            },
            onEndObject() {
                if ((this.currentState === 'editing')
                    || ((this.currentMode === 'object') && (this.currentState === 'paused'))){
                    this.$store.dispatch('endObject');
                    this.$store.commit('currentMode', 'video');
                    this.$store.commit('currentState', 'paused');
                }
            },
            onObjectBlocked() {
                if (this.currentState === 'editing') {
                    this.$store.dispatch('objectBlocked');
                }
            },
            onObjectVisible() {
                if (this.currentState === 'editing') {
                    this.$store.dispatch('objectVisible');
                }
            },
            onClearObject() {
                if (this.currentState === 'editing') {
                    this.$store.dispatch('clearObject');
                }
            },
            onObjectDelete() {
                if (this.currentState === 'editing') {
                    this.$store.commit('currentMode', 'paused');
                    this.$store.dispatch('deleteObject');
                }
            },
            onStartTrack() {
                if (this.currentState === 'paused') {
                    this.$store.dispatch('startTrackObject');
                    this.$store.commit('currentMode', 'object');
                    this.$store.commit('currentState', 'editing');
                }
            },
            onForwardClick() {
                if ((this.currentState === 'paused') || (this.currentState === 'editing')) {
                    this.currentFrame = this.$store.state.currentFrame;
                    if ((this.currentFrame >= this.framesRange.first) && (this.currentFrame < this.framesRange.last)) {
                        this.currentFrame = this.currentFrame + 1;
                        this.$store.commit('currentFrame', this.currentFrame);
                    }
                }
            },
            onBackwardClick() {
                if ((this.currentState === 'paused') || (this.currentState === 'editing')) {
                    this.currentFrame = this.$store.state.currentFrame;
                    if ((this.currentFrame > this.framesRange.first) && (this.currentFrame <= this.framesRange.last)) {
                        this.currentFrame = this.currentFrame - 1;
                        this.$store.commit('currentFrame', this.currentFrame);
                    }
                }
            },
            onBeginningClick() {
                if ((this.currentState === 'paused') || (this.currentVideoState === 'loaded')) {
                    this.currentFrame = this.$store.state.currentFrame;
                    if (this.currentFrame > this.framesRange.first) {
                        this.currentFrame = this.framesRange.first;
                        this.$store.commit('currentFrame', this.currentFrame);
                    }
                }
            },
            onPlayClick() {
                if ((this.currentState === 'paused') || (this.currentVideoState === 'loaded')) {
                    this.$store.commit('currentObject', null);
                    this.$store.commit('currentMode', 'video');
                    this.$store.commit('currentState', 'playing');
                }
            },
            onPlay2() {
                if ((this.currentState === 'paused') && (this.currentVideoState === 'ready')) {
                    this.$store.commit('currentMode', 'video');
                    this.$store.commit('currentState', 'playing2');
                }
            },
            onPlay5() {
                if ((this.currentState === 'paused') && (this.currentVideoState === 'ready')) {
                    this.$store.commit('currentMode', 'video');
                    this.$store.commit('currentState', 'playing5');
                }
            },
            onPlay8() {
                if ((this.currentState === 'paused') && (this.currentVideoState === 'ready')) {
                    this.$store.commit('currentMode', 'video');
                    this.$store.commit('currentState', 'playing8');
                }
            },
            onPauseClick() {
                if ((this.currentState === 'playing')
                    || (this.currentState === 'playing2')
                    || (this.currentState === 'playing5')
                    || (this.currentState === 'playing8')) {
                    this.$store.commit('currentState', 'paused');
                    this.$store.commit('currentStopFrame', 0);
                    this.$store.commit('currentMode', 'video');
                }
            },
            async onPlayAnnoClick(go_on) {
                if (go_on) {
                    this.$store.commit('currentState', 'playingAnno');
                    this.currentFrame = this.$store.state.currentFrame;
                    if (((this.currentFrame >= this.framesRange.first) && (this.currentFrame < this.framesRange.last))) {
                        this.currentFrame = this.currentFrame + 1;
                        this.$store.commit('currentFrame', this.currentFrame);
                        await new Promise(r => setTimeout(r, 500));
                        return this.onPlayAnnoClick(this.$store.state.currentState !== 'paused')
                    }
                }
            },
            onPauseAnnoClick() {
                if ((this.currentState === 'playingAnno')) {
                    this.$store.commit('currentState', 'paused');
                }
            },
            onShowBoxes() {
                if ((this.currentState === 'paused') && (this.currentVideoState === 'ready')) {
                    this.$store.commit('currentState', 'paused');
                    this.$store.commit('currentMode', 'video');
                    this.$emit('event-showboxes');
                }
            },
            doKeyCommand(e) {
                let keyCode = e.keyCode;
                if (keyCode === 32) {
                    if (this.currentState === 'paused') {
                        this.onPlayClick();
                    } else {
                        this.onPauseClick();
                    }
                }
                if (keyCode === 37) {
                    this.onBackwardClick();
                }
                if (keyCode === 39) {
                    this.onForwardClick();
                }
            },
        },
        mounted: function () {
            this.currentState = this.$store.state.currentState;
            this.currentVideoState = this.$store.state.currentVideoState;
            this.hasObjectSelected = (this.$store.state.currentObject != null);
            this.$store.watch(
                (state, getters) => getters.currentVideoState,
                (currentVideoState) => {
                    this.currentVideoState = currentVideoState;
                }
            )
            this.$store.watch(
                (state, getters) => getters.currentState,
                (currentState) => {
                    this.currentState = currentState;
                }
            )
            this.$store.watch(
                (state, getters) => getters.currentMode,
                (currentMode) => {
                    this.currentMode = currentMode;
                    this.isEditing = (currentMode === 'object');
                    this.isPlayback = (currentMode === 'video');
                }
            )
            this.$store.watch(
                (state, getters) => getters.currentObject,
                (currentObject) => {
                    this.hasObjectSelected = (currentObject != null);
                }
            )
        },
        watch: {}
    }

</script>

<script type="text/x-template" id="tools-pane">
    <div id="toolsPaneDiv" style="width:{{$videoWidth}}px; display:flex; flex-direction: row;">
        <div id="toolsPaneLeft">
            <LinkButton
                    id="btnBeginning"
                    :plain="true"
                    @click="onBeginningClick"
                    :disabled="isEditing || isPlayback"
            ><i class="fas fa-fast-backward" ></i>
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
            <span>|</span>
            <LinkButton
                    id="play2"
                    iconCls="faTool fas fa-thermometer-quarter"
                    :plain="true"
                    @click="onPlay2"
                    title="Play 0.2"
                    :disabled="isEditing"
            ></LinkButton>
            <LinkButton
                    id="play5"
                    iconCls="faTool fas fa-thermometer-half"
                    :plain="true"
                    @click="onPlay5"
                    title="Play 0.5"
                    :disabled="isEditing"
            ></LinkButton>
            <LinkButton
                    id="play8"
                    iconCls="faTool fas fa-thermometer-three-quarters"
                    :plain="true"
                    @click="onPlay8"
                    title="Play 0.8" :disabled="isEditing">

            </LinkButton>
        </div>
        <div id="toolsPaneRight" style="width:{{$rightWidth}}px">
            <LinkButton
                    id="btnShowBoxes"
                    iconCls="faTool fas fa-eye"
                    :plain="true"
                    @click="onShowBoxes"
                    title="Show/Hide All Boxes in Frame"
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
                    iconCls="faTool fas fa-trash-alt"
                    :plain="true"
                    @click="onObjectDelete"
                    title="Delete Object"
                    :disabled="!hasObjectSelected || isPlayback || (currentState == 'creating')"
            ></LinkButton>
            <LinkButton
                    id="btnStartTrack"
                    iconCls="faTool fa fa-pencil-square-o"
                    :plain="true"
                    @click="onStartTrack"
                    title="Start tracking"
                    :disabled="!hasObjectSelected"
                    title="Edit Tracking"
            ></LinkButton>
            <LinkButton
                    id="btnEndObject"
                    iconCls="material-outlined  wt-icon-end"
                    :plain="true"
                    @click="onEndObject"
                    title="End Object"
                    :disabled="!isEditing"
            ></LinkButton>
            <LinkButton
                    id="btnPauseAnno"
                    :plain="true"
                    @click="onPauseAnnoClick"
                    :disabled="!isEditing"
                    title="Pause Tracking"
            ><i class="fa fa-pause-circle-o"></i>
            </LinkButton>
            <LinkButton
                    id="btnPlayAnno"
                    :plain="true"
                    @click="onPlayAnnoClick(true)"
                    :disabled="!isEditing"
                    title="Start Tracking"
            ><i class="fa fa-play-circle-o"></i>
            </LinkButton>
            <LinkButton
                    id="btnNewObject"
                    iconCls="material-outlined  wt-icon-create"
                    :plain="true" @click="onNewObject"
                    title="New Object"
                    :disabled="isEditing"
            ></LinkButton>
            <div style="width:220px">
                @{{currentMode}} : @{{currentState}}
            </div>
        </div>
        <!--
        <form id="formObjects" method="post" :action="urlPutObjects">
            <input type="hidden" id="dataObjects" name="dataObjects" value=""/>
        </form>
        -->

    </div>
</script>

