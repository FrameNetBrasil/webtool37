import Vue from '../../../vue/node_modules/vue/dist/vue.esm.browser.js';
import _store from './_store.js';

//import '../../../vue/node_modules/vx-easyui/dist/themes/default/easyui.css';
//import '../../../vue/node_modules/vx-easyui/dist/themes/icon.css';
//import '../../../vue/node_modules/vx-easyui/dist/themes/vue.css';
//import EasyUI from '../../../vue/node_modules/vx-easyui/dist/vx-easyui-min.js';

let EasyUI = window["vx-easyui"];


Vue.use(EasyUI);

window.vue = new Vue({
    el: "#vapp",
    store: _store,
    data: {},
    methods: {}
})

Vue.component('edit-post', {
    template: `
<form @submit.prevent="savePost">
    <label>Title:</label>
    <input type="text" v-model="post"/><br>
    <label>Description:</label>
    <textarea rows="10" cols="50" v-model="post"></textarea><br>
    <input type="submit">
</form>
`,
    data() {
        return {
            post: 'teste',
        }
    },
    computed: {
        a() {
            return 1;
        }
    },
    methods: {
        savePost() {
            console.log(vue.$store.state.currentTime);
        }
    }
})
