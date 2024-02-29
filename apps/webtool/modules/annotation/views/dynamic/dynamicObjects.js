window.dynamicObjects = {
    tracker: null,
    framesManager: null,
    init: () => {
        console.log('initing dynamicObjects');
        dynamicObjects.framesManager = new FramesManager();
        dynamicObjects.tracker = new AnnotatedObjectsTracker(dynamicObjects.framesManager);
    },
    add: (annotatedObject) => {
        // annotatedObject.idObject = dynamicObjects.tracker.getLength() + 1;
        dynamicObjects.tracker.add(annotatedObject);
    },
    push: (annotatedObject) => {
        dynamicObjects.tracker.add(annotatedObject);
    },
    get: (idObject) => {
        return dynamicObjects.tracker.annotatedObjects.find(o => o.idObject === idObject);
    },
    clear: (annotatedObject) => {
        dynamicObjects.tracker.clear(annotatedObject);
    },
    clearAll: () => {
        dynamicObjects.tracker.clearAll();
    },
    getByIdObjectMM: (idObjectMM) => {
        return dynamicObjects.tracker.annotatedObjects.find(o => o.idObjectMM === idObjectMM);
    },
    clearObject: (i) => {
        let annotatedObject = dynamicObjects.tracker.get(i);
        dynamicObjects.tracker.clear(annotatedObject);
    },
    toAbsoluteCoord: (x, y, width, height, currentScale) => {
        return {
            x: Math.round(x / currentScale),
            y: Math.round(y / currentScale),
            width: Math.round(width / currentScale),
            height: Math.round(height / currentScale)
        }
    },
    toScaledCoord: (x, y, width, height, currentScale) => {
        return {
            x: Math.round(x * currentScale),
            y: Math.round(y * currentScale),
            width: Math.round(width * currentScale),
            height: Math.round(height * currentScale)
        }
    },
    interactify: (annotatedObject, onChange) => {
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
        let x = createHandleDiv('handle center-drag');
        let i = createHandleDiv('objectId', annotatedObject.idObject);
        bbox.resizable({
            handles: "n, e, s, w",
            onStopResize: (e) => {
                let position = bbox.position();
                onChange(Math.round(position.left), Math.round(position.top), Math.round(bbox.width()), Math.round(bbox.height()));
            }
        });
        i.addEventListener("click", function () {
            dynamicStore.dispatch('selectObject', parseInt(this.innerHTML))
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
        bbox.css('display','none')
    },
    newBboxElement: (boxesContainer) => {
        let dom = document.createElement('div');
        dom.className = 'bbox';
        boxesContainer.appendChild(dom);
        //dom.style.display = 'none';
        return dom;
    },
    loadObjectsFromDb: async () => {
        dynamicObjects.clearAll();
        let boxesContainer = annotationVideoModel.boxesContainer;
        let currentScale = annotationVideoModel.currentScale;
        let objectsLoaded = await annotationVideoModel.api.loadObjects();
        // console.log(objectsLoaded);
        let i = 1;
        for (var object of objectsLoaded) {
            if ((object.startFrame >= annotationVideoModel.framesRange.first) && (object.startFrame <= annotationVideoModel.framesRange.last)) {
                object.color = vatic.getColor(i);
                let annotatedObject = new AnnotatedObject();
                annotatedObject.loadFromDb(i++, object)
                annotatedObject.dom = dynamicObjects.newBboxElement(boxesContainer);
                dynamicObjects.add(annotatedObject);
                dynamicObjects.interactify(
                    annotatedObject,
                    (x, y, width, height) => {
                        let currentScale = annotationVideoModel.currentScale;
                        console.log(x,y,width,height)
                            let absolute = dynamicObjects.toAbsoluteCoord(x, y, width, height, currentScale);
                        console.log(absolute);
                            let bbox = new BoundingBox(absolute.x, absolute.y, absolute.width, absolute.height);
                            let currentFrame = dynamicStore.state.currentFrame;
                            annotatedObject.add(new AnnotatedFrame(currentFrame, bbox, true));
                            console.log('box changed!', annotatedObject.idObject);
                            dynamicObjects.saveRawObject(annotatedObject);
                        // this.$store.dispatch("setObjectState", {
                        //     object: annotatedObject,
                        //     state: 'dirty',
                        //     flag: this.currentFrame
                        // });
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
        return objectsLoaded;
        // dynamicStore.commit('objects', objectsLoaded)
        // dynamicStore.commit('updateGridPane', true)
    },
    addControlsToObject: (annotatedObject) => {
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
    },
    createNewObject: async (tempObject, currentScale, currentFrame) => {
        if (currentFrame === 0) {
            currentFrame = 1;
        }
        console.log('createNewObject',tempObject,currentScale,currentFrame);
        let annotatedObject = new AnnotatedObject();
        annotatedObject.dom = tempObject.dom;
        let absolute = dynamicObjects.toAbsoluteCoord(tempObject.x, tempObject.y, tempObject.width, tempObject.height, currentScale);
        let bbox = new BoundingBox(absolute.x, absolute.y, absolute.width, absolute.height);
        annotatedObject.add(new AnnotatedFrame(currentFrame, bbox, true));
        dynamicObjects.addControlsToObject(annotatedObject);
        dynamicObjects.interactify(
            annotatedObject,
            (x, y, width, height) => {
                let currentObject = annotatedObject;//this.$store.state.currentObject;
                if (!currentObject) {
                    return;
                }
                console.log('interactify fn', currentObject.idObject)
                if (annotatedObject.idObject !== currentObject.idObject) {
                    return;
                }
                let absolute = dynamicObjects.toAbsoluteCoord(x, y, width, height, currentScale);
                let bbox = new BoundingBox(absolute.x, absolute.y, absolute.width, absolute.height);
                let currentFrame = dynamicStore.state.currentFrame;
                annotatedObject.add(new AnnotatedFrame(currentFrame, bbox, true));
                console.log('box changed!', annotatedObject.idObject, absolute.x + absolute.y + absolute.width + absolute.height);
                dynamicObjects.saveRawObject(annotatedObject);
                // this.$store.dispatch("setObjectState", {
                //     object: annotatedObject,
                //     state: 'dirty',
                //     flag: absolute.x + absolute.y + absolute.width + absolute.height
                // });
            }
        );
        annotatedObject.startFrame = currentFrame;
        annotatedObject.endFrame = annotationVideoModel.framesRange.last;
        console.log('##### creating newObject');
        let data = await dynamicObjects.saveObject(annotatedObject,{
            idDocumentMM: annotationVideoModel.documentMM.idDocumentMM,
            idObjectMM: null,
            startFrame: annotatedObject.startFrame,
            endFrame: annotatedObject.endFrame,
            idFrame:null,
            idFrameElement: null,
            idLU: null,
            startTime: (annotatedObject.startFrame - 1) / annotationVideoModel.fps,
            endTime:(annotatedObject.endFrame - 1) / annotationVideoModel.fps,
        })
        $.messager.alert('Ok', "New object created.", 'info');
        dynamicStore.commit('currentState', 'objectEditing')
//        dynamicStore.dispatch('selectObjectMM', data.idObjectMM);
        return data;
    },
    getObjectFrameData: (currentObject, startFrame, endFrame) => {
        console.log('getObjectFrameData', currentObject, startFrame, endFrame)
        let data = [];
        //let lastFrame = currentObject.endFrame;
        let lastFrame = startFrame;
        for (frame of currentObject.frames) {
            if ((frame.frameNumber >= startFrame) && (frame.frameNumber <= endFrame)) {
                if (frame.bbox !== null) {
                    data.push({
                        frameNumber: frame.frameNumber,
                        frameTime: (frame.frameNumber - 1) / annotationVideoModel.fps,
                        x: frame.bbox.x,
                        y: frame.bbox.y,
                        width: frame.bbox.width,
                        height: frame.bbox.height,
                        blocked: frame.blocked,
                    })
                    lastFrame = frame.frameNumber;
                }
            }
        }
        return {
            frames:data,
            lastFrame: lastFrame
        }
    },
    saveObject: async (currentObject, params) => {
        console.log('##### saving newObject');
        console.log('saveObject', currentObject,params)
        /*
         params = {
            idObjectMM
            idDocumentMM
            startFrame
            endFrame
            idFrame
            frame
            idFrameElement
            fe
            idLU
            lu
            startTime
            endTime
        }
         */
        if (params.startFrame > params.endFrame) {
            throw new Error('endFrame must be greater or equal to startFrame.');
        }

        if (params.endFrame > currentObject.endFrame) {
            let bbox = null;
            let j = currentObject.frames.length - 1;
            let polygon = currentObject.frames[j];
            for (let i = currentObject.endFrame; i <= params.endFrame; i++) {
                let frameNumber = i;
                let isGroundThrough = true;
                let x = parseInt(polygon.bbox.x);
                let y = parseInt(polygon.bbox.y);
                let w = parseInt(polygon.bbox.width);
                let h = parseInt(polygon.bbox.height);
                bbox = new BoundingBox(x, y, w, h);
                let annotatedFrame = new AnnotatedFrame(frameNumber, bbox, isGroundThrough);
                annotatedFrame.blocked = (parseInt(polygon.blocked) === 1);
                currentObject.add(annotatedFrame);
            }
        }

        if (params.startFrame < currentObject.startFrame) {
            let bbox = null;
            let polygon = currentObject.get(currentObject.startFrame);
            console.log(polygon);
            for (let i = params.startFrame; i < currentObject.startFrame; i++) {
                let frameNumber = i;
                let isGroundThrough = true;
                let x = parseInt(polygon.bbox.x);
                let y = parseInt(polygon.bbox.y);
                let w = parseInt(polygon.bbox.width);
                let h = parseInt(polygon.bbox.height);
                bbox = new BoundingBox(x, y, w, h);
                let annotatedFrame = new AnnotatedFrame(frameNumber, bbox, isGroundThrough);
                annotatedFrame.blocked = (parseInt(polygon.blocked) === 1);
                currentObject.add(annotatedFrame);
            }
        }
        let frames = dynamicObjects.getObjectFrameData(currentObject, params.startFrame, params.endFrame);
        console.log(frames);
        params.frames = frames.frames;

        let data = await dynamicAPI.updateObject(params);
        console.log(data);
        annotationVideoModel.currentIdObjectMM = data.idObjectMM;
        dynamicStore.commit('updateGridPane', true)
        return data;
    },
    saveObjectData: async (currentObject, params) => {
        console.log('saveObject', currentObject,params)
        /*
         params = {
            idObjectMM
            idDocumentMM
            startFrame
            endFrame
            idFrame
            frame
            idFrameElement
            fe
            idLU
            lu
            startTime
            endTime
        }
         */
        if (params.startFrame > params.endFrame) {
            throw new Error('endFrame must be greater or equal to startFrame.');
        }
        let data = await dynamicAPI.updateObjectData(params);
        console.log(data);
        annotationVideoModel.currentIdObjectMM = data.idObjectMM;
        dynamicStore.commit('updateGridPane', true)
        return data;
    },
    saveCurrentObject: async () => {
        let object = dynamicStore.state.currentObject;
        let currentObject = dynamicObjects.get(object.idObject);
        console.log('saving currentObject', currentObject)
        dynamicObjects.saveRawObject(currentObject)
    },
    saveRawObject: async (currentObject) => {
        console.log('saving object #', currentObject.idObject)
        let params = {
            idObjectMM: currentObject.idObjectMM,
            idDocumentMM: annotationVideoModel.documentMM.idDocumentMM,
            startFrame: currentObject.startFrame,
            endFrame: currentObject.endFrame,
            idFrame:currentObject.idFrame,
            idFrameElement: currentObject.idFE,
            idLU: currentObject.idLU,
            startTime: (currentObject.startFrame - 1) / annotationVideoModel.fps,
            endTime:(currentObject.endFrame - 1) / annotationVideoModel.fps,
        }
        let frames = dynamicObjects.getObjectFrameData(currentObject, params.startFrame, params.endFrame);
        params.frames = frames.frames;
        console.log('params',params);
        let data = await dynamicAPI.updateObject(params);
    },
    deleteObject: async(currentObject) => {
        let msg = 'Current Object: #' + currentObject.idObject + ' [' + currentObject.idObjectMM + '] deleted.';
        await dynamicAPI.deleteObjects([currentObject.idObjectMM]);
        annotationVideoModel.currentIdObjectMM = -1;
        dynamicStore.commit('updateGridPane', true)
        $.messager.alert('Ok', msg, 'info');
    }

}