<script type="text/javascript">
    $(function () {
            let framesManager = new FramesManager();
            let annotatedObjectsTracker = new AnnotatedObjectsTracker(framesManager);
            let tmpAnnotatedObject = null;


            let player = {
                currentFrame: 0,
                isPlaying: false,
                isReady: false,
                timeout: null,

                initialize: function () {
                    this.currentFrame = 0;
                    this.isPlaying = false;
                    this.isReady = false;
                },

                ready: function () {
                    this.isReady = true;
                },

                seek: function (frameNumber) {
                    if (!this.isReady) {
                        return;
                    }
                    console.log('playre.seek = ' + frameNumber);
                    this.pause();
                    if (frameNumber >= 0 && frameNumber < framesManager.frames.totalFrames()) {
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

                    this.isPlaying = true;
                    this.nextFrame();
                },

                pause: function () {
                    if (!this.isReady) {
                        return;
                    }

                    this.isPlaying = false;
                    if (this.timeout != null) {
                        clearTimeout(this.timeout);
                        this.timeout = null;
                    }

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

                    if (this.currentFrame >= framesManager.frames.totalFrames()) {
                        this.done();
                        return;
                    }

                    this.drawFrame(this.currentFrame).then(() => {
                        this.currentFrame++;
                        this.timeout = setTimeout(() => this.nextFrame(), 1000 / (config.fps * parseFloat(speedInput.value)));
                    });
                },

                drawFrame: function (frameNumber) {
                    return new Promise((resolve, _) => {
                        annotatedObjectsTracker.getFrameWithObjects(frameNumber).then((frameWithObjects) => {
                            ctx.drawImage(frameWithObjects.img, 0, 0);

                            for (let i = 0; i < frameWithObjects.objects.length; i++) {
                                let object = frameWithObjects.objects[i];
                                let annotatedObject = object.annotatedObject;
                                let annotatedFrame = object.annotatedFrame;
                                if (annotatedFrame.isVisible()) {
                                    annotatedObject.dom.style.display = 'block';
                                    annotatedObject.dom.style.width = annotatedFrame.bbox.width + 'px';
                                    annotatedObject.dom.style.height = annotatedFrame.bbox.height + 'px';
                                    annotatedObject.dom.style.left = annotatedFrame.bbox.x + 'px';
                                    annotatedObject.dom.style.top = annotatedFrame.bbox.y + 'px';
                                    annotatedObject.visible.prop('checked', true);
                                } else {
                                    annotatedObject.dom.style.display = 'none';
                                    annotatedObject.visible.prop('checked', false);
                                }
                            }

                            let shouldHideOthers = frameWithObjects.objects.some(o => o.annotatedObject.hideOthers);
                            if (shouldHideOthers) {
                                for (let i = 0; i < frameWithObjects.objects.length; i++) {
                                    let object = frameWithObjects.objects[i];
                                    let annotatedObject = object.annotatedObject;
                                    if (!annotatedObject.hideOthers) {
                                        annotatedObject.dom.style.display = 'none';
                                    }
                                }
                            }

                            slider.setPosition(this.currentFrame);

                            resolve();
                        });
                    });
                },

                done: function () {
                    this.currentFrame = 0;
                    this.isPlaying = false;

                    //playButton.disabled = false;
                    //playButton.style.display = 'block';
                    //pauseButton.disabled = true;
                    //pauseButton.style.display = 'none';
                    $('#btnPlay').linkbutton('enable');
                    $('#btnPause').linkbutton('disable');

                }
            };

            function clearAllAnnotatedObjects() {
                for (let i = 0; i < annotatedObjectsTracker.annotatedObjects.length; i++) {
                    clearAnnotatedObject(i);
                }
            }

            function clearAnnotatedObject(i) {
                let annotatedObject = annotatedObjectsTracker.annotatedObjects[i];
                annotatedObject.controls.remove();
                $(annotatedObject.dom).remove();
                annotatedObjectsTracker.annotatedObjects.splice(i, 1);
                annotatedObjectsSet.remove(annotatedObject)
            }

            function interactify(dom, onChange) {
                let bbox = $(dom);
                bbox.addClass('bbox');

                let createHandleDiv = (className) => {
                    let handle = document.createElement('div');
                    handle.className = className;
                    bbox.append(handle);
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
                console.log(x);
                bbox.draggable({
                    //containment: 'parent',
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
                    //stop: (e, ui) => {
                    onStopDrag: (e) => {
                        let position = bbox.position();
                        onChange(Math.round(position.left), Math.round(position.top), Math.round(bbox.width()), Math.round(bbox.height()));
                    }
                });
            }

            function newBboxElement() {
                let dom = document.createElement('div');
                dom.className = 'bbox';
                doodle.appendChild(dom);
                console.log('new box');
                console.log(dom);
                return dom;
            }

            function addAnnotatedObjectControls(annotatedObject) {
                annotatedObject.name = '';
                annotatedObject.visible = true;
                annotatedObject.hide = false;
                annotatedObject.idFE = -1;
                annotatedObject.fe = '';
                annotatedObject.startTime = config.currentTime;
                annotatedObject.endTime = config.currentTime;
                annotatedObjectsSet.add(annotatedObject)
                let border = 'color' + annotatedObject.id;
                annotatedObject.dom.classList.add(border);
                updateObjectsGrid();
            }
        });


</script>