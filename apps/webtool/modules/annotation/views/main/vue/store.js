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
        currentState: 'paused',
        objects: [],
        currentObject: null,
        currentObjectState: 'none',// creating, created, selected,editingFE, editingBox, stopping, updated
        idObjectSelected: -1,
        duration: 0,
        totalFrames: 0,
        framesRange: {},
        annotatedObject: null,
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
    },
    actions: {
        setDuration(context, duration) {
            context.commit('duration', duration);
            context.commit('totalFrames', duration * 30); // 30fps
        },
        objectsTrackerInit(context) {
            context.commit('objectsTracker', new AnnotatedObjectsTracker(context.state.framesManager));
            console.log('objectsTrackerInit ok')
        },
        objectsTrackerAdd(context, annotatedObject) {
            annotatedObject.idObject = context.state.objectsTracker.annotatedObjects.length;
            context.state.objectsTracker.annotatedObjects.push(annotatedObject);
            context.commit('currentObject',annotatedObject);
            context.commit('idObjectSelected',annotatedObject.idObject);
            context.commit('currentObjectState', 'created');
            context.commit('objectsTrackerState', 'dirty');
            console.log('new Object');
        },
        objectsTrackerClearAll(context) {
            console.log(context.objectsTracker);
            for (let i = 0; i < context.state.objectsTracker.annotatedObjects.length; i++) {
                context.dispatch('clearAnnotatedObject', i);
            }
        },
        clearAnnotatedObject(context, i) {
            let annotatedObject = context.state.objectsTracker.annotatedObjects[i];
            $(annotatedObject.dom).remove();
            context.state.objectsTracker.annotatedObjects.splice(i, 1);
            context.commit('objectsTrackerState', 'dirty');
        },
        selectObject(context, idObject) {
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                context.commit('currentObject', annotatedObject)
                context.commit('idObjectSelected', idObject)
                context.commit('currentObjectState', 'selected')
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

            setCurrentObject(context, updatedObject) {
            /*
            let objects = context.state.objects;
            for (i in objects) {
                object = objects[i];
                if (object.idObject == updatedObject.idObject) {
                    objects[i] = updatedObject;
                }
            }
            context.commit('objects', objects);

             */
        }
    },
})

