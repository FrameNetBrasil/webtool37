let workPane = {
    template: '#work-pane',
    data() {
        return {
            slider: {
                min: 0,
                max: 0,
                value: 0,
            },
            currentFrame: 1,
            currentVideoState: this.$store.state.currentVideoState,
            framesRange: annotationVideoModel.framesRange,
            currentTime: 0,
        }
    },
    created() {
        window.addEventListener('keyup', this.doKeyCommand);
    },
    computed: {
        displayTime: function () {
            return this.formatTime(this.$store.getters.currentTime);
        },
        totalDuration: function () {
            return this.formatTime(annotationVideoModel.time.end);
        },
        showBoxes: function () {
            return (this.$store.state.showBoxes);
        },
        currentState: function () {
            return (this.$store.state.currentState);
        },
        currentFrame: function () {
            return (this.$store.state.currentFrame);
        },
        hasObjectSelected: function () {
            return (this.$store.state.currentObject !== null);
        },
        currentObject: function () {
            return (this.$store.state.currentObject);
        },
        isEditing: function () {
            return (this.$store.state.currentState === 'objectEditing');
        },
        isCreating: function () {
            return (this.$store.state.currentState === 'objectCreating');
        },
        isTracking: function () {
            return (this.$store.state.currentState === 'objectTracking');
        },
        isPaused: function () {
            return (this.$store.state.currentState === 'objectPaused');
        },
        isPlayback: function () {
            return (this.$store.state.currentState === 'videoPlaying')
                || (this.$store.state.currentState === 'videoPlaying2')
                || (this.$store.state.currentState === 'videoPlaying5')
                || (this.$store.state.currentState === 'videoPlaying8');
        }
    },
    methods: {
        formatTime(timeToFormat) {
            let dt1 = timeToFormat;
            let ft = parseFloat(dt1);
            let i = parseInt(dt1);
            let ms = parseInt((ft - i) * 1000);
            //let show = i + '.' + ms + 's';
            let show = i + 's';
            return show;
        },
        changeValue(v) {
            if ((this.currentState === 'paused') || (this.currentVideoState === 'loaded')) {
                if ((v >= this.slider.min) && (v <= this.slider.max)) {
                    this.$store.commit('currentFrame', v);
                }
            }
        },
        updateSlider() {
            this.slider.value = this.currentFrame;
            let value = 0;
            if (this.currentFrame > 0) {
                value = Math.round((100 * (this.currentFrame - this.slider.min) / (this.slider.max - this.slider.min)));
            }
            $('#sliderVideo').slider('setValue', value);
        },
        //
        // Tools
        //
        onNewObject() {
            if (this.currentState === 'videoPaused') {
                //this.$store.dispatch('newObject');
                // this.$store.commit('currentMode', 'object');
                this.$store.commit('currentState', 'objectCreating');
            }
        },
        onEndObject() {
            if ((this.currentState === 'objectEditing')
                || (this.currentState === 'videoPaused')
                || (this.currentState === 'objectPaused')) {
                //this.$store.dispatch('endObject');
                // this.$store.commit('currentMode', 'video');
                this.$store.dispatch('currentObjectEndFrame', this.$store.state.currentFrame);
                console.log('ending frame = ', this.$store.state.currentObject.endFrame)
                this.$store.commit('currentState', 'videoPaused');
                dynamicObjects.saveCurrentObject();
                this.$store.commit('updateObjectPane', true);
            }
        },
        onObjectBlocked() {
            if ((this.currentState === 'objectEditing')
                || (this.currentState === 'videoPaused')) {
                this.$store.dispatch('objectBlocked');
            }
        },
        onObjectVisible() {
            if ((this.currentState === 'objectEditing')
                || (this.currentState === 'videoPaused')) {
                this.$store.dispatch('objectVisible');
            }
        },
        onObjectDelete() {
            let currentObject = this.$store.state.currentObject;
            if (currentObject) {
                if ((this.currentState === 'objectEditing')
                    || (this.currentState === 'videoPaused')) {
                    this.$store.commit('currentState', 'videoPaused');
                    // this.$store.dispatch('deleteObject');
                    dynamicObjects.deleteObject(currentObject);
                }
            }
        },
        onObjectEdit() {
            let currentObject = this.$store.state.currentObject;
            if (currentObject) {
                if (this.currentState === 'videoPaused') {
                    // this.$store.dispatch('startTrackObject');
                    // this.$store.commit('currentMode', 'object');
                    currentObject.endFrame = annotationVideoModel.framesRange.last;
                    this.$store.commit('redrawFrame', true);
                    this.$store.commit('currentState', 'objectEditing');
                }
            }
        },
        onStopEdit() {
            if (this.currentState === 'objectEditing') {
                this.$store.commit('redrawFrame', true);
                this.$store.commit('currentState', 'videoPaused');
                dynamicObjects.saveCurrentObject();
                this.$store.commit('updateObjectPane', true);
            }
        },
        async onPlayAnnoClick(go_on) {
            if (go_on) {
                this.$store.commit('currentState', 'objectTracking');
                this.currentFrame = this.$store.state.currentFrame;
                if (((this.currentFrame >= this.framesRange.first) && (this.currentFrame < this.framesRange.last))) {
                    this.currentFrame = this.currentFrame + 1;
                    this.$store.commit('currentFrame', this.currentFrame);
                    await new Promise(r => setTimeout(r, 500));
                    return this.onPlayAnnoClick(this.$store.state.currentState !== 'objectPaused')
                }
            }
        },
        onPauseAnnoClick() {
            if (this.currentState === 'objectTracking') {
                this.$store.commit('currentState', 'objectPaused');
            }
        },
        onShowBoxes() {
            if ((this.currentState === 'videoPaused') && (this.currentVideoState === 'ready')) {
                // this.$store.commit('currentState', 'videoPaused');
                // this.$store.commit('currentMode', 'video');
                //this.$emit('event-showboxes');
                console.log('change showBoxes to ', !this.showBoxes)
                this.$store.commit('showBoxes', !this.showBoxes);
            }
        },
        onForwardClick() {
            if ((this.currentState === 'videoPaused')
                || (this.currentState === 'objectEditing')
                || (this.currentState === 'objectPaused')) {
                this.currentFrame = this.$store.state.currentFrame;
                if ((this.currentFrame >= this.framesRange.first) && (this.currentFrame < this.framesRange.last)) {
                    this.currentFrame = this.currentFrame + 1;
                    this.$store.commit('currentFrame', this.currentFrame);
                }
            }
        },
        onBackwardClick() {
            if ((this.currentState === 'videoPaused')
                || (this.currentState === 'objectEditing')
                || (this.currentState === 'objectPaused')) {
                this.currentFrame = this.$store.state.currentFrame;
                if ((this.currentFrame > this.framesRange.first) && (this.currentFrame <= this.framesRange.last)) {
                    this.currentFrame = this.currentFrame - 1;
                    this.$store.commit('currentFrame', this.currentFrame);
                }
            }
        },
        onBeginningClick() {
            if ((this.currentState === 'videoPaused') || (this.currentVideoState === 'loaded')) {
                this.currentFrame = this.$store.state.currentFrame;
                if (this.currentFrame > this.framesRange.first) {
                    this.currentFrame = this.framesRange.first;
                    this.$store.commit('currentFrame', this.currentFrame);
                }
            }
        },
        onPlayClick() {
            if ((this.currentState === 'videoPaused') || (this.currentVideoState === 'loaded')) {
                this.$store.commit('currentObject', null);
                // this.$store.commit('currentMode', 'video');
                this.$store.commit('currentState', 'videoPlaying');
            }
        },
        onPlay2() {
            if ((this.currentState === 'videoPaused') && (this.currentVideoState === 'ready')) {
                // this.$store.commit('currentMode', 'video');
                this.$store.commit('currentState', 'videoPlaying2');
            }
        },
        onPlay5() {
            if ((this.currentState === 'videoPaused') && (this.currentVideoState === 'ready')) {
                // this.$store.commit('currentMode', 'video');
                this.$store.commit('currentState', 'videoPlaying5');
            }
        },
        onPlay8() {
            if ((this.currentState === 'videoPaused') && (this.currentVideoState === 'ready')) {
                // this.$store.commit('currentMode', 'video');
                this.$store.commit('currentState', 'videoPlaying8');
            }
        },
        onPauseClick() {
            if ((this.currentState === 'videoPlaying')
                || (this.currentState === 'videoPlaying2')
                || (this.currentState === 'videoPlaying5')
                || (this.currentState === 'videoPlaying8')) {
                this.$store.commit('currentState', 'videoPaused');
                this.$store.commit('currentStopFrame', 0);
                // this.$store.commit('currentMode', 'video');
            }
        },
        doKeyCommand(e) {
            let keyCode = e.keyCode;
            if (keyCode === 32) {
                if (this.currentState === 'videoPaused') {
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
        this.$store.watch(
            (state, getters) => getters.currentVideoState,
            (currentVideoState) => {
                this.currentVideoState = currentVideoState;
            }
        )
        this.$store.watch(
            (state, getters) => getters.currentFrame,
            (currentFrame) => {
                this.currentFrame = currentFrame;
                this.updateSlider();
            }
        )
        this.currentState = this.$store.state.currentState;
        // this.currentVideoState = this.$store.state.currentVideoState;
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
        // this.$store.watch(
        //     (state, getters) => getters.currentMode,
        //     (currentMode) => {
        //         this.currentMode = currentMode;
        //         this.isEditing = (currentMode === 'object');
        //         this.isPlayback = (currentMode === 'video');
        //     }
        // )
        this.$store.watch(
            (state, getters) => getters.currentObject,
            (currentObject) => {
                this.hasObjectSelected = (currentObject != null);
            }
        )

        this.slider.min = annotationVideoModel.framesRange.first;
        this.slider.max = annotationVideoModel.framesRange.last;
        this.slider.value = annotationVideoModel.framesRange.first;
        let that = this;
        $('#sliderVideo').slider({
            min: 0,
            max: 100,
            // min: that.slider.min,
            // max: that.slider.max,
            value: that.slider.value,
            disabled: false,
            width: 'auto',
            onComplete: function (value) {
                let v = Math.round(that.slider.min + (that.slider.max - that.slider.min) * (value / 100));
                if ((v >= that.slider.min) && (v <= that.slider.max)) {
                    that.$store.commit('currentFrame', v);
                }
            },
            onSlideStart: function () {
                that.$store.commit('currentState', 'videoDragging');
            },
            onSlideEnd: function () {
                that.$store.commit('currentState', 'videoPaused');
            },
            onChange(newValue) {
                that.$store.commit('currentSliderPosition', newValue);
            }
        });
    }
}
