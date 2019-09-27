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
        annotation: {}
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
        model(state, model) {
            state.model = model;
        },
        annotation(state, annotation) {
            state.annotation = annotation;
        }
    },
    actions: {},
})

