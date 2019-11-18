let store = new Vuex.Store({
    state: {
        model: {},
        title: '',
        m4v: '',
        swfPath: '',
        urlLookupFrame: '',
        urlLookupFE: '',
        framesManager: new FramesManager(),
        objectsTracker: null,
        objectsTrackerState: 'clean', // dirty
        currentFrame: 0,
        currentState: 'loading', // playing, paused, loading, loaded
        objects: [],
        currentObject: null,
        currentObjectState: 'none',// creating, created, selected,editingFE, editingBox, stopping, updated
        idObjectSelected: -1,
        duration: 0,
        totalFrames: 0,
        framesRange: {},
        annotatedObject: null,
        redrawFrame: false,
        videoLoaded: false,
        zipLoaded: false,
    },
    getters: {
        currentFrame(state) {
            return state.currentFrame
        },
        currentState(state) {
            return state.currentState
        },
        currentObject(state) {
            return state.currentObject
        },
        currentObjectState(state) {
            return state.currentObjectState
        },
        idObjectSelected(state) {
            return state.idObjectSelected
        },
        duration(state) {
            return state.duration
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
        allAnnotatedObjects(state) {
            return state.objectsTracker.annotatedObjects;
        },
        allLoaded(state) {
            return state.videoLoaded && state.zipLoaded;
        }
    },
    mutations: {
        objects(state, value) {
            state.objects = value;
        },
        currentFrame(state, value) {
            state.currentFrame = value;
        },
        currentState(state, value) {
            state.currentState = value;
        },
        currentObject(state, value) {
            state.currentObject = value;
        },
        currentObjectState(state, value) {
            state.currentObjectState = value;
        },
        idObjectSelected(state, value) {
            state.idObjectSelected = value;
        },
        duration(state, value) {
            state.duration = value;
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
        zipLoaded(state, value) {
            state.zipLoaded = true;
        },
    },
    actions: {
        setDuration(context, duration) {
            context.commit('duration', duration);
            context.commit('totalFrames', duration * 30); // 30fps
        },
        updateFramesRange(context, framesRange) {
            context.commit('framesRange', framesRange);

        },
        objectsTrackerInit(context) {
            context.commit('objectsTracker', new AnnotatedObjectsTracker(context.state.framesManager));
            console.log('objectsTrackerInit ok')
        },
        objectsTrackerAdd(context, annotatedObject) {
            annotatedObject.idObject = context.state.objectsTracker.getLength();
            context.state.objectsTracker.add(annotatedObject);
            context.commit('currentObject',annotatedObject);
            context.commit('idObjectSelected',annotatedObject.idObject);
            context.commit('currentObjectState', 'created');
            context.commit('objectsTrackerState', 'dirty');
            console.log('new Object');
        },
        objectsTrackerPush(context, annotatedObject) { // use to loaded objects
            //annotatedObject.idObject = context.state.objectsTracker.annotatedObjects.length;
            context.state.objectsTracker.add(annotatedObject);
        },
        objectsTrackerClear(context, annotatedObject) {
            context.state.objectsTracker.clear(annotatedObject);

        },
        objectsTrackerClearAll(context) {
            context.state.objectsTracker.clearAll();
        },
        clearAnnotatedObject(context, i) {
            let annotatedObject = context.state.objectsTracker.annotatedObjects[i];
            $(annotatedObject.dom).remove();
            context.state.objectsTracker.remove(i);
            context.commit('objectsTrackerState', 'dirty');
        },
        selectObject(context, idObject) {
            let idObjectSelected = context.state.idObjectSelected;
            if (idObject == idObjectSelected) {
                context.commit('currentObjectState', 'unselected');
                context.commit('currentObject', null);
                context.commit('idObjectSelected', -1);
            } else {
                let annotatedObject = context.getters.annotatedObject(idObject);
                if (annotatedObject) {
                    context.commit('currentObject', annotatedObject);
                    context.commit('idObjectSelected', idObject);
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
        updateObject(context, updatedObject) {
            let idObject = updatedObject.idObject;
            console.log('updating ' + idObject);
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                this.idFrame = updatedObject.idFrame;
                this.frame =  updatedObject.frame;
                this.idFE =  updatedObject.idFE;
                this.fe =  updatedObject.fe;
                this.color =  updatedObject.color;
                context.commit('currentObject', annotatedObject)
                context.commit('idObjectSelected', idObject)
                context.commit('currentObjectState', 'updated')
                context.commit('objectsTrackerState', 'dirty');
            }
        },
        endObject(context) {
            let idObject = context.state.idObjectSelected;
            console.log('end ' + idObject);
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                annotatedObject.endFrame = context.state.currentFrame - 1;
                annotatedObject.dom.style.display = 'none';
                annotatedObject.add(new AnnotatedFrame(context.state.currentFrame, null, true));
                context.commit('currentObject', null)
                context.commit('idObjectSelected', -1)
                context.commit('currentObjectState', 'none')
                context.commit('objectsTrackerState', 'dirty');
            }
        },
        objectBlocked(context) {
            let idObject = context.state.idObjectSelected;
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                let frame = annotatedObject.get(context.state.currentFrame);
                if (frame) {
                    if (!frame.blocked) {
                        frame.blocked = true;
                        frame.isGroundTruth = true;
                        console.log(frame);
                        context.commit('redrawFrame', true)
                    }
                }
            }
        },
        objectVisible(context) {
            let idObject = context.state.idObjectSelected;
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                let frame = annotatedObject.get(context.state.currentFrame);
                if (frame) {
                    if (frame.blocked) {
                        frame.blocked = false;
                        frame.isGroundTruth = true;
                        console.log(frame);
                        context.commit('redrawFrame', true)
                    }
                }
            }
        },
        clearObject(context) {
            let idObject = context.state.idObjectSelected;
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                annotatedObject.removeFrame(context.state.currentFrame);
                context.commit('currentObjectState', 'cleared');
                context.commit('objectsTrackerState', 'dirty');
            }
        },
        deleteObject(context) {
            let idObject = context.state.idObjectSelected;
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                context.dispatch('objectsTrackerClear', annotatedObject);
                context.commit('currentObjectState', 'cleared');
                context.commit('objectsTrackerState', 'dirty');
            }
        },
    },
})

