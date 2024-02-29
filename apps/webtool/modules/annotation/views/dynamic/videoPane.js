let videoPane = {
    template: '#video-pane',
    components: {
        'work-pane': workPane,
    },
    props: [],
    data() {
        return {
            ctx: null,
            showControls: false,
            currentFrame: 1,
            currentVideoState: '',
            currentState: '',
            isReady: false,
            isPlaying: false,
            currentScale: 1,
            originalDimensions: {
                width: 0,
                height: 0
            },
            mouse: {
                x: 0,
                y: 0,
                startX: 0,
                startY: 0
            },
            framesManager: null,
            config: {
                fps: annotationVideoModel.fps,//25,//30,
                duration: 0.0,
                timeInterval: 40, // 25 fps
                idVideoDOMElement: 'jp_video_0'
            }
        }
    },
    created() {
//        annotationVideoModel.objectsTracker.init();
        dynamicObjects.init();
    },
    computed: {},
    mounted() {
        this.ctx = this.$refs.canvas.getContext('2d');
        this.showControls = false;
        this.currentVideoState = this.$store.state.currentVideoState;
        this.currentFrame = this.$store.state.currentFrame;
        // this.$store.commit('currentMode', 'video');
        this.$store.commit('currentState', 'videoPaused');
        this.currentState = this.$store.state.currentState;
        //
        // watch change currentFrame
        //
        this.$store.watch(
            (state, getters) => getters.currentFrame,
            (currentFrame) => {
                //this.currentFrame = currentFrame;
                console.log('video watch currentFrame changed to ' + currentFrame)
                if (currentFrame === 0) {
                    console.log('==============================')
                    console.log('=== frame 0 ===========================')
                    console.log('==============================')
                }
                this.currentFrame = currentFrame;
                let currentState = this.$store.state.currentState;
                if (this.isPlaying) {
//                    console.log('  --- ' + currentState);
                    if ((currentState === 'videoPlaying2') || (currentState === 'videoPlaying5') || (currentState === 'videoPlaying8')) {
                        this.drawFrame(this.currentFrame);
                    }
                } else {
                    let currentVideoState = this.$store.state.currentVideoState;
//                    console.log('  --- currentVideoState ', currentVideoState);
                    if (currentVideoState !== 'dragging') {
                        this.pause();
                    }
                    this.drawFrame(this.currentFrame);
                }
            }
        )
        //
        // watch change currentVideoState
        //
        this.$store.watch(
            (state, getters) => getters.currentVideoState,
            (currentVideoState) => {
                console.log('video watch currentVideoState changed to ' + currentVideoState)
                this.currentVideoState = currentVideoState;
                if (currentVideoState === 'loaded') {
                    this.config.timeInterval = 1000 / this.config.fps;
                    //console.log('timeInterval = ', this.config.timeInterval);
                    console.log('duration', this.config.duration);
                    annotationVideoModel.framesRange = {
                        first: 1,
                        last: this.frameFromTime(this.config.duration)
                    }
                    //console.log(this.framesRange);
                    //this.$store.commit('framesRange', this.framesRange);
                    this.currentFrame = 1;
                    this.$store.commit('currentFrame', this.currentFrame);
                    this.initializeFrameObject();
                    this.showControls = true;
                }
            }
        )
        //
        // watch change currentState
        //
        this.$store.watch(
            (state, getters) => getters.currentState,
            (currentState) => {
                console.log('video watch currentState changed to ' + currentState)
                this.currentState = currentState;
                if (currentState === 'videoPlaying') {
                    this.clearFrame(this.$store.state.currentFrame);
                    $("#jquery_jplayer_1").jPlayer({playbackRate: 1.0});
                    this.play();
                }
                if (currentState === 'videoPlaying2') {
                    this.pause();
                    $("#jquery_jplayer_1").jPlayer({playbackRate: 0.17});
                    this.play();
                }
                if (currentState === 'videoPlaying5') {
                    this.pause();
                    $("#jquery_jplayer_1").jPlayer({playbackRate: 0.5});
                    this.play();
                }
                if (currentState === 'videoPlaying8') {
                    this.pause();
                    $("#jquery_jplayer_1").jPlayer({playbackRate: 0.8});
                    this.play();
                }
                if (currentState === 'videoPaused') {
                    this.pause();
                }
                if (currentState === 'objectPaused') {
                    this.pause();
                }
                if (currentState === 'objectCreating') {
                    this.onNewObject();
                }
            }
        )
        //
        // watch change currentObjectState
        //
        this.$store.watch(
            (state, getters) => getters.currentObjectState,
            (currentObjectState) => {
                if (this.$store.state.currentObject) {
                    console.log('video watch currentObjectState = ' + currentObjectState + '  ' + this.$store.state.currentFrame)
                    if ((currentObjectState === 'updated') || (currentObjectState === 'unselected') || (currentObjectState === 'cleared')) {
                        this.drawFrame(this.$store.state.currentFrame);
                    }
                }
            }
        )
        //
        // watch change currentObject
        //
        this.$store.watch(
            (state, getters) => getters.currentObject,
            (currentObject) => {
                if (currentObject) {
                    console.log('video watch currentObject ', currentObject.idObject);
                    this.drawFrame(this.$store.state.currentFrame);
                } else {
                    this.clearFrame();
                }
            }
        )
        //
        // watch change redrawFrame
        //
        this.$store.watch(
            (state, getters) => state.redrawFrame,
            (redrawFrame) => {
                if (redrawFrame) {
                    //console.log('redraw frame');
                    this.drawFrame(this.currentFrame);
                    this.$store.commit('redrawFrame', false);
                }
            }
        )

        //
        // watch change showBoxes
        //
        this.$store.watch(
            (state, getters) => state.showBoxes,
            (showBoxes) => {
                console.log('video watch showBoxes = ', showBoxes)
                this.drawFrameBoxes(this.currentFrame);
            }
        )
        this.$nextTick(() => {
            console.log('visual pane mounted');
            this.framesManager = dynamicObjects.framesManager;
            $("#jquery_jplayer_1").jPlayer({
                ready: function () {
                    console.log('jplayer1 ready');
                    let videoPath = annotationVideoModel.videoPath;
                    //console.log('video path = ' + videoPath);
                    $(this).jPlayer("setMedia", {
                        title: "",
                        m4v: videoPath
                    });
                },
                size: {
                    width: annotationVideoModel.documentMM.videoWidth + "px",
                    height: annotationVideoModel.documentMM.videoHeight + "px",
                    cssClass: "jp-video-360p"
                },
                defaultPlaybackRate: 1.0,//0.5,
                minPlaybackRate: 0.15,//0.5,
                playbackRate: 1.0,//0.5,
                cssSelectorAncestor: "#jp_container",
                swfPath: "/js",
                supplied: "m4v",
                useStateClassSkin: false,
                autoBlur: false,
                smoothPlayBar: false,
                keyEnabled: false,
                remainingDuration: false,
                toggleDuration: false,
                loadeddata: event => {
                    //console.log('**** jplayer loaded data!');
                    //console.log('currenttime = ' + event.jPlayer.status.currentTime);
                    //console.log('duration = ' + event.jPlayer.status.duration);
                    //console.log('width = ' + event.jPlayer.status.videoWidth);
                    //console.log('height = ' + event.jPlayer.status.videoHeight);
                    this.config.duration = event.jPlayer.status.duration;
                    this.originalDimensions = {
                        width: event.jPlayer.status.videoWidth,
                        height: event.jPlayer.status.videoHeight
                    }
                    this.initializeCanvasDimensions();
                    this.framesManager.setConfig(this.config);
                    //this.$store.commit('endTime', this.config.duration);
                    annotationVideoModel.time.end = this.config.duration;
                    this.$store.commit('currentVideoState', 'loaded');
                },
                timeupdate: event => {
                    this.currentTime = event.jPlayer.status.currentTime;
                    // console.log('== player timeupdate ', this.currentTime, event.jPlayer.status.playbackRate)
                    let currentFrame = this.frameFromTime(this.currentTime);
                    // console.log('== event time update - jplayer1 - ' + this.currentTime);
                    // console.log('== event time update - currentFrame ' + currentFrame);
                    if (this.isPlaying) {
                        let currentVideoState = this.$store.state.currentVideoState;
                        if ((currentVideoState === 'videoPlaying2') || (currentVideoState === 'videoPlaying5') || (currentVideoState === 'videoPlaying8')) {
                            this.drawFrame(currentFrame);
                        }
                        this.$store.commit('currentFrame', currentFrame);
                        //this.$store.commit('playFrame', currentFrame);
                        this.currentFrame = currentFrame;
                        let stopFrame = this.$store.state.currentStopFrame;
                        if (stopFrame > 0) {
                            if (this.currentFrame > stopFrame) {
                                this.$store.commit('currentState', 'videoPaused');
                                this.$store.commit('currentStopFrame', 0);
                            }
                        }
                    }
                    //if (!this.isPlaying && this.showControls) {
                    //console.log('== curentFrame ' + currentFrame);
                    //this.drawFrame(currentFrame);
                    //}
                },
            });
        })
    },
    methods: {
        frameFromTime(timeSeconds) {
            return parseInt((timeSeconds * 1000) / this.config.timeInterval) + 1;
        },
        timeFromFrame(frameNumber) {
            return ((frameNumber - 1) * this.config.timeInterval) / 1000;
        },
        ready: function () {
            this.isReady = true;
        },
        seek: function (frameNumber) {
            if (!this.isReady) {
                return;
            }
            this.pause();
            if (frameNumber >= 0 && frameNumber < this.framesRange.last) {
                this.drawFrame(frameNumber);
                this.currentFrame = frameNumber;
            }
        },

        forward: function () {
            this.seek(this.currentFrame + 1);
        },

        backward: function () {
            this.seek(this.currentFrame - 1);
        },

        play: function () {
            if (!this.isReady) {
                return;
            }
            $("#jquery_jplayer_1").jPlayer('play', this.timeFromFrame(this.currentFrame));
            this.isPlaying = true;
        },
        pause: function () {
            if (!this.isReady) {
                return;
            }
            this.isPlaying = false;
            let timeFromFrame = this.timeFromFrame(this.currentFrame);
            $("#jquery_jplayer_1").jPlayer('pause', timeFromFrame);
            //$("#jquery_jplayer_2").jPlayer('pause', timeFromFrame);
        },
        toogle: function () {
            if (!this.isPlaying) {
                this.play();
            } else {
                this.pause();
            }
        },
        nextFrame: function () {
            if (!this.isPlaying) {
                return;
            }
            if (this.currentFrame === this.lastFrame) {
                this.done();
                return;
            }
            this.currentFrame++;
            this.$store.commit('currentFrame', this.currentFrame);
        },
        done: function () {
            this.currentFrame = this.firstFrame;
            this.isPlaying = false;
            this.$store.commit('currentState', 'videoPaused');
        },
        drawFrame: function (frameNumber) {
            let that = this;
            frameNumber = parseInt(frameNumber);
            if (frameNumber < 1) {
                return;
            }
            try {
                let currentState = that.$store.state.currentState;
                jQuery('.bbox').css("display", "none");
                let currentObject = that.$store.state.currentObject;
                console.log('drawFrame ' + frameNumber + ' ' + currentState);
                let isEditing = ((currentState === 'objectEditing') || (currentState === 'objectTracking') || (currentState === 'objectPaused'));
                if (isEditing) {
                    let annotatedObjectsTracker = dynamicObjects.tracker;
                    annotatedObjectsTracker.getFrameWithObject(frameNumber, currentObject).then((frameWithObject) => {
                        console.log(frameWithObject);
                        for (let i = 0; i < frameWithObject.objects.length; i++) {
                            let object = frameWithObject.objects[i];
                            let annotatedObject = object.annotatedObject;
                            if (currentObject && (annotatedObject.idObject === currentObject.idObject)) {
                                console.log('in drawing frame for currentObject');
                                console.log(annotatedObject.idObject);
                                console.log(annotatedObject);
                                let annotatedFrame = object.annotatedFrame;
                                console.log(annotatedFrame);
                                // this.$store.dispatch("setObjectState", {
                                //     object: annotatedObject,
                                //     state: 'dirty',
                                //     flag: frameNumber
                                // });
                                annotatedObject.dom.style.display = 'none';
                                if (!annotatedObject.hidden) {
                                    if (annotatedFrame.isVisible()) {
                                        let scaledBox = dynamicObjects.toScaledCoord(annotatedFrame.bbox.x, annotatedFrame.bbox.y, annotatedFrame.bbox.width, annotatedFrame.bbox.height, this.currentScale);
                                        annotatedObject.dom.style.display = 'block';
                                        annotatedObject.dom.style.width = scaledBox.width + 'px';
                                        annotatedObject.dom.style.height = scaledBox.height + 'px';
                                        annotatedObject.dom.style.left = scaledBox.x + 'px';
                                        annotatedObject.dom.style.top = scaledBox.y + 'px';
                                        annotatedObject.dom.style.borderStyle = 'solid';
                                        annotatedObject.dom.style.borderColor = annotatedObject.color;
                                        annotatedObject.dom.style.borderWidth = "medium";
                                        annotatedObject.visible = true;
                                        annotatedObject.dom.style.backgroundColor = 'transparent';
                                        annotatedObject.dom.style.opacity = 1;
                                        if (annotatedFrame.blocked) {
                                            annotatedObject.dom.style.opacity = 0.5;
                                            annotatedObject.dom.style.backgroundColor = 'white';
                                            annotatedObject.dom.style.borderStyle = 'dashed';
                                        }
                                    } else {
                                        annotatedObject.dom.style.display = 'none';
                                        annotatedObject.visible = false;
                                    }
                                }
                            }
                        }
                        that.$store.commit('redrawFrame', false);
                    });
                } else {
                    console.log('drawFrame not editing', currentObject);
                    if (currentObject) {
                        let annotatedObject = currentObject;
                        annotatedObject.dom.style.display = 'none';
                        let annotatedFrame = annotatedObject.get(frameNumber);
                        if (annotatedFrame) {
                            if (!currentObject.hidden) {
                                if (annotatedFrame.isVisible()) {
                                    // console.log(annotatedObject);
                                    let scaledBox = dynamicObjects.toScaledCoord(annotatedFrame.bbox.x, annotatedFrame.bbox.y, annotatedFrame.bbox.width, annotatedFrame.bbox.height, this.currentScale);
                                    annotatedObject.dom.style.display = 'block';
                                    annotatedObject.dom.style.width = scaledBox.width + 'px';
                                    annotatedObject.dom.style.height = scaledBox.height + 'px';
                                    annotatedObject.dom.style.left = scaledBox.x + 'px';
                                    annotatedObject.dom.style.top = scaledBox.y + 'px';
                                    annotatedObject.dom.style.borderStyle = 'dotted';
                                    annotatedObject.dom.style.borderColor = annotatedObject.color;
                                    annotatedObject.dom.style.borderWidth = "medium";
                                    annotatedObject.visible = true;
                                    annotatedObject.dom.style.backgroundColor = 'transparent';
                                    annotatedObject.dom.style.opacity = 1;
                                    if (annotatedFrame.blocked) {
                                        annotatedObject.dom.style.opacity = 0.5;
                                        annotatedObject.dom.style.backgroundColor = 'white';
                                        annotatedObject.dom.style.borderStyle = 'dashed';
                                    }
                                } else {
                                    annotatedObject.dom.style.display = 'none';
                                    annotatedObject.visible = false;
                                }
                            }
                        }
                    }
                }
            } catch (e) {
                $.messager.alert('Error', e.message, 'error');
            }
        },
        drawFrameBoxes: function (frameNumber) {
            let that = this;
            frameNumber = parseInt(frameNumber);
            if (frameNumber < 1) {
                return;
            }
            try {
                let showBoxes = this.$store.state.showBoxes;
                if (!showBoxes) {
                    jQuery('.bbox').css("display", "none");
                    if (that.$store.state.currentObject) {
                        that.$store.dispatch('selectObject', that.$store.state.currentObject.idObject);
                    }
                    //this.showBoxesState = false;
                } else {
                    let currentState = that.$store.state.currentState;
                    console.log('drawFrameBoxes ', frameNumber, currentState);
                    if (currentState === 'videoPaused') {
                        jQuery('.bbox').css("display", "none");
                        let annotatedObjectsTracker = dynamicObjects.tracker;
                        let objectsInFrame = annotatedObjectsTracker.getObjectsByFrame(frameNumber)
console.log(objectsInFrame);
                            for (let i = 0; i < objectsInFrame.length; i++) {
                                let annotatedObject = objectsInFrame[i].annotatedObject;
                                let annotatedFrame = objectsInFrame[i].annotatedFrame;
                                // console.log('in drawing frame');
                                // console.log(annotatedObject.idObject);
                                // console.log(annotatedObject);
                                // console.log(annotatedFrame);
                                annotatedObject.dom.style.display = 'none';
                                if (!annotatedObject.hidden) {
                                    if (annotatedFrame.isVisible()) {
                                        let scaledBox = dynamicObjects.toScaledCoord(annotatedFrame.bbox.x, annotatedFrame.bbox.y, annotatedFrame.bbox.width, annotatedFrame.bbox.height, this.currentScale);
                                        annotatedObject.dom.style.display = 'block';
                                        annotatedObject.dom.style.width = scaledBox.width + 'px';
                                        annotatedObject.dom.style.height = scaledBox.height + 'px';
                                        annotatedObject.dom.style.left = scaledBox.x + 'px';
                                        annotatedObject.dom.style.top = scaledBox.y + 'px';
                                        annotatedObject.dom.style.borderStyle = 'solid';
                                        annotatedObject.dom.style.borderColor = annotatedObject.color;
                                        annotatedObject.dom.style.borderWidth = "thick";
                                        annotatedObject.visible = true;
                                        annotatedObject.dom.style.backgroundColor = 'transparent';
                                        annotatedObject.dom.style.opacity = 1;
                                        if (annotatedFrame.blocked) {
                                            annotatedObject.dom.style.opacity = 0.5;
                                            annotatedObject.dom.style.backgroundColor = 'white';
                                            annotatedObject.dom.style.borderStyle = 'dashed';
                                        }
                                    } else {
                                        annotatedObject.dom.style.display = 'none';
                                        annotatedObject.visible = false;
                                    }
                                }
                            }






                        // annotatedObjectsTracker.getFrameWithObjects(frameNumber).then((frameWithObjects) => {
                        //     for (let i = 0; i < frameWithObjects.objects.length; i++) {
                        //         let object = frameWithObjects.objects[i];
                        //         let annotatedObject = object.annotatedObject;
                        //         console.log('in drawing frame');
                        //         console.log(annotatedObject.idObject);
                        //         console.log(annotatedObject);
                        //         let annotatedFrame = object.annotatedFrame;
                        //         console.log(annotatedFrame);
                        //         annotatedObject.dom.style.display = 'none';
                        //         if (!annotatedObject.hidden) {
                        //             if (annotatedFrame.isVisible()) {
                        //                 let scaledBox = dynamicObjects.toScaledCoord(annotatedFrame.bbox.x, annotatedFrame.bbox.y, annotatedFrame.bbox.width, annotatedFrame.bbox.height, this.currentScale);
                        //                 annotatedObject.dom.style.display = 'block';
                        //                 annotatedObject.dom.style.width = scaledBox.width + 'px';
                        //                 annotatedObject.dom.style.height = scaledBox.height + 'px';
                        //                 annotatedObject.dom.style.left = scaledBox.x + 'px';
                        //                 annotatedObject.dom.style.top = scaledBox.y + 'px';
                        //                 annotatedObject.dom.style.borderStyle = 'solid';
                        //                 annotatedObject.dom.style.borderColor = annotatedObject.color;
                        //                 annotatedObject.dom.style.borderWidth = "thick";
                        //                 annotatedObject.visible = true;
                        //                 annotatedObject.dom.style.backgroundColor = 'transparent';
                        //                 annotatedObject.dom.style.opacity = 1;
                        //                 if (annotatedFrame.blocked) {
                        //                     annotatedObject.dom.style.opacity = 0.5;
                        //                     annotatedObject.dom.style.backgroundColor = 'white';
                        //                     annotatedObject.dom.style.borderStyle = 'dashed';
                        //                 }
                        //             } else {
                        //                 annotatedObject.dom.style.display = 'none';
                        //                 annotatedObject.visible = false;
                        //             }
                        //         }
                        //     }

                            // that.$store.commit('redrawFrame', false);
                        // });
                    }
                    // this.showBoxesState = true;
                }
            } catch (e) {
                $.messager.alert('Error', e.message, 'error');
            }
        },
        clearFrame: function (frameNumber) {
            jQuery('.bbox').css("display", "none");
        },
        initializeVideo: function () {
            this.currentFrame = 0;
            this.isPlaying = false;
            this.isReady = false;
            this.$store.commit('currentVideoState', 'initing');
        },
        initializeCanvasDimensions: function () {
            let doodle = this.$refs.doodle;
            let canvas = this.$refs.canvas;
            canvas.width = this.originalDimensions.width;
            canvas.height = this.originalDimensions.height;
            doodle.style.width = canvas.width + 'px';
            doodle.style.height = canvas.height + 'px';
            let jpContainer = this.$refs.jp_container;
            jp_container.style.width = canvas.width + 'px';
        },
        initializeFrameObject: async function () {
            this.initializeVideo();
            vatic.getFramesForObjects(this.framesManager, this.config);
            this.ready();
            // this.clearAllAnnotatedObjects();
            //await this.loadObjects();
            annotationVideoModel.boxesContainer = this.$refs.doodle;//this.$refs.boxesContainer;
            annotationVideoModel.currentScale = this.currentScale;
            // await dynamicObjects.loadObjectsFromDb();
            this.drawFrame(2);
            this.drawFrame(1);
            $("#jquery_jplayer_1").jPlayer('pause', 0);
            $("#jquery_jplayer_1").jPlayer("playHead", 0);
            this.$store.commit('currentObject', null);
            this.$store.commit('currentObjectState', '');
            this.$store.commit('currentState', 'videoPaused');
            this.$store.commit('currentVideoState', 'ready');
            dynamicStore.commit('updateGridPane', true)
        },
        // async loadObjects() {
        //     await dynamicObjects.loadObjectsFromDb(this.$refs.doodle, this.currentScale);
        //this.$store.commit('objectsTrackerState', 'dirty');
        // },
        // clearAllAnnotatedObjects() {
        //     dynamicObjects.tracker.clearAll();
        //     this.$store.commit('objectsTrackerState', 'dirty');
        // },
        // clearAnnotatedObject(i) {
        //     dynamicObjects.clearObject(i);
        //     this.$store.commit('objectsTrackerState', 'dirty');
        // },
        // addAnnotatedObjectControls(annotatedObject) {
        //     dynamicObjects.addControlsToObject(annotatedObject);
        //     this.$store.dispatch('objectsTrackerAdd', annotatedObject);
        // },
        onNewObject() {
            console.log('onNewObject');
            this.$store.commit('currentObject', null);
            this.$refs.doodle.style.cursor = 'crosshair';
        },

        onMouseMove(event) {
            if (event.pageX) {
                this.mouse.x = event.pageX;
                this.mouse.y = event.pageY;
            } else if (event.clientX) {
                this.mouse.x = event.clientX;
                this.mouse.y = event.clientY;
            }
            const rect = this.$refs.doodle.getBoundingClientRect();
            this.mouse.x -= rect.x;
            this.mouse.y -= rect.y;
            if (this.tempAnnotatedObject != null) {
                this.tempAnnotatedObject.width = Math.abs(this.mouse.x - this.mouse.startX);
                this.tempAnnotatedObject.height = Math.abs(this.mouse.y - this.mouse.startY);
                this.tempAnnotatedObject.x = (this.mouse.x - this.mouse.startX < 0) ? this.mouse.x : this.mouse.startX;
                this.tempAnnotatedObject.y = (this.mouse.y - this.mouse.startY < 0) ? this.mouse.y : this.mouse.startY;
                this.tempAnnotatedObject.dom.style.width = this.tempAnnotatedObject.width + 'px';
                this.tempAnnotatedObject.dom.style.height = this.tempAnnotatedObject.height + 'px';
                this.tempAnnotatedObject.dom.style.left = this.tempAnnotatedObject.x + 'px';
                this.tempAnnotatedObject.dom.style.top = this.tempAnnotatedObject.y + 'px';
                this.tempAnnotatedObject.dom.style.border = "1px solid #D3D3D3";
            }
        },
        async onMouseClick(event) {
            //console.log(event);
            let doodle = this.$refs.doodle;
            if (doodle.style.cursor !== 'crosshair') {
                return;
            }
            if (this.tempAnnotatedObject != null) {
                console.log('mouse click - create new object')
                let data = await dynamicObjects.createNewObject(this.tempAnnotatedObject, this.currentScale, this.currentFrame);
                console.log('after createNewObject')
                doodle.style.cursor = 'default';
                this.tempAnnotatedObject = null;
                // this.$store.commit('currentIdObjectMM', data.idObjectMM)
                // console.log(this.$store.state.currentIdObjectMM)
                // this.clearAllAnnotatedObjects();
                // this.$store.dispatch('findObjectByIdObjectMM',data.idObjectMM);
                // this.$store.commit('currentMode', 'object');
                // this.$store.commit('currentState', 'editing');
                // await this.loadObjects();
            } else {
                this.mouse.startX = this.mouse.x;
                this.mouse.startY = this.mouse.y;
                let dom = dynamicObjects.newBboxElement(doodle);
                dom.style.left = this.mouse.x + 'px';
                dom.style.top = this.mouse.y + 'px';
                dom.style.borderColor = '#D3D3D3';
                this.tempAnnotatedObject = {
                    dom: dom
                };
            }
        },
        // onShowBoxes() {
        //     console.log('onshowboxes3', this.currentVideoState);
        //     this.drawFrameBoxes(this.currentFrame);
        // },
    },
}

