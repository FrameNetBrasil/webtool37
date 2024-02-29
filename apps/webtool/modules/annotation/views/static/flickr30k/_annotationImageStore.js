let annotationImageStore = new Vuex.Store({
    state: {
        model: {},
        objectsTracker: null,
        objectsTrackerState: 'clean', // dirty
        objects: [],
        currentObject: null,
        currentObjectState: 'none',// creating, created, selected, editingFE, editingBox, stopping, updated
        //idObjectSelected: -1,
        annotatedObject: null,
        currentBox: null,
        boxesState: 'clean',

        framesManager: new FramesManager(),
        currentFrame: 1,
        playFrame: 0,
        totalFrames: 0,
        framesRange: {},
        currentTime: 0,
        endTime: 0,

        currentVideoState: 'loading', // playing, paused, loading, loaded
        currentSliderPosition: 0,

        redrawFrame: false,
        videoLoaded: false,
        video2Loaded: false,
        objectLoaded: false,
    },
    getters: {
        currentFrame(state) {
            return state.currentFrame
        },
        currentTime(state) {
            return state.currentTime
        },
        endTime(state) {
            return state.endTime
        },
        playFrame(state) {
            return state.playFrame
        },
        currentVideoState(state) {
            return state.currentVideoState
        },
        currentObject(state) {
            return state.currentObject
        },
        currentSliderPosition(state) {
            return state.currentSliderPosition
        },
        currentObjectState(state) {
            return state.currentObjectState
        },
        currentBox(state) {
            return state.currentBox
        },
        boxesState(state) {
            return state.boxesState
        },
        framesRange(state) {
            return state.framesRange
        },
        objectsTracker(state) {
            return state.objectsTracker;
        },
        objectsTrackerState(state) {
            return state.objectsTrackerState;
        },
        annotatedObject: (state) => (id) => {
            return state.objectsTracker.annotatedObjects.find(o => o.idObject === id);
        },
        annotatedObjectByIdObjectMM: (state) => (idObjectMM) => {
            return state.objectsTracker.annotatedObjects.find(o => o.idObjectMM === idObjectMM);
        },
        allAnnotatedObjects(state) {
            return state.objectsTracker.annotatedObjects;
        },
        videoLoaded(state) {
            //return state.videoLoaded && state.video2Loaded;
            return state.videoLoaded;
        },
        allLoaded(state) {
            //return state.videoLoaded && state.video2Loaded && state.objectLoaded;
            return state.videoLoaded && state.objectLoaded;
        }
    },
    mutations: {
        objects(state, value) {
            state.objects = value;
        },
        currentFrame(state, value) {
            state.currentTime = (value - 1) / state.model.fps;
            state.currentFrame = value;
        },
        endTime(state, value) {
            state.endTime = value;
        },
        playFrame(state, value) {
            state.playFrame = value;
        },
        currentSliderPosition(state, value) {
            state.currentSliderPosition = value;
        },
        currentVideoState(state, value) {
            state.currentVideoState = value;
        },
        currentObject(state, value) {
            state.currentObject = value;
        },
        currentObjectState(state, value) {
            state.currentObjectState = value;
        },
        currentBox(state, value) {
            state.currentBox = value;
        },
        boxesState(state, value) {
            state.boxesState = value;
        },
        totalFrames(state, value) {
            state.totalFrames = value;
        },
        model(state, model) {
            state.model = model;
        },
        annotation(state, annotation) {
            state.annotation = annotation;
        },
        framesRange(state, value) {
            state.framesRange = value;
        },
        objectsTracker(state, value) {
            state.objectsTracker = value;
        },
        objectsTrackerState(state, value) {
            state.objectsTrackerState = value;
        },
        redrawFrame(state, value) {
            state.redrawFrame = value;
        },
        videoLoaded(state, value) {
            state.videoLoaded = true;
        },
        video2Loaded(state, value) {
            state.video2Loaded = true;
        },
        objectLoaded(state, value) {
            state.objectLoaded = true;
        },
    },
    actions: {
        setEndTime(context, endTime) {
            context.commit('endTime', endTime);
            context.commit('totalFrames', endTime * context.state.model.fps);
        },
        updateFramesRange(context, framesRange) {
            context.commit('framesRange', framesRange);

        },
        objectsTrackerInit(context) {
            context.commit('objectsTracker', new AnnotatedObjectsTracker(context.state.framesManager));
            console.log('objectsTrackerInit ok')
        },
        objectsTrackerAdd(context, annotatedObject) {
            annotatedObject.idObject = context.state.objectsTracker.getLength() + 1;
            annotatedObject.color = vatic.getColor(annotatedObject.idObject);
            context.state.objectsTracker.add(annotatedObject);
            console.log('adding');
            console.log(context.state.objectsTrackerState);
            context.commit('currentObject', annotatedObject);
            context.commit('currentObjectState', 'created');
            context.commit('objectsTrackerState', 'dirty');
            context.commit('boxesState', 'dirty');
            console.log('new Object');
        },
        objectsTrackerPush(context, annotatedObject) { // use to loaded objects in objectsTracker
            context.state.objectsTracker.add(annotatedObject);
        },
        objectsTrackerClear(context, annotatedObject) {
            context.state.objectsTracker.clear(annotatedObject);
        },
        objectsTrackerClearAll(context) {
            context.state.objectsTracker.clearAll();
            context.commit('objectsTrackerState', 'clean');
        },
        clearAnnotatedObject(context, i) {
            let annotatedObject = context.state.objectsTracker.annotatedObjects[i];
            $(annotatedObject.dom).remove();
            context.state.objectsTracker.remove(i);
            context.commit('objectsTrackerState', 'dirty');
        },
        newObject(context) {
            context.commit('currentObjectState', 'creating')
        },
        selectObject(context, idObject) {
            // let idObjectSelected = context.state.idObjectSelected;
            // if ((idObject === idObjectSelected) && (context.state.currentObjectState === 'selected')) {
            let currentObject = context.state.currentObject;
            if (currentObject) {
                currentObject.visible = false;
            }
            console.log('selecting object');
            console.log('currentObject', currentObject)
            console.log('idObject', idObject)
            if (currentObject && (currentObject.idObject === idObject)) {
                context.commit('currentObjectState', 'none');
                context.commit('currentObject', null);
                //context.commit('idObjectSelected', -1);
            } else {
                let annotatedObject = context.getters.annotatedObject(idObject);
                if (annotatedObject) {
                    annotatedObject.visible = true;
                    context.commit('currentObject', annotatedObject);
                    //context.commit('idObjectSelected', idObject);
                    context.commit('currentBox', null);
                    context.commit('currentObjectState', 'selected');
                }
            }
        },
        lockObject(context, idObject) {
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                annotatedObject.locked = !annotatedObject.locked;
                context.commit('objectsTrackerState', 'dirty');
            }
        },
        hideObject(context, idObject) {
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                annotatedObject.hidden = !annotatedObject.hidden;
                context.commit('redrawFrame', true)
                context.commit('objectsTrackerState', 'dirty');
            }
        },
        delObjectAnnotation(context, idObject) {
            console.log(idObject);
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                annotatedObject.idFrame = null;
                annotatedObject.frame = '';
                annotatedObject.idFE = null;
                annotatedObject.fe = '';
                console.log(annotatedObject)
                context.commit('currentObject', annotatedObject)
                context.commit('currentObjectState', 'updated')
                context.commit('objectsTrackerState', 'dirty');
            }
        },
        updateObject(context, updatedObject) {
            let idObject = updatedObject.idObject;
            console.log('updating ' + idObject);
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                annotatedObject.idFrame = updatedObject.idFrame;
                annotatedObject.frame = updatedObject.frame;
                annotatedObject.idFE = updatedObject.idFE;
                annotatedObject.fe = updatedObject.fe;
                annotatedObject.color = updatedObject.color;
                annotatedObject.idObjectMM = updatedObject.idObjectMM;
                context.commit('currentObject', annotatedObject)
                //context.commit('idObjectSelected', idObject)
                context.commit('currentObjectState', 'updated')
                context.commit('objectsTrackerState', 'dirty');
            }
        },
        selectBox(context, annotatedBox) {
            console.log('selected box', annotatedBox.id);
            if (context.state.currentBox && (context.state.currentBox.id === annotatedBox.id)) {
                context.commit('currentBox', null);
            } else {
                let currentObject = context.state.currentObject;
                if ((currentObject == null) || (currentObject.idObject !== annotatedBox.idObject)) {
                    context.dispatch('selectObject', annotatedBox.idObject);
                }
                //context.commit('currentObjectState', 'none');
                //context.commit('currentObject', null);
                context.commit('currentBox', annotatedBox);
            }
            context.commit('redrawFrame', true)
        },
        boxBlocked(context) {
            let currentBox = context.state.currentBox;
            if (!currentBox.blocked) {
                currentBox.blocked = true;
                currentBox.isGroundTruth = true;
                context.dispatch("setObjectState", {
                    object: context.state.currentObject,
                    state: 'dirty'
                });
                context.commit('redrawFrame', true)
            }
        },
        boxVisible(context) {
            let currentBox = context.state.currentBox;
            if (currentBox.blocked) {
                currentBox.blocked = false;
                currentBox.isGroundTruth = true;
                context.dispatch("setObjectState", {
                    object: context.state.currentObject,
                    state: 'dirty'
                });
                context.commit('redrawFrame', true)
            }
        },
        boxDelete(context) {
            let currentBox = context.state.currentBox;
            console.log(currentBox);
            let annotatedObject = context.getters.annotatedObject(currentBox.idObject);
            annotatedObject.deleteBox(currentBox);
            context.commit('currentBox', null);
            context.dispatch("setObjectState", {
                object: annotatedObject,
                state: 'dirty'
            });
            context.commit('redrawFrame', true)
        },
        clearObject(context) {
            let idObject = context.state.currentObject.idObject;
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                annotatedObject.removeFrame(context.state.currentFrame);
                context.commit('currentObjectState', 'cleared');
                context.commit('objectsTrackerState', 'dirty');
            }
        },
        deleteObject(context) {
            let idObject = context.state.currentObject.idObject;
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                context.dispatch('objectsTrackerClear', annotatedObject);
                context.commit('currentObjectState', 'cleared');
                context.commit('objectsTrackerState', 'dirty');
            }
        },
        deleteObjectById(context, idObject) {
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                context.dispatch('objectsTrackerClear', annotatedObject);
                context.commit('currentObjectState', 'cleared');
                context.commit('objectsTrackerState', 'dirty');
            }
        },
        setObjectState(context, params) {
            console.log(params.object);
            if (params.object) {
                console.log('changing state', params.state);
                params.object.setState(params.state);
                context.commit('boxesState', 'dirty');
                console.log('box state dirty');
                context.commit('currentObjectState', 'editingBox');
            }
        },
    },
})

