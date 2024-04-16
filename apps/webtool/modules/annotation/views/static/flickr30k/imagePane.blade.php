@php
    $imageURL = "https://flickr30k.frame.net.br/flickr30k-images/" . $data->imageMM->name;
    $imageWidth = $data->imageMM->width;
    $imageHeight = $data->imageMM->height;

@endphp
<script>
    let imagePane = {
        template: '#image-pane',
        components: {
            'work-pane': workPane,
        },
        props: [],
        data() {
            return {
                ctx: null,
                currentScale: 1,
                originalDimensions: {
                    width: {{$imageWidth}},
                    height: {{$imageHeight}}
                },
                tempAnnotatedBox: null,
                mouse: {
                    x: 0,
                    y: 0,
                    startX: 0,
                    startY: 0
                },
                flick30kMode: this.$store.state.model.flickr30kMode
            }
        },
        created() {
            this.$store.dispatch('objectsTrackerInit');
        },
        mounted() {
            this.ctx = this.$refs.canvas.getContext('2d');
            //
            // watch change currentObjectState
            //
            this.$store.watch(
                (state, getters) => getters.currentObjectState,
                (currentObjectState) => {
                    if ((currentObjectState === 'updated') || (currentObjectState === 'none') || (currentObjectState === 'cleared')) {
                        this.drawFrame();
                    }
                    if (currentObjectState === 'creating') {
                        this.onNewObject();
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
                        this.drawFrame(this.$store.state.currentFrame);
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

            this.initializeFrameObject();

            this.$nextTick(() => {
                let that = this;
            })
        },
        methods: {
            clearAllAnnotatedObjects() {
                this.$store.dispatch('objectsTrackerClearAll');
            },
            clearAnnotatedObject(i) {
                this.$store.dispatch('clearAnnotatedObject', i);
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
                annotatedObject.startFrame = -1;
                annotatedObject.endFrame = -1;
                annotatedObject.startWord = -1;
                annotatedObject.endWord = -1;
                this.$store.dispatch('objectsTrackerAdd', annotatedObject);
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
            drawFrame: function () {
                console.log('drawing frame');
                let that = this;
                jQuery('.bbox').css("display", "none");
                let annotatedObjectsTracker = this.$store.getters.objectsTracker;
                //console.log(annotatedObjectsTracker);
                let objects = annotatedObjectsTracker.getObjects();
                let currentObject = that.$store.state.currentObject;
                let currentBox = that.$store.state.currentBox;
                currentState = that.$store.state.currentObjectState;
                for (let i = 0; i < objects.length; i++) {
                    let object = objects[i];
                    let annotatedObject = object.annotatedObject;
                    if (annotatedObject.hidden) {
                        for (let b = 0; b < annotatedObject.boxes.length; b++) {
                            let annotatedBox = annotatedObject.boxes[b];
                            annotatedBox.dom.style.display = 'none';
                        }
                    } else {
                        for (let b = 0; b < annotatedObject.boxes.length; b++) {
                            let annotatedBox = annotatedObject.boxes[b];
                            if (annotatedObject.isVisible()) {
                                if (annotatedBox.isVisible()) {
                                    let scaledBox = this.toScaledCoord(annotatedBox.bbox.x, annotatedBox.bbox.y, annotatedBox.bbox.width, annotatedBox.bbox.height);
                                    annotatedBox.dom.style.display = 'block';
                                    annotatedBox.dom.style.width = scaledBox.width + 'px';
                                    annotatedBox.dom.style.height = scaledBox.height + 'px';
                                    annotatedBox.dom.style.left = scaledBox.x + 'px';
                                    annotatedBox.dom.style.top = scaledBox.y + 'px';
                                    annotatedBox.dom.style.borderStyle = 'solid';
                                    annotatedBox.dom.style.borderColor = annotatedObject.color;
                                    annotatedObject.visible = true;
                                    //console.log('annotated', annotatedObject.idObject);
                                    annotatedBox.dom.style.backgroundColor = 'transparent';
                                    annotatedBox.dom.style.opacity = 1;
                                    if (currentBox && (annotatedBox.id === currentBox.id)) {
                                        let tempAnnotatedObject = that.$store.getters.annotatedObject(currentBox.idObject);
                                        annotatedBox.dom.style.backgroundColor = tempAnnotatedObject.color;
                                        annotatedBox.dom.style.opacity = 0.5;
                                    } else {
                                         // if (currentObject && (annotatedObject.idObject === currentObject.idObject)) {
                                         //     annotatedBox.dom.style.backgroundColor = currentObject.color;
                                         //     annotatedBox.dom.style.opacity = 0.5;
                                         // }
                                    }

                                    if (annotatedBox.blocked) {
                                        annotatedBox.dom.style.opacity = 0.5;
                                        annotatedBox.dom.style.backgroundColor = 'white';
                                        annotatedBox.dom.style.borderStyle = 'dashed';
                                    }
                                }
                            } else {
                                annotatedBox.dom.style.display = 'none';
                            }
                        }
                    }
                }
                that.$store.commit('redrawFrame', false);
            },
            initializeFrameObject: function () {
                this.clearAllAnnotatedObjects();
                this.loadObjects();
                this.$store.commit('objectLoaded');
                this.drawFrame();
            },
            loadObjects() {
                let objectsLoaded = this.$store.state.model.objects;
                let i = 1;
                for (var object of objectsLoaded) {
                    let annotatedObject = new AnnotatedObject();
                    annotatedObject.loadFromDb(i++, object)
                    annotatedObject.visible = false;
                    this.$store.dispatch("objectsTrackerPush", annotatedObject);
                    let bbox = null;
                    let polygons = object.frames;
                    for (let j = 0; j < polygons.length; j++) {
                        let polygon = object.frames[j];
                        let isGroundThrough = true;// parseInt(topLeft.find('l').text()) == 1;
                        let x = parseInt(polygon.x);
                        let y = parseInt(polygon.y);
                        let w = parseInt(polygon.width);
                        let h = parseInt(polygon.height);
                        let dom = this.newBboxElement();
                        let id = polygon.idObjectFrameMM;
                        bbox = new BoundingBox(x, y, w, h);
                        let annotatedBox = new AnnotatedBox(id, dom, bbox, isGroundThrough, annotatedObject.idObject);
                        annotatedBox.blocked = (parseInt(polygon.blocked) === 1);
                        annotatedBox.status = 1;
                        annotatedObject.addBox(annotatedBox);
                        //if (this.flick30kMode !== 1) {
                        //     this.interactify(
                        //         annotatedObject,
                        //         annotatedBox,
                        //         (x, y, width, height) => {
                        //             let absolute = this.toAbsoluteCoord(x, y, width, height);
                        //             let bbox = new BoundingBox(absolute.x, absolute.y, absolute.width, absolute.height);
                        //             annotatedBox.bbox = bbox;
                        //             annotatedBox.status = 0;
                        //             console.log('box changed!', annotatedObject.idObject);
                        //             this.$store.dispatch("setObjectState", {
                        //                 object: annotatedObject,
                        //                 state: 'dirty'
                        //             });
                        //         }
                        //     );
                        //}
                    }
                }
                console.log('objects loaded');
                this.$store.commit('objectsTrackerState', 'dirty');
            },
            onNewObject() {
                this.$refs.image.style.cursor = 'crosshair';
            },
            newBboxElement() {
                let dom = document.createElement('div');
                dom.className = 'bbox';
                this.$refs.image.appendChild(dom);
                return dom;
            },
            interactify(annotatedObject, annotatedBox, onChange) {
                let that = this;
                let dom = annotatedBox.dom;
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
                    //that.$store.dispatch('selectObject', parseInt(this.innerHTML))
                    that.$store.dispatch('selectBox', annotatedBox.id)
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
                const rect = this.$refs.image.getBoundingClientRect();
                this.mouse.x -= rect.x;
                this.mouse.y -= rect.y;

                if (this.tempAnnotatedBox != null) {
                    this.tempAnnotatedBox.width = Math.abs(this.mouse.x - this.mouse.startX);
                    this.tempAnnotatedBox.height = Math.abs(this.mouse.y - this.mouse.startY);
                    this.tempAnnotatedBox.x = (this.mouse.x - this.mouse.startX < 0) ? this.mouse.x : this.mouse.startX;
                    this.tempAnnotatedBox.y = (this.mouse.y - this.mouse.startY < 0) ? this.mouse.y : this.mouse.startY;

                    this.tempAnnotatedBox.dom.style.width = this.tempAnnotatedBox.width + 'px';
                    this.tempAnnotatedBox.dom.style.height = this.tempAnnotatedBox.height + 'px';
                    this.tempAnnotatedBox.dom.style.left = this.tempAnnotatedBox.x + 'px';
                    this.tempAnnotatedBox.dom.style.top = this.tempAnnotatedBox.y + 'px';
                }

            },
            onMouseClick(event) {
                //console.log(event);
                let image = this.$refs.image;
                if (image.style.cursor !== 'crosshair') {
                    return;
                }
                if (this.tempAnnotatedBox != null) {
                    //let annotatedObject = new AnnotatedObject();
                    //annotatedObject.dom = this.tempAnnotatedObject.dom;
                    let dom = this.tempAnnotatedBox.dom;
                    let absolute = this.toAbsoluteCoord(this.tempAnnotatedBox.x, this.tempAnnotatedBox.y, this.tempAnnotatedBox.width, this.tempAnnotatedBox.height);
                    //console.log(absolute);
                    let bbox = new BoundingBox(absolute.x, absolute.y, absolute.width, absolute.height);

                    let annotatedObject = this.$store.state.currentObject;
                    let annotatedBox = new AnnotatedBox(-1, dom, bbox, true, annotatedObject.idObject);
                    annotatedBox.status = 0;
                    annotatedObject.addBox(annotatedBox);

                    this.tempAnnotatedBox = null;

                    this.$store.dispatch("setObjectState", {
                        object: annotatedObject,
                        state: 'dirty'
                    });

                    this.$store.dispatch('selectBox', annotatedBox);

                    if (this.flick30kMode !== 1) {
                        this.interactify(
                            annotatedObject,
                            annotatedBox,
                            (x, y, width, height) => {
                                let currentObject = this.$store.state.currentObject;
                                if (!currentObject) {
                                    return;
                                }
                                console.log('interactify fn', currentObject.idObject)
                                if (annotatedObject.idObject !== currentObject.idObject) {
                                    return;
                                }
                                let absolute = this.toAbsoluteCoord(x, y, width, height);
                                let bbox = new BoundingBox(absolute.x, absolute.y, absolute.width, absolute.height);
                                annotatedBox.bbox = bbox;
                                annotatedBox.status = 0;
                                console.log('box changed!', annotatedObject.idObject);
                                this.$store.dispatch("setObjectState", {
                                    object: annotatedObject,
                                    state: 'dirty'
                                });
                            }
                        );
                    }
                    image.style.cursor = 'default';
                } else {
                    this.mouse.startX = this.mouse.x;
                    this.mouse.startY = this.mouse.y;
                    let dom = this.newBboxElement();
                    dom.style.left = this.mouse.x + 'px';
                    dom.style.top = this.mouse.y + 'px';
                    this.tempAnnotatedBox = {
                        dom: dom
                    };
                }
            },
        },
    }

</script>

<script type="text/x-template" id="image-pane">
    <div style="display:flex; flex-direction: column; width:auto">
        <div ref="image" id="image" @mousemove="onMouseMove" @click="onMouseClick"
             style="width: {{$imageWidth}}px;height: {{$imageHeight}}px;">
            <img src="{{$imageURL}}" width="{{$imageWidth}}" height="{{$imageHeight}}">
            <canvas ref="canvas" id="canvas" style="display:none">
            </canvas>
        </div>
    </div>
</script>

