import Vue from '../../../vue/node_modules/vue/dist/vue.esm.browser.js';
import store from './store.js';

window.vue = new Vue({
    el: "#vapp",
    store,
    data: {
    },
    methods: {
    }
})
