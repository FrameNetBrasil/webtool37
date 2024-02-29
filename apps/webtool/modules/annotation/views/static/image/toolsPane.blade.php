@php
    $imageWidth = $data->imageMM->width;
    $rightWidth = $imageWidth;
@endphp
<script>
    let toolsPane = {
        template: '#tools-pane',
        props: [],
        data() {
            return {}
        },
        computed: {},
        methods: {
            onNewObject() {
                let currentObject = this.$store.state.currentObject;
                if (currentObject) {
                    this.$store.dispatch('newObject');
                } else {
                    $.messager.alert('Warning', 'Select an entity.', 'warning');
                }
            },
            onEndObject() {
                this.$store.dispatch('endObject');
            },
            onObjectBlocked() {
                this.$store.dispatch('boxBlocked');
            },
            onObjectVisible() {
                this.$store.dispatch('boxVisible');
            },
            onClearObject() {
                this.$store.dispatch('clearObject');
            },
            onObjectDelete() {
                let currentBox = this.$store.state.currentBox;
                if (currentBox.id < 0) {
                    this.$store.dispatch('boxDelete');
                    $.messager.alert('Ok', 'Box deleted.', 'info');
                } else {
                    try {
                        let params = {
                            idObjectFrameMM: currentBox.id,
                        }
                        let url = "/index.php/webtool/annotation/multimodal/deleteObjectFrame";
                        manager.doAjax(url, (response) => {
                            if (response.type === 'success') {
                                $.messager.alert('Ok', 'Box deleted.', 'info');
                                this.$store.dispatch('boxDelete');
                            } else if (response.type === 'error') {
                                throw new Error(response.message);
                            }
                        }, params);

                    } catch (e) {
                        $.messager.alert('Error', e.message, 'error');
                    }
                }
            },
        },
        mounted: function () {
        },
        watch: {}
    }

</script>

<script type="text/x-template" id="tools-pane">
    <div id="toolsPaneDiv" style="width:{{$imageWidth}}px; display:flex; flex-direction: row;">
        <div id="toolsPaneRight" style="width:{{$rightWidth}}px">
            <LinkButton id="btnObjectVisible" iconCls="faTool far fa-image" :plain="true" @click="onObjectVisible"
                        title="Visible"></LinkButton>
            <LinkButton id="btnObjectBlocked" iconCls="faTool far fa-images" :plain="true" @click="onObjectBlocked"
                        title="Blocked"></LinkButton>
            <!--
            <LinkButton id="btnEndObject" iconCls="faTool fas fa-unlink" :plain="true" @click="onEndObject"
                        title="End Object"></LinkButton>
                        -->
            <LinkButton id="btnObjectDelete" iconCls="faTool fas fa-trash-alt" :plain="true"
                        @click="onObjectDelete"
                        title="Delete Object"></LinkButton>
            <LinkButton id="btnNewObject" iconCls="faTool fas fa-crop" :plain="true" @click="onNewObject"
                        title="New Object"></LinkButton>
        </div>
    </div>
</script>

