<link rel="stylesheet" type="text/css"
      href="{{$manager->getAppFileURL($manager->getApp(), 'modules/annotation/views/main/vue/vatic.webtool.css')}}">

<!-- jp-player -->
<script type="text/javascript"
        src="{{$manager->getStaticURL($manager->getApp(), 'scripts/jplayer/jquery.jplayer.min.js')}}"></script>
<link rel="stylesheet" type="text/css"
      href="{{$manager->getStaticURL($manager->getApp(), 'scripts/jplayer/blue.monday/css/jplayer.blue.monday.css')}}">

@include('render/controlPane')
@include('render/videoPane')


<div id="renderVideo" style="width:100%;height:100%;">
    <div style="width:100%;padding:5px;height:70%;background:#eee;">
        <div id="videoPaneContainer"
             style="display:flex; flex-direction: column;padding-left: 16px; width:auto; height:360px">
            <div id="doodle" style="width:640px;height:360px">
                <div id="jquery_jplayer_1" class="jp-jplayer">
                </div>
            </div>
        </div>
        <div role="application" aria-label="media player"
             style="text-align:left; padding-left: 16px;width:640px">
            <div id="controlsPane" style="margin-bottom: 0px;width:640px">
                <div id="controls">
                    <button id="btnBeginning"></button>
                    <button id="btnBackward"></button>
                    <button id="btnPlay"></button>
                    <button id="btnPause"></button>
                    <button id="btnForward"></button>
                    <div style="width:80px;padding:5px">
                    </div>
                    <div id="sliderContainer" style=";width:640px">
                        <input id="sliderVideo"/>
                    </div>
                </div>
            </div>
            <div id="jcontrols" style=";width:640px">
                <span id="displayTime"></span>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    const subjectCurrentState = new rxjs.Subject();
    const subjectCurrentFrame = new rxjs.Subject();
    const subjectCurrentSlider = new rxjs.Subject();

    window.model = {
        document: @json($data->document),
        documentMM: @json($data->documentMM),
    };

    window.storage = {
        currentState: {
            set: (value) => {
                annotationImageStore.set('currentState', value);
                subjectCurrentState.next(value);
            },
            get: () => annotationImageStore.get('currentState')
        },
        currentFrame: {
            set: (value) => {
                annotationImageStore.set('currentFrame', value);
                subjectCurrentFrame.next(value);
            },
            get: () => annotationImageStore.get('currentFrame')
        },
        currentSlider: {
            set: (value) => {
                annotationImageStore.set('currentSlider', value);
                subjectCurrentSlider.next(value);
            },
            get: () => annotationImageStore.get('currentSlider')
        },
    }

    let multimodal = {
        getURL: (action) => {
            return '/index.php/webtool/structure/multimodal/' + action;
        }
    }

    $(function () {


        var build = {
            getURL: function (url, id) {
                return "/index.php/webtool/" + url + '/' + id;
            },
        }

        $('#btnBeginning').linkbutton({
            plain: true,
            selected: true,
            onClick: () => controlPane.onBeginningClick(),
            iconCls: 'fas fa-fast-backward'
        })

        $('#btnBackward').linkbutton({
            plain: true,
            selected: true,
            onClick: () => controlPane.onBackwardClick(),
            iconCls: 'fas fa-step-backward'
        })

        $('#btnPlay').linkbutton({
            plain: true,
            selected: true,
            onClick: () => controlPane.onPlayClick(),
            iconCls: 'fas fa-play'
        })

        $('#btnPause').linkbutton({
            plain: true,
            selected: true,
            onClick: () => controlPane.onPauseClick(),
            iconCls: 'fas fa-pause'
        })

        $('#btnForward').linkbutton({
            plain: true,
            selected: true,
            onClick: () => controlPane.onForwardClick(),
            iconCls: 'fas fa-step-forward'
        })

        subjectCurrentState.subscribe({
            next: (currentState) => {
                if (currentState === 'playing') {
                    videoPane.play();
                }
                if (currentState === 'paused') {
                    videoPane.pause();
                }
                if (currentState === 'loaded') {
                    videoPane.config.timeInterval = 1000 / videoPane.config.fps;
                    videoPane.framesRange = {
                        first: 0,
                        last: parseInt((videoPane.config.duration * 1000) / videoPane.config.timeInterval)
                    }
                    controlPane.mount();
                }
            }
        });
        subjectCurrentFrame.subscribe({
            next: (currentFrame) => {
                videoPane.currentFrame = currentFrame;
                //console.log('currentFrame changed to ' + currentFrame)
                if (videoPane.isPlaying) {
                } else {
                    let currentState = storage.currentState.get();
                    if (currentState !== 'dragging') {
                        videoPane.pause();
                    }
                }
            }
        });
        subjectCurrentSlider.subscribe({
            next: (currentSlider) => {
                videoPane.currentTime = (videoPane.config.duration * (currentSlider / 100));
            }
        });

        videoPane.start();


    })
    ;
</script>
