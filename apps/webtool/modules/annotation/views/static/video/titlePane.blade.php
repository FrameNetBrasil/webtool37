<script>
    let titlePane = {
        template: '#title-pane',
        data() {
            return {
                videoTitle: annotationVideoModel.documentMM.title,
            }
        },

    }
</script>

<script type="text/x-template" id="title-pane">
    <h3 class="title">@{{videoTitle}}</h3>
</script>

