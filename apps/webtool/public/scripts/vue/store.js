import Vue from '../../../vue/node_modules/vue/dist/vue.esm.browser.js';
import Vuex from '../../../vue/node_modules/vuex/dist/vuex.esm.browser.js';

Vue.use(Vuex)
//console.log(base);
let store = new Vuex.Store({
    state: {
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
        annotation(state, annotation) {
            state.annotation = annotation;
        }
    },
    actions: {},
})
export default store;
