//Vue.use(Vuex)
//console.log(base);
let store = new Vuex.Store({
    state: {
        model: {},
        title: '',
        m4v: '',
        swfPath: '',
        urlLookupFrame: '',
        urlLookupFE: '',
        currentTime: 0,
        currentState: 'paused',
        duration: 0,
        totalFrames: 0,
        annotation: {}
    },
    getters: {
        currentTime(state) {
            return state.currentTime
        },
        currentState(state) {
            return state.currentState
        },
        duration(state) {
            return state.duration
        },
    },
    mutations: {
        fromLatte(state, latte) {
            state.title = latte.title;
            state.m4v = latte.m4v;
            state.swfPath = latte.swfPath;
            state.urlLookupFrame = latte.urlLookupFrame;
            state.urlLookupFE = latte.urlLookupFE;
        },
        currentTime(state, time) {
            state.currentTime = time;
        },
        currentState(state, value) {
            state.currentState = value;
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
        }
    },
    actions: {
        setDuration(context, duration) {
            context.commit('duration', duration);
            context.commit('totalFrames', duration * 30); // 30fps
        }
    },
})

