@php
    $rootFolder = $manager->getConf('charon.rootFolder')
@endphp
<script>
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
                showBoxesState: false,
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
                    // Should be higher than real FPS to not skip real frames
                    // Hardcoded due to JS limitations
                    fps: annotationVideoModel.fps,//25,//30,
                    // Low rate decreases the chance of losing frames with poor browser performances
                    //playbackRate: 1.0,//0.4,
                    // Format of the extracted frames
                    //imageMimeType: 'image/jpeg',
                    //imageExtension: '.jpg',
                    // Name of the extracted frames zip archive
                    //framesZipFilename: 'extracted-frames.zip',
                    duration: 0.0,
                    timeInterval: 40, // 25 fps
                    //url: '',
                    idVideoDOMElement: 'jp_video_0'
                }
            }
        },
        created() {
            //console.log('video segment = ' + this.segment);
            //this.$store.dispatch('objectsTrackerInit');
            annotationVideoModel.objectsTracker.init();
        },
        mounted() {
            this.ctx = this.$refs.canvas.getContext('2d');
            this.showControls = false;
            this.currentVideoState = this.$store.state.currentVideoState;
            this.currentFrame = this.$store.state.currentFrame;
            this.$store.commit('currentMode', 'video');
            this.$store.commit('currentState', 'paused');
            this.currentState = this.$store.state.currentState;
            //
            // watch change currentFrame
            //
            this.$store.watch(
                (state, getters) => getters.currentFrame,
                (currentFrame) => {
                    //this.currentFrame = currentFrame;
                    console.log('video watch currentFrame changed to ' + currentFrame)
                    this.currentFrame = currentFrame;
                    let currentState = this.$store.state.currentState;
                    if (this.isPlaying) {
                        console.log('  --- ' + currentState);
                        if ((currentState === 'playing2') || (currentState === 'playing5') || (currentState === 'playing8')) {
                            this.drawFrame(this.currentFrame);
                        }
                    } else {
                        let currentVideoState = this.$store.state.currentVideoState;
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
                    if (currentState === 'playing') {
                        this.clearFrame(this.$store.state.currentFrame);
                        $("#jquery_jplayer_1").jPlayer({playbackRate: 1.0});
                        this.play();
                    }
                    if (currentState === 'playing2') {
                        this.pause();
                        $("#jquery_jplayer_1").jPlayer({playbackRate: 0.17});
                        this.play();
                    }
                    if (currentState === 'playing5') {
                        this.pause();
                        $("#jquery_jplayer_1").jPlayer({playbackRate: 0.5});
                        this.play();
                    }
                    if (currentState === 'playing8') {
                        this.pause();
                        $("#jquery_jplayer_1").jPlayer({playbackRate: 0.8});
                        this.play();
                    }
                    if (currentState === 'paused') {
                        this.pause();
                    }
                    if (currentState === 'creating') {
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
                    }
                }
            )
            //
            // watch videoLoaded
            //
            // this.$store.watch(
            //     (state, getters) => getters.videoLoaded,
            //     (videoLoaded) => {
            //         if (videoLoaded) {
            //             this.config.timeInterval = 1000 / this.config.fps;
            //             //console.log('timeInterval = ', this.config.timeInterval);
            //             console.log('duration', this.config.duration);
            //             annotationVideoModel.framesRange = {
            //                 first: 1,
            //                 last: this.frameFromTime(this.config.duration)
            //             }
            //             //console.log(this.framesRange);
            //             //this.$store.commit('framesRange', this.framesRange);
            //             this.currentFrame = 1;
            //             this.$store.commit('currentFrame', this.currentFrame);
            //             this.initializeFrameObject();
            //         }
            //     }
            // )
            //
            // watch allLoaded
            //
            // this.$store.watch(
            //     (state, getters) => getters.allLoaded,
            //     (allLoaded) => {
            //         if (allLoaded) {
            //             this.showControls = true;
            //         }
            //     }
            // )

            this.$nextTick(() => {
                console.log('visual pane mounted');
                let that = this;
                console.log(window.annotationVideoModel.objectsTracker);
                this.framesManager = annotationVideoModel.objectsTracker.framesManager;
                $("#jquery_jplayer_1").jPlayer({
                    ready: function () {
                        console.log('jplayer1 ready');
                        let videoPath = annotationVideoModel.documentMM.videoPath.replace('{{$rootFolder}}', '');
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
                            if ((currentVideoState === 'playing2') || (currentVideoState === 'playing5') || (currentVideoState === 'playing8')) {
                                this.drawFrame(currentFrame);
                            }
                            this.$store.commit('currentFrame', currentFrame);
                            //this.$store.commit('playFrame', currentFrame);
                            this.currentFrame = currentFrame;
                            let stopFrame = this.$store.state.currentStopFrame;
                            if (stopFrame > 0) {
                                if (this.currentFrame > stopFrame) {
                                    this.$store.commit('currentState', 'paused');
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
            clearAllAnnotatedObjects() {
                annotationVideoModel.objectsTracker.tracker.clearAll();
                this.$store.commit('objectsTrackerState', 'clean');
            },
            clearAnnotatedObject(i) {
                let annotatedObject = annotationVideoModel.objectsTracker.tracker.get(i);
                annotationVideoModel.objectsTracker.tracker.clear(annotatedObject);
                this.$store.commit('objectsTrackerState', 'dirty');
            },
            addAnnotatedObjectControls(annotatedObject) {
                //console.log(annotatedObject);
                annotatedObject.name = '';
                annotatedObject.visible = true;
                annotatedObject.hidden = false;
                annotatedObject.locked = false;
                annotatedObject.idFrame = -1;
                annotatedObject.frame = '';
                annotatedObject.idFE = -1;
                annotatedObject.fe = '';
                annotatedObject.color = 'white';
                annotatedObject.startFrame = this.currentFrame;
                annotatedObject.endFrame = annotationVideoModel.framesRange.last;
                this.$store.dispatch('objectsTrackerAdd', annotatedObject);
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
                this.$store.commit('currentState', 'paused');
            },
            toAbsoluteCoord(x, y, width, height) {
                return {
                    x: Math.round(x / this.currentScale),
                    y: Math.round(y / this.currentScale),
                    width: Math.round(width / this.currentScale),
                    height: Math.round(height / this.currentScale)
                }
            },
            toScaledCoord(x, y, width, height) {
                return {
                    x: Math.round(x * this.currentScale),
                    y: Math.round(y * this.currentScale),
                    width: Math.round(width * this.currentScale),
                    height: Math.round(height * this.currentScale)
                }
            },
            drawFrame: function (frameNumber) {
                let that = this;
                //return new Promise((resolve, _) => {
                if (frameNumber < 1) {
                    return;
                }
                try {
                    let currentMode = that.$store.state.currentMode;
                    // let isPlayBack = (currentMode === 'video');
                    // if (!isPlayBack) {
                    jQuery('.bbox').css("display", "none");
                    // }
                    //this.$store.commit('currentObjectState', 'drawing');
                    let currentObject = that.$store.state.currentObject;
                    console.log('drawFrame ' + frameNumber + ' ' + currentMode);
                    let isEditing = (currentMode === 'object');
                    if (isEditing) {
                        let annotatedObjectsTracker = annotationVideoModel.objectsTracker.tracker;
                        //annotatedObjectsTracker.getFrameWithObjects(frameNumber).then((frameWithObjects) => {
                        annotatedObjectsTracker.getFrameWithObject(frameNumber, currentObject).then((frameWithObject) => {
                            console.log(frameWithObject);
                            for (let i = 0; i < frameWithObject.objects.length; i++) {
                                let object = frameWithObject.objects[i];
                                let annotatedObject = object.annotatedObject;
                                if (currentObject && (annotatedObject.idObject === currentObject.idObject)) {
                                    console.log('in drawing frame');
                                    console.log(annotatedObject.idObject);
                                    console.log(annotatedObject);
                                    let annotatedFrame = object.annotatedFrame;
                                    console.log(annotatedFrame);
                                    // if (!isPlayBack) {
                                    //     // force update objectPane
                                    this.$store.dispatch("setObjectState", {
                                        object: annotatedObject,
                                        state: 'dirty',
                                        flag: frameNumber
                                    });
                                    // }
                                    annotatedObject.dom.style.display = 'none';
                                    if (!annotatedObject.hidden) {
                                        if (annotatedFrame.isVisible()) {
                                            //if ((annotatedFrame.isVisible()) && (frameNumber >= annotatedObject.startFrame) && (frameNumber <= annotatedObject.endFrame)) {
                                            //console.log(annotatedFrame.blocked);
                                            let scaledBox = this.toScaledCoord(annotatedFrame.bbox.x, annotatedFrame.bbox.y, annotatedFrame.bbox.width, annotatedFrame.bbox.height);
                                            annotatedObject.dom.style.display = 'block';
                                            annotatedObject.dom.style.width = scaledBox.width + 'px';
                                            annotatedObject.dom.style.height = scaledBox.height + 'px';
                                            annotatedObject.dom.style.left = scaledBox.x + 'px';
                                            annotatedObject.dom.style.top = scaledBox.y + 'px';
                                            annotatedObject.dom.style.borderStyle = 'solid';
                                            annotatedObject.dom.style.borderColor = annotatedObject.color;
											annotatedObject.dom.style.borderWidth = "thick";
                                            annotatedObject.visible = true;
                                            //console.log('annotated', annotatedObject.idObject);
                                            //if (currentObject && (annotatedObject.idObject === currentObject.idObject)) {
                                            //if (currentState === 'selected') {
                                            //console.log(currentObject);
                                            //console.log('color', currentObject.color);
                                            // annotatedObject.dom.style.backgroundColor = currentObject.color;
                                            // annotatedObject.dom.style.opacity = 0.5;
                                            //}
                                            //} else {
                                            annotatedObject.dom.style.backgroundColor = 'transparent';
                                            annotatedObject.dom.style.opacity = 1;
                                            //}
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
                        console.log('drawFrame not editing');
                        if (currentObject) {
                            let annotatedObject = currentObject;
                            annotatedObject.dom.style.display = 'none';
                            let annotatedFrame = annotatedObject.get(frameNumber);
                            if (annotatedFrame) {
                                if (!currentObject.hidden) {
                                    if (annotatedFrame.isVisible()) {
                                        let scaledBox = this.toScaledCoord(annotatedFrame.bbox.x, annotatedFrame.bbox.y, annotatedFrame.bbox.width, annotatedFrame.bbox.height);
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
                        }
                    }
                } catch (e) {
                    $.messager.alert('Error', e.message, 'error');
                }
            },
            drawFrameBoxes: function (frameNumber) {
                let that = this;
                //return new Promise((resolve, _) => {
                if (frameNumber < 1) {
                    return;
                }
                try {
                    if (this.showBoxesState) {
                        jQuery('.bbox').css("display", "none");
                        this.showBoxesState = false;
                    } else {
                        let currentState = that.$store.state.currentState;
                        console.log('drawFrameBoxes ', frameNumber, currentState);
                        if (currentState === 'paused') {
                            jQuery('.bbox').css("display", "none");
                            //this.$store.commit('currentObjectState', 'drawing');
                            // let currentObject = that.$store.state.currentObject;
                            let annotatedObjectsTracker = annotationVideoModel.objectsTracker.tracker;
                            annotatedObjectsTracker.getFrameWithObjects(frameNumber).then((frameWithObjects) => {
                                for (let i = 0; i < frameWithObjects.objects.length; i++) {
                                    let object = frameWithObjects.objects[i];
                                    let annotatedObject = object.annotatedObject;
                                    console.log('in drawing frame');
                                    console.log(annotatedObject.idObject);
                                    console.log(annotatedObject);
                                    let annotatedFrame = object.annotatedFrame;
                                    console.log(annotatedFrame);
                                    annotatedObject.dom.style.display = 'none';
                                    if (!annotatedObject.hidden) {
                                        if (annotatedFrame.isVisible()) {
                                            let scaledBox = this.toScaledCoord(annotatedFrame.bbox.x, annotatedFrame.bbox.y, annotatedFrame.bbox.width, annotatedFrame.bbox.height);
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

                                that.$store.commit('redrawFrame', false);
                            });
                        }
                        this.showBoxesState = true;
                    }
                } catch (e) {
                    $.messager.alert('Error', e.message, 'error');
                }
            },
            clearFrame: function (frameNumber) {
                jQuery('.bbox').css("display", "none");
                // if (frameNumber < 1) {
                //     return;
                // }
                // try {
                //     let annotatedObjectsTracker = this.$store.getters.objectsTracker;
                //     annotatedObjectsTracker.getFrameWithObjects(frameNumber).then((frameWithObjects) => {
                //         for (let i = 0; i < frameWithObjects.objects.length; i++) {
                //             let object = frameWithObjects.objects[i];
                //             let annotatedObject = object.annotatedObject;
                //             annotatedObject.dom.style.display = 'none';
                //         }
                //     });
                // } catch (e) {
                //     $.messager.alert('Error', e.message, 'error');
                // }
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
                //this.$store.dispatch('updateFramesRange', this.framesRange);
                this.clearAllAnnotatedObjects();
                await this.loadObjects();
                //this.seek(2);
                this.drawFrame(2);
                this.drawFrame(1);
                $("#jquery_jplayer_1").jPlayer('pause', 0);
                $("#jquery_jplayer_1").jPlayer("playHead", 0);
                this.$store.commit('currentObject', null);
                this.$store.commit('currentObjectState', '');
                this.$store.commit('currentState', 'paused');
                this.$store.commit('currentVideoState', 'ready');
                //this.$store.commit('objectLoaded');
            },
            async loadObjects() {
                let objectsLoaded = await annotationVideoModel.api.loadObjects();
                // console.log(objectsLoaded);
                let i = 1;
                for (var object of objectsLoaded) {
                    if ((object.startFrame >= annotationVideoModel.framesRange.first) && (object.startFrame <= annotationVideoModel.framesRange.last)) {
                        let annotatedObject = new AnnotatedObject();
                        annotatedObject.loadFromDb(i++, object)
                        annotatedObject.dom = this.newBboxElement();
                        annotationVideoModel.objectsTracker.add(annotatedObject);
                        this.interactify(
                            annotatedObject,
                            (x, y, width, height) => {
                                let absolute = this.toAbsoluteCoord(x, y, width, height);
                                let bbox = new BoundingBox(absolute.x, absolute.y, absolute.width, absolute.height);
                                annotatedObject.add(new AnnotatedFrame(this.currentFrame, bbox, true));
                                console.log('box changed!', annotatedObject.idObject);
                                this.$store.dispatch("setObjectState", {
                                    object: annotatedObject,
                                    state: 'dirty',
                                    flag: this.currentFrame
                                });
                            }
                        );
                        let lastFrame = -1;
                        let bbox = null;
                        let polygons = object.frames;
                        for (let j = 0; j < polygons.length; j++) {
                            let polygon = object.frames[j];
                            let frameNumber = parseInt(polygon.frameNumber);
                            let isGroundThrough = true;// parseInt(topLeft.find('l').text()) == 1;
                            let x = parseInt(polygon.x);
                            let y = parseInt(polygon.y);
                            let w = parseInt(polygon.width);
                            let h = parseInt(polygon.height);
                            bbox = new BoundingBox(x, y, w, h);
                            let annotatedFrame = new AnnotatedFrame(frameNumber, bbox, isGroundThrough);
                            annotatedFrame.blocked = (parseInt(polygon.blocked) === 1);
                            annotatedObject.add(annotatedFrame);
                            lastFrame = frameNumber;
                        }
                    }
                }
                this.$store.commit('objectsTrackerState', 'dirty');
            },
            onNewObject() {
                console.log('onNewObject');
                this.$store.commit('currentObject', null);
                this.$refs.doodle.style.cursor = 'crosshair';
            },
            newBboxElement() {
                let dom = document.createElement('div');
                dom.className = 'bbox';
                this.$refs.doodle.appendChild(dom);
                //dom.style.display = 'none';
                return dom;
            },
            interactify(annotatedObject, onChange) {
                let that = this;
                let dom = annotatedObject.dom;
                let bbox = $(dom);
                bbox.addClass('bbox');
                let createHandleDiv = (className, content = null) => {
                    //console.log('className = ' + className + '  content = ' + content);
                    let handle = document.createElement('div');
                    handle.className = className;
                    bbox.append(handle);
                    if (content !== null) {
                        handle.innerHTML = content;
                    }
                    return handle;
                };
                bbox.resizable({
                    handles: "n, e, s, w",
                    onStopResize: (e) => {
                        let position = bbox.position();
                        onChange(Math.round(position.left), Math.round(position.top), Math.round(bbox.width()), Math.round(bbox.height()));
                    }
                });
                let x = createHandleDiv('handle center-drag');
                let i = createHandleDiv('objectId', annotatedObject.idObject);
                i.addEventListener("click", function () {
                    that.$store.dispatch('selectObject', parseInt(this.innerHTML))
                });
                bbox.draggable({
                    handle: $(x),
                    onDrag: (e) => {
                        var d = e.data;
                        if (d.left < 0) {
                            d.left = 0
                        }
                        if (d.top < 0) {
                            d.top = 0
                        }
                        if (d.left + $(d.target).outerWidth() > $(d.parent).width()) {
                            d.left = $(d.parent).width() - $(d.target).outerWidth();
                        }
                        if (d.top + $(d.target).outerHeight() > $(d.parent).height()) {
                            d.top = $(d.parent).height() - $(d.target).outerHeight();
                        }
                    },
                    onStopDrag: (e) => {
                        let position = bbox.position();
                        onChange(Math.round(position.left), Math.round(position.top), Math.round(bbox.width()), Math.round(bbox.height()));
                    }
                });
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
                // this.mouse.x -= this.$refs.doodle.offsetLeft;
                // this.mouse.y -= this.$refs.doodle.offsetTop;
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
            onMouseClick(event) {
                //console.log(event);
                let doodle = this.$refs.doodle;
                if (doodle.style.cursor !== 'crosshair') {
                    return;
                }
                if (this.tempAnnotatedObject != null) {
                    let annotatedObject = new AnnotatedObject();
                    annotatedObject.dom = this.tempAnnotatedObject.dom;
                    let absolute = this.toAbsoluteCoord(this.tempAnnotatedObject.x, this.tempAnnotatedObject.y, this.tempAnnotatedObject.width, this.tempAnnotatedObject.height);
                    //console.log(absolute);
                    let bbox = new BoundingBox(absolute.x, absolute.y, absolute.width, absolute.height);
                    annotatedObject.add(new AnnotatedFrame(this.currentFrame, bbox, true));
                    this.addAnnotatedObjectControls(annotatedObject);
                    this.tempAnnotatedObject = null;

                    this.interactify(
                        annotatedObject,
                        (x, y, width, height) => {
                            //console.log('annotated object changing - box frame ' + this.currentFrame);
                            //console.log(x + ' ' + y + ' ' + width + ' ' + height);
                            let currentObject = this.$store.state.currentObject;
                            if (!currentObject) {
                                return;
                            }
                            console.log('interactify fn', currentObject.idObject)
                            if (annotatedObject.idObject !== currentObject.idObject) {
                                return;
                            }
                            let absolute = this.toAbsoluteCoord(x, y, width, height);
                            //console.log(absolute);
                            let bbox = new BoundingBox(absolute.x, absolute.y, absolute.width, absolute.height);
                            annotatedObject.add(new AnnotatedFrame(this.currentFrame, bbox, true));
                            console.log('box changed!', annotatedObject.idObject, absolute.x + absolute.y + absolute.width + absolute.height);
                            this.$store.dispatch("setObjectState", {
                                object: annotatedObject,
                                state: 'dirty',
                                flag: absolute.x + absolute.y + absolute.width + absolute.height
                            });
                        }
                    );
                    doodle.style.cursor = 'default';
                } else {
                    this.mouse.startX = this.mouse.x;
                    this.mouse.startY = this.mouse.y;
                    let dom = this.newBboxElement();
                    dom.style.left = this.mouse.x + 'px';
                    dom.style.top = this.mouse.y + 'px';
                    dom.style.borderColor = '#D3D3D3';
                    this.tempAnnotatedObject = {
                        dom: dom
                    };
                }
            },
            onShowBoxes() {
                console.log('onshowboxes3', this.currentVideoState);
                this.drawFrameBoxes(this.currentFrame);
            },
        },
    }

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
            <work-pane v-if="showControls" @event-showboxes="onShowBoxes"></work-pane>
        </div>
    </div>
</script>

