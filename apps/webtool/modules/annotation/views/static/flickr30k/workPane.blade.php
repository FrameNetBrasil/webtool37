<script>
    let workPane = {
        template: '#work-pane',
        components: {
            'tools-pane': toolsPane,
        },
        data() {
            return {}
        },

    }
</script>

<script type="text/x-template" id="work-pane">
    <div>
        <div id="toolsPane">
            <tools-pane></tools-pane>
        </div>
    </div>
</script>

