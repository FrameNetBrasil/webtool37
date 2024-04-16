<script>
    let gridPane = {
        template: '#grid-pane',
        props: [],
        data() {
            return {
                fieldClicked: '',
                currentVideoState: '',
                currentState: '',
                currentRowSelected: -1,
                objects: [],
            }
        },
        created() {

        },
        mounted: function () {
            //
            // watch change currentObject
            //
            this.$store.watch(
                (state, getters) => getters.currentObject,
                (currentObject) => {
                    if (currentObject) {
                        if (this.currentRowSelected !== -1) {
                            $('#gridObjects').datagrid('refreshRow', this.currentRowSelected);
                        }
                        let index = $('#gridObjects').datagrid('getRowIndex', currentObject.idObject);
                        $('#gridObjects').datagrid('scrollTo', index);
                        $('#gridObjects').datagrid('refreshRow', index);
                        this.currentRowSelected = index;
                    } else {
                        $('#gridObjects').datagrid('unselectRow', this.currentRowSelected);
                        $('#gridObjects').datagrid('refreshRow', this.currentRowSelected);
                        this.currentRowSelected = -1;
                    }
                    this.$store.commit('currentRowGrid', this.currentRowSelected);
                }
            )
            //
            // watch change currentObjectState
            //
            // this.$store.watch(
            //     (state, getters) => getters.currentObjectState,
            //     (currentObjectState) => {
            //         console.log('gridPane ', currentObjectState);
            //         if (currentObjectState === 'updated') {
            //             console.log('grid Pane refreshing row');
            //             console.log(this.currentRowSelected);
            //             $('#gridObjects').datagrid('refreshRow', this.currentRowSelected);
            //         }
            //     }
            // )
            //
            // watch change currentObjectStateFlag
            //
            // this.$store.watch(
            //     (state, getters) => getters.currentObjectStateFlag,
            //     (currentObjectStateFlag) => {
            //         let currentObjectState = this.$store.state.currentObjectState;
            //         //let currentObject = this.$store.state.currentObject;
            //         //console.log('object pane currentObjectStateFlag = ' + currentObjectStateFlag, currentObjectState)
            //         if ((currentObjectState === 'editingBox') || (currentObjectState === 'updated')) {
            //             console.log('#######################################');
            //             console.log('grid Pane refreshing row');
            //             console.log(this.currentRowSelected);
            //             $('#gridObjects').datagrid('refreshRow', this.currentRowSelected);
            //         }
            //     }
            // )


            //
            // watch objectsTrackerState
            //
            this.$store.watch(
                (state, getters) => getters.objectsTrackerState,
                (objectsTrackerState) => {
                    if (objectsTrackerState === 'dirty') {
                        console.log('grid Object updated objectsTrackerState', objectsTrackerState);
                        this.objects = annotationVideoModel.objectsTracker.tracker.annotatedObjects;
                        $('#gridObjects').datagrid({
                            data: this.objects
                        })
                        this.$store.commit('objectsTrackerState', 'clean');
                    }
                }
            )
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
                    field: 'idObjectMM',
                    hidden: true,
                },
                {
                    field: 'chkObject',
                    checkbox: true,
                },
                {
                    field: 'locked',
                    width: 24,
                    title: '<i class="fas fa-lock"></i>',
                    formatter: function (value, row, index) {
                        let icon = (value ? 'fas fa-lock' : 'fas fa-unlock');
                        return "<i style='color:black' class='" + icon + "'></i>";
                    },
                },
                {
                    field: 'hidden',
                    width: 24,
                    title: '<i class="fas fa-eye"></i>',
                    formatter: function (value, row, index) {
                        let color = (value ? '#CCC' : 'black');
                        return "<i style='color:" + color + "' class='fas fa-eye'></i>";
                    },
                },
                {
                    field: 'idObject',
                    title: '#',
                    align: 'right',
                    sortable: true,
                    width: 56,
                },
                {
                    field: 'tag',
                    width: 24,
                    title: '<i class="fas fa-tag"></i>',
                    formatter: function (value, row, index) {
                        return "<i style='color:" + row.color + "' class='fas fa-tag'></i>";
                    },
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
                    field: 'idFrame',
                    title: 'idFrame',
                    hidden: true,
                },
                {
                    field: 'frame',
                    title: 'FrameNet Frame',
                    width: 185,
                },
                {
                    field: 'idFE',
                    title: 'idFE',
                    hidden: true,
                },
                {
                    field: 'fe',
                    title: 'FE',
                    width: 185,
                },
                {
                    field: 'lu',
                    title: 'CV_Name',
                    //hidden: true,
                    width: 130,
                },
                {
                    field: 'origin',
                    title: 'Origin',
                    width: 100,
                    formatter: function (value, row, index) {
                        if (row.origin === '1') {
                            return "yolo";
                        }
                        if (row.origin === '2') {
                            return "manual";
                        }
                    },
                },
                {
                    field: 'status',
                    width: 24,
                    title: '<i class="fas fa-info"></i>',
                    formatter: function (value, row, index) {
                        //console.log(row);
                        if ((row.idFE != null) && (row.idFE != -1)) {
                            return "<i style='color:green' class='fas fa-check'></i>";
                        } else {
                            return "<i style='color:gold' class='fas fa-exclamation-triangle'></i>";
                        }
                    },
                },
            ];
            let toolbar = [
                {
                    text: 'Hide All',
                    iconCls: 'fas fa-eye fa16px',
                    handler: function () {
                        var rows = $('#gridObjects').datagrid('getRows');
                        $.each(rows, function (index, row) {
                            if (!row.hidden) {
                                that.$store.dispatch('hideObject', row.idObject);
                            }
                        });
                    }
                },
                {
                    text: 'Show All',
                    iconCls: 'fas fa-eye fa16px',
                    handler: function () {
                        var rows = $('#gridObjects').datagrid('getRows');
                        $.each(rows, function (index, row) {
                            if (row.hidden) {
                                that.$store.dispatch('hideObject', row.idObject);
                            }
                        });
                    }
                },
                {
                    text: 'Delete checked',
                    iconCls: 'fas fa-trash-alt fa16px',
                    handler: function () {
                        var toDelete = [];
                        var checked = $('#gridObjects').datagrid('getChecked');
                        $.each(checked, function (index, row) {
                            console.log(row);
                            that.$store.dispatch('deleteObjectById', row.idObject);
                            toDelete.push(row.idObjectMM);
                        });
                        that.$store.commit('objectsTrackerState', 'dirty');
                        annotationVideoAPI.deleteObjects(toDelete);
                    }
                },
                {
                    text: 'Duplicate checked',
                    iconCls: 'fas fa fa-files-o fa16px',
                    handler: function () {
                        var toDuplicate = [];
                        var checked = $('#gridObjects').datagrid('getChecked');
                        $.each(checked, function (index, row) {
                            toDuplicate.push(row);
                        });
                        that.duplicateObjects(that, toDuplicate);
                    }
                },
            ];

            $('#gridObjects').datagrid({
                data: that.objects,
                border: 1,
                width: 1200,
                height: 544,
                //fit:true,
                idField: 'idObject',
                title: 'Objects',
                showHeader: true,
                singleSelect: false,
                toolbar: toolbar,
                columns: [
                    columns
                ],
                rowStyler: function (index, row) {
                    let currentObject = that.$store.state.currentObject;
                    if (currentObject && (row)) {
                        if (currentObject.idObject === row.idObject) {
                            return 'background-color:#6293BB;color:#fff;'; // return inline style
                        }
                    }
                },
                onClickRow: function (index, row) {
                    let currentMode = that.$store.state.currentMode;
                    let currentState = that.$store.state.currentState;
                    if (currentMode === 'video') {
                        if (currentState === 'paused') {
                            if (that.fieldClicked === 'locked') {
                                that.$store.dispatch('lockObject', row.idObject);
                            } else if (that.fieldClicked === 'hidden') {
                                that.$store.dispatch('hideObject', row.idObject);
                            } else if (that.fieldClicked === 'startFrame') {
                                that.$store.commit('currentFrame', row.startFrame);
                                that.$store.dispatch('selectObject', row.idObject);
                            } else if (that.fieldClicked === 'endFrame') {
                                that.$store.commit('currentFrame', row.endFrame);
                                that.$store.dispatch('selectObject', row.idObject);
                            } else {
                                that.$store.commit('currentFrame', row.startFrame);
                                that.$store.dispatch('selectObject', row.idObject);
                            }
                        }
                    }
                },
                onClickCell: function (index, field, value) {
                    let currentMode = that.$store.state.currentMode;
                    let currentState = that.$store.state.currentState;
                    if (currentMode === 'video') {
                        if (currentState === 'paused') {
                            that.fieldClicked = field;
                        }
                    }
                },
                onLoadSuccess: function () {
                    $('#gridObjects').datagrid('sort', 'idObject');
                    let currentObject = that.$store.state.currentObject;
                    if (currentObject) {
                        let index = $('#gridObjects').datagrid('getRowIndex', currentObject.idObject);
                        $('#gridObjects').datagrid('scrollTo', index);
                    }
                },
                onBeforeSelect: function () {
                    return false;
                },
            });
            $('#gridObjects').datagrid('getPanel').panel('panel').attr('tabindex', 1).unbind('keydown');
        },
        methods: {
            duplicateObjects(that, toDuplicate) {
                let newCurrentObject = null;
                let msg = '';
                $.each(toDuplicate, function (index, sourceAnnotatedObject) {
                    console.log(sourceAnnotatedObject);
                    let annotatedObject = new AnnotatedObject();
                    annotatedObject.cloneFrom(sourceAnnotatedObject);
                    //that.$store.dispatch('objectsTrackerAdd', annotatedObject);
                    annotationVideoModel.objectsTracker.add(annotatedObject);
                    newCurrentObject = annotatedObject;
                    msg = msg + ' #' + annotatedObject.idObject;
                });
                this.objects = annotationVideoModel.objectsTracker.tracker.annotatedObjects;
                $('#gridObjects').datagrid({
                    data: this.objects
                })
                $.messager.alert('Confirmation','Object(s) ' + msg + ' created.','info');
            }
        }
    }

</script>

<script type="text/x-template" id="grid-pane">
    <div ref="gridObjects" id="gridObjects">
    </div>
</script>

