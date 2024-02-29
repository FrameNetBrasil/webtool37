<script>
    let workPane = {
        template: '#work-pane',
        components: {
            'time-pane': timePane,
            'tools-pane': toolsPane,
        },
        data() {
            return {}
        },
        methods: {
            onShowBoxes() {
                console.log('onshowboxes2', this.currentVideoState);
                this.$emit('event-showboxes');
            },
        }
    }
</script>

<script type="text/x-template" id="work-pane">
    <div>
        <div id="timePane">
            <time-pane></time-pane>
        </div>
        <div id="toolsPane">
            <tools-pane @event-showboxes="onShowBoxes"></tools-pane>
        </div>
    </div>
</script>

