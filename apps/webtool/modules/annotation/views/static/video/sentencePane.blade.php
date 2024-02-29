<script>
    let sentencePane = {
        template: '#sentence-pane',
        props: [],
        data() {
            return {
                currentState: '',
            }
        },
        computed: {
        },
        created() {
        },
        mounted: function () {
            //
            // watch currentState
            //
            this.$store.watch(
                (state, getters) => getters.currentState,
                (currentState) => {
                    this.currentState = currentState;
                }
            )
            this.currentState = this.$store.state.currentState;

            let that = this;

            let columns = [
                {
                    field: 'idAnnotationSet',
                    hidden:true,
                },
                {
                    field: 'idSentenceMM',
                    hidden:true,
                },
                {
                    field: 'startFrame',
                    title: 'Start Frame',
                    align: 'right',
                    sortable: true,
                    width: 96,
                },
                {
                    field: 'endFrame',
                    title: 'End Frame',
                    align: 'right',
                    width: 96,
                },
                {
                    field: 'startTimestamp',
                    title: 'Start Time (s)',
                    align: 'right',
                    width: 96,
                },
                {
                    field: 'endTimestamp',
                    title: 'End Time (s)',
                    align: 'right',
                    width: 96,
                },
                {
                    field: 'text',
                    title: 'Sentence',
                    align: 'left',
                    width: 660,
                },
                {
                    field: 'play',
                    width: 24,
                    title: '',
                    formatter: function (value, row, index) {
                        return '<i class="fas fa-play"></i>';
                    },
                },
                {
                    field: 'play3',
                    width: 56,
                    title: '',
                    formatter: function (value, row, index) {
                        return '<i class="fas fa-play"></i> +- 3';
                    },
                },
                {
                    field: 'play5',
                    width: 56,
                    title: '',
                    formatter: function (value, row, index) {
                        return '<i class="fas fa-play"></i> +- 5';
                    },
                },
            ];
            $('#gridSentences').datagrid({
                url: manager.baseURL + annotationVideoModel.url.sentences + '/' + annotationVideoModel.documentMM.idDocumentMM,
                method: 'get',
                border:1,
                //fit:true,
                width:1200,
                height:260,
                title: 'Sentences',
                showHeader: true,
                singleSelect: true,
                nowrap: false,
                columns: [
                    columns
                ],
                /*
                onClickRow: function (index, row) {
                    console.log(row);
                    let startFrame = that.frameFromTime(row.startTimestamp);
                    that.$store.commit('currentFrame', startFrame);
                    let idObjectSelected = that.$store.state.idObjectSelected;
                    if (idObjectSelected !== -1) {
                        that.$store.dispatch('selectObject', idObjectSelected);
                    }
                    let currentVideoState = that.$store.state.currentVideoState;
                    if ((currentVideoState == 'playing') || (currentVideoState == 'loaded')) {
                        that.$store.commit('currentVideoState', 'paused');
                    }
                },

                 */
                onClickCell: function (index, field, value) {
                    let currentMode = that.$store.state.currentMode;
                    let currentState = that.$store.state.currentState;
                    if (currentMode === 'video') {
                        if (currentState === 'paused') {
                            console.log(index, field, value);
                            let rows = $('#gridSentences').datagrid('getRows');
                            let row = rows[index];
                            let currentState = that.$store.state.currentState;
                            if ((currentState === 'playing')) {
                                that.$store.commit('currentState', 'paused');
                            }
                            console.log('state = ' + currentState);
                            if (field === 'startTimestamp') {
                                let startFrame = that.frameFromTime(value);
                                that.$store.commit('currentFrame', startFrame);
                                let idObjectSelected = that.$store.state.idObjectSelected;
                                if (idObjectSelected !== -1) {
                                    that.$store.dispatch('selectObject', idObjectSelected);
                                }
                            }
                            if (field === 'startFrame') {
                                that.$store.commit('currentFrame', value);
                                let idObjectSelected = that.$store.state.idObjectSelected;
                                if (idObjectSelected !== -1) {
                                    that.$store.dispatch('selectObject', idObjectSelected);
                                }
                            }
                            if (field === 'endTimestamp') {
                                let startFrame = that.frameFromTime(value);
                                that.$store.commit('currentFrame', startFrame);
                                let idObjectSelected = that.$store.state.idObjectSelected;
                                if (idObjectSelected !== -1) {
                                    that.$store.dispatch('selectObject', idObjectSelected);
                                }
                            }
                            if (field === 'endFrame') {
                                that.$store.commit('currentFrame', value);
                                let idObjectSelected = that.$store.state.idObjectSelected;
                                if (idObjectSelected !== -1) {
                                    that.$store.dispatch('selectObject', idObjectSelected);
                                }
                            }
                            if (field === 'text') {
                                let startFrame = that.frameFromTime(row.startTimestamp);
                                that.$store.commit('currentFrame', startFrame);
                                let idObjectSelected = that.$store.state.idObjectSelected;
                                if (idObjectSelected !== -1) {
                                    that.$store.dispatch('selectObject', idObjectSelected);
                                }
                            }
                            if (field === 'play') {
                                let startFrame = that.frameFromTime(row.startTimestamp);
                                that.$store.commit('currentFrame', startFrame);
                                let idObjectSelected = that.$store.state.idObjectSelected;
                                if (idObjectSelected !== -1) {
                                    that.$store.dispatch('selectObject', idObjectSelected);
                                }
                                let stopFrame = that.frameFromTime(row.endTimestamp);
                                that.$store.commit('currentStopFrame', stopFrame);
                                that.$store.commit('currentState', 'playing');
                            }
                            if (field === 'play3') {
                                let startFrame = that.frameFromTime(row.startTimestamp);
                                let endFrame = that.frameFromTime(row.endTimestamp);
                                let start = startFrame - (annotationVideoModel.fps * 3)
                                let stop = endFrame + (annotationVideoModel.fps * 3);
                                that.$store.commit('currentFrame', start);
                                let idObjectSelected = that.$store.state.idObjectSelected;
                                if (idObjectSelected !== -1) {
                                    that.$store.dispatch('selectObject', idObjectSelected);
                                }
                                that.$store.commit('currentStopFrame', stop);
                                that.$store.commit('currentState', 'playing');
                            }
                            if (field === 'play5') {
                                let startFrame = that.frameFromTime(row.startTimestamp);
                                let endFrame = that.frameFromTime(row.endTimestamp);
                                let start = startFrame - (annotationVideoModel.fps * 5)
                                let stop = endFrame + (annotationVideoModel.fps * 5);
                                that.$store.commit('currentFrame', start);
                                let idObjectSelected = that.$store.state.idObjectSelected;
                                if (idObjectSelected !== -1) {
                                    that.$store.dispatch('selectObject', idObjectSelected);
                                }
                                that.$store.commit('currentStopFrame', stop);
                                that.$store.commit('currentState', 'playing');
                            }
                        }
                    }
                },
                //onBeforeSelect: function () {
                //    return false;
                //},
            });
            $('#gridSentences').datagrid('getPanel').panel('panel').attr('tabindex',1).unbind('keydown');
        },
        methods: {
            frameFromTime(timeSeconds) {
                return parseInt(timeSeconds * annotationVideoModel.fps) + 1;
            },
        },
    }

</script>

<script type="text/x-template" id="sentence-pane">
    <div ref="gridSentences" id="gridSentences">
    </div>
</script>

