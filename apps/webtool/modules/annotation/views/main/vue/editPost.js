Vue.component('edit-post', {
        template: '#edit-post',
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
    }
);


