Vue.component('edit-post', {
    template: `
<form @submit.prevent="savePost">
    <label>Title:</label>
    <input type="text" v-model="post.title"/><br>
    <label>Description:</label>
    <textarea rows="10" cols="50" v-model="post.body"></textarea><br>
    <input type="submit">
</form>
`,
    computed: mapState(['post']),
    methods: {
        savePost() {
            this.$store.dispatch('persistPost')
            this.$root.hideEdit()
        }
    }
})