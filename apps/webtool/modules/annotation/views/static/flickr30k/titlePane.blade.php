<script>
    let titlePane = {
        template: '#title-pane',
        data() {
            return {
                imageTitle: this.$store.state.model.document.name + ' - ' + this.$store.state.model.imageMM.name,
            }
        },

    }
</script>

<script type="text/x-template" id="title-pane">
    <h3 class="title">@{{imageTitle}}  [flickr30k mode]</h3>
</script>

