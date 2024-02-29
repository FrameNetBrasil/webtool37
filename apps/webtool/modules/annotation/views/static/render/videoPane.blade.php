<script>
    videoPane = {
        framesManager: null,//this.$store.state.framesManager,
        framesRange: {},// this.$store.state.model.framesRange,
        currentState: '',
        currentTime: 0,
        firstFrame: 0,
        lastFrame: 0,
        currentFrame: 0,
        currentScale: 0.5,
        isPlaying: false,
        isReady: false,
        timeout: null,
        showControls: false,
        ctx: null,
        config: {
            // Should be higher than real FPS to not skip real frames
            // Hardcoded due to JS limitations
            fps: 25,//30,
            // Low rate decreases the chance of losing frames with poor browser performances
            playbackRate: 1.0,//0.4,
            // Format of the extracted frames
            imageMimeType: 'image/jpeg',
            imageExtension: '.jpg',
            // Name of the extracted frames zip archive
            framesZipFilename: 'extracted-frames.zip',
            duration: 0.0,
            timeInterval: 40,
            url: '',
        },
        displayTime: function () {
            return this.formatTime(this.currentTime);
        },
        totalDuration: function () {
            return this.formatTime(this.config.duration);
        },
        mount: function () {
            this.showControls = false;
            this.currentState = annotationImageStore.get('currentState');
        },
        loadVideo: function () {
            //console.log('visual pane mounted');
            let that = this;
            $("#jquery_jplayer_1").jPlayer({
                ready: function () {
                    //console.log('jplayer ready');
                    let videoPath = model.documentMM.videoPath.replace('/var/www/html', '');
                    console.log('video path = ' + videoPath);
                    $(this).jPlayer("setMedia", {
                        title: "",
                        //m4a: "http://127.0.0.1/webtool/apps/webtool/files/multimodal/audio/PedroPeloMundo_Se01_Ep06_Bl01_Par01.mp4"
                        //m4v: "http://server2.framenetbr.ufjf.br/webtooldev/apps/webtool/files/multimodal/videos/PedroPeloMundoSe01Ep06Bl01.mp4"
                        //m4v: "http://server2.framenetbr.ufjf.br/webtooldev/apps/webtool/files/multimodal/videos/PedroPeloMundoSe01Ep06Bl01.mp4"
                        m4v: videoPath
                    });
                },
                size: {
                    width: "640px",
                    height: "360px",
                    cssClass: "jp-video-360p"
                },
                defaultPlaybackRate: 1.0,//0.5,
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
                    storage.currentState.set('loaded');
                    this.ready();
                    console.log('video loaded');
                },
                timeupdate: event => {
                    this.currentTime = event.jPlayer.status.currentTime;
                    console.log('== event time update - jplayer1 - ' + this.currentTime);
                    $('#displayTime').html(this.formatTime(this.currentTime));
                    console.log('time = ' + this.formatTime(this.currentTime));
                    if (this.isPlaying) {
                        let currentFrame = parseInt((event.jPlayer.status.currentTime * 1000) / this.config.timeInterval);
                        annotationImageStore.set('playFrame', currentFrame);
                        this.currentFrame = currentFrame;
                    }
                },
            });
        },
        formatTime: function (timeToFormat) {
            /*
            let dt1 = timeToFormat + '00';
            let ft = parseFloat(dt1);
            let i = parseInt(dt1);
            let horas = parseInt(i / 3600);
            let resto = i - (horas * 3600);
            let minutos = parseInt(resto / 60);
            let segundos = resto - (minutos * 60);
            let ms = parseInt((ft - i) * 1000);
            let show = horas + ':' + minutos + ':' + segundos + '.' + ms;
             */
            let dt1 = timeToFormat;
            let ft = parseFloat(dt1);
            let i = parseInt(dt1);
            let ms = parseInt((ft - i) * 1000);
            let show = i + '.' + ms;
            return show;
        },
        ready: function () {
            this.isReady = true;
        },
        getFrameNumberFromTime: function (timeString) {
            console.log(timeString);
            /*
            var hms = timeString;   // your input string
            var a = hms.split(':'); // split it at the colons
            var seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
            console.log(this.config.fps);
            console.log(parseFloat(seconds));
            let frameNumber = this.config.fps * parseFloat(seconds);
             */
            let ft = parseFloat(timeString);
            let frameNumber = this.config.fps * ft;
            return frameNumber;
        },
        seek: function (frameNumber) {
            if (!this.isReady) {
                return;
            }
            this.pause();
            console.log('frameNumber', frameNumber);
            console.log('last', this.framesRange.last);
            if (frameNumber >= 0 && frameNumber < this.framesRange.last) {
                //this.drawFrame(frameNumber);
                this.currentFrame = frameNumber;
                console.log(this.currentFrame);
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
            //this.$store.commit('currentState', 'playing');
            console.log('playing ' + this.currentFrame);
            $("#jquery_jplayer_1").jPlayer('play', (((this.currentFrame) * this.config.timeInterval) / 1000));
            this.isPlaying = true;
            //this.nextFrame();
        },
        pause: function () {
            if (!this.isReady) {
                return;
            }

            this.isPlaying = false;
            //this.$store.commit('currentState', 'paused');
            let timeForFrame = (this.currentFrame * this.config.timeInterval) / 1000;
            //console.log('== paused = ' + this.currentFrame + '  ' + timeForFrame);

            $("#jquery_jplayer_1").jPlayer('pause', timeForFrame);
            $("#jquery_jplayer_2").jPlayer('pause', timeForFrame);

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
            annotationImageStore.set('currentFrame', this.currentFrame);
        },
        done: function () {
            this.currentFrame = this.firstFrame;
            this.isPlaying = false;
            annotationImageStore.set('currentState', 'paused');
        },
        initializeCanvasDimensions: function () {
            this.currentDimensions = {
                width: parseInt(this.originalDimensions.width * 0.5),
                height: parseInt(this.originalDimensions.height * 0.5),
            }
            console.log(this.currentDimensions);
            $("#doodle").css({
                "width": this.currentDimensions.width + 'px',
                "height": this.currentDimensions.height + 'px'
            });
            $("#jp_container").css("width", this.currentDimensions.width + 'px');
        },
        framesLoaded(firstFrame, lastFrame) {
            annotationImageStore.set('updateFramesRange', this.framesRange);
        },
        start: function () {
            this.currentFrame = 0;
            this.isPlaying = false;
            this.isReady = false;
            annotationImageStore.set('currentState', 'loading');
            this.mount();
            this.loadVideo();
        },
    }
</script>