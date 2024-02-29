let sentencePane = {
    template: '#sentence-pane',
    props: [],
    data() {
        return {
            sentences: []
        }
    },
    created() {
        this.sentences = dynamicAPI.listSentences(annotationVideoModel.documentMM.idDocumentMM);
    },
    computed: {
        currentState: function () {
            return (this.$store.state.currentState);
        },
    },
    mounted: function () {
        let that = this;
        let columns = [
            {
                field: 'idAnnotationSet',
                hidden: true,
            },
            {
                field: 'idDynamicSentenceMM',
                hidden: true,
            },
            {
                field: 'start',
                title: 'Start Frame [Time]',
                align: 'right',
                sortable: true,
                width: 112,
            },
            {
                field: 'end',
                title: 'End Frame [Time]',
                align: 'right',
                width: 112,
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
            // {
            //     field: 'startTimestamp',
            //     title: 'Start Time (s)',
            //     align: 'right',
            //     width: 96,
            // },
            // {
            //     field: 'endTimestamp',
            //     title: 'End Time (s)',
            //     align: 'right',
            //     width: 96,
            // },
            {
                field: 'text',
                title: 'Sentence',
                align: 'left',
                width: 900,
            },
        ];
        $('#gridSentences').datagrid({
            //url: manager.baseURL + 'webtool/annotation/dynamic/sentences/' + annotationVideoModel.documentMM.idDocumentMM,
            data: that.sentences,
            method: 'get',
            border: 1,
            fit: true,
            // width:1200,
            //height:260,
            loadMsg: "Loading sentences",
            title: 'Sentences',
            showHeader: true,
            singleSelect: true,
            nowrap: false,
            columns: [
                columns
            ],
            onClickCell: function (index, field, value) {
                let currentState = that.$store.state.currentState;
                if (currentState === 'videoPaused') {
                    console.log(index, field, value);
                    let rows = $('#gridSentences').datagrid('getRows');
                    let row = rows[index];
                    if (field === 'startTimestamp') {
                        let startFrame = that.frameFromTime(value);
                        that.$store.commit('currentFrame', startFrame);
                        // let objectSelected = that.$store.state.currentObject;
                        // if (objecidObjectSelected !== -1) {
                        //     that.$store.dispatch('selectObject', idObjectSelected);
                        // }
                    }
                    if (field === 'startFrame') {
                        that.$store.commit('currentFrame', value);
                        // let idObjectSelected = that.$store.state.idObjectSelected;
                        // if (idObjectSelected !== -1) {
                        //     that.$store.dispatch('selectObject', idObjectSelected);
                        // }
                    }
                    if (field === 'endTimestamp') {
                        let startFrame = that.frameFromTime(value);
                        that.$store.commit('currentFrame', startFrame);
                        // let idObjectSelected = that.$store.state.idObjectSelected;
                        // if (idObjectSelected !== -1) {
                        //     that.$store.dispatch('selectObject', idObjectSelected);
                        // }
                    }
                    if (field === 'endFrame') {
                        that.$store.commit('currentFrame', value);
                        // let idObjectSelected = that.$store.state.idObjectSelected;
                        // if (idObjectSelected !== -1) {
                        //     that.$store.dispatch('selectObject', idObjectSelected);
                        // }
                    }
                    if (field === 'text') {
                        let startFrame = that.frameFromTime(row.startTimestamp);
                        that.$store.commit('currentFrame', startFrame);
                        // let idObjectSelected = that.$store.state.idObjectSelected;
                        // if (idObjectSelected !== -1) {
                        //     that.$store.dispatch('selectObject', idObjectSelected);
                        // }
                    }
                    if (field === 'play') {
                        let startFrame = that.frameFromTime(row.startTimestamp);
                        that.$store.commit('currentFrame', startFrame);
                        // let idObjectSelected = that.$store.state.idObjectSelected;
                        // if (idObjectSelected !== -1) {
                        //     that.$store.dispatch('selectObject', idObjectSelected);
                        // }
                        let stopFrame = that.frameFromTime(row.endTimestamp);
                        that.$store.commit('currentStopFrame', stopFrame);
                        that.$store.commit('currentState', 'videoPlaying');
                    }
                    if (field === 'play3') {
                        let startFrame = that.frameFromTime(row.startTimestamp);
                        let endFrame = that.frameFromTime(row.endTimestamp);
                        let start = startFrame - (annotationVideoModel.fps * 3)
                        let stop = endFrame + (annotationVideoModel.fps * 3);
                        that.$store.commit('currentFrame', start);
                        // let idObjectSelected = that.$store.state.idObjectSelected;
                        // if (idObjectSelected !== -1) {
                        //     that.$store.dispatch('selectObject', idObjectSelected);
                        // }
                        that.$store.commit('currentStopFrame', stop);
                        that.$store.commit('currentState', 'videoPlaying');
                    }
                    if (field === 'play5') {
                        let startFrame = that.frameFromTime(row.startTimestamp);
                        let endFrame = that.frameFromTime(row.endTimestamp);
                        let start = startFrame - (annotationVideoModel.fps * 5)
                        let stop = endFrame + (annotationVideoModel.fps * 5);
                        that.$store.commit('currentFrame', start);
                        // let idObjectSelected = that.$store.state.idObjectSelected;
                        // if (idObjectSelected !== -1) {
                        //     that.$store.dispatch('selectObject', idObjectSelected);
                        // }
                        that.$store.commit('currentStopFrame', stop);
                        that.$store.commit('currentState', 'videoPlaying');
                    }
                }
            },
        });
        $('#gridSentences').datagrid('getPanel').panel('panel').attr('tabindex', 1).unbind('keydown');
    },
    methods: {
        frameFromTime(timeSeconds) {
            return parseInt(timeSeconds * annotationVideoModel.fps) + 1;
        },
    },
}

