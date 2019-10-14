let store = new Vuex.Store({
    state: {
        model: {},
        title: '',
        m4v: '',
        swfPath: '',
        urlLookupFrame: '',
        urlLookupFE: '',
        currentFrame: 0,
        currentState: 'paused',
        objects: [],
        currentObject: null,
        currentObjectState: 'none',// creating, created, selected,editingFE, editingBox, stopping, updated
        duration: 0,
        totalFrames: 0,
        framesRange: {},
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
        duration(state) {
            return state.duration
        },
        framesRange(state) {
            return state.framesRange
        },
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
    },
    actions: {
        setDuration(context, duration) {
            context.commit('duration', duration);
            context.commit('totalFrames', duration * 30); // 30fps
        },
        setObjects(context, objectsList) {
            let objects = [];
            for (object of objectsList) {
                objects.push(object)
            }
            context.commit('objects', objects);
        }
    },
})

