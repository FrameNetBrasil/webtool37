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
                console.log('gridPane watch currentObject', currentObject)
                if (currentObject) {
                    if (this.currentRowSelected !== -1) {
                        $('#gridObjects').datagrid('refreshRow', this.currentRowSelected);
                    }
                    let index = $('#gridObjects').datagrid('getRowIndex', currentObject.idObject);
                    console.log('index = ', index);
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

        // watch updateGridPane

        this.$store.watch(
            (state, getters) => getters.updateGridPane,
            async (updateGridPane) => {
                console.log('-- gridPane watch updateGridGrane', updateGridPane)
                if (updateGridPane) {
                    let objects = await dynamicObjects.loadObjectsFromDb();
                    $('#gridObjects').datagrid('loadData', objects)
                    if (annotationVideoModel.currentIdObjectMM > 0) {
                        dynamicStore.dispatch('selectObjectMM', annotationVideoModel.currentIdObjectMM);
                    } else {
                        this.$store.commit('currentObject', null);
                    }
                    this.$store.commit('updateGridPane', false);
                }
            }
        )

        //
        // watch objectsTrackerState
        //
        // this.$store.watch(
        //     (state, getters) => getters.objectsTrackerState,
        //     (objectsTrackerState) => {
        //         console.log('-- gridPane watch objectsTrackerState', objectsTrackerState)
        //         if (objectsTrackerState === 'dirty') {
        //             console.log('grid Object updated objectsTrackerState', objectsTrackerState);
        //             this.objects = dynamicObjects.tracker.annotatedObjects;
        //             $('#gridObjects').datagrid({
        //                 data: this.objects
        //             })
        //             this.$store.commit('objectsTrackerState', 'clean');
        //         }
        //     }
        // )
        //
        // watch currentState
        //
        // this.$store.watch(
        //     (state, getters) => getters.currentState,
        //     (currentState) => {
        //         this.currentState = currentState;
        //     }
        // )

        this.currentState = this.$store.state.currentState;

        let that = this;
        let columns = [
            {
                field: 'chkObject',
                checkbox: true,
            },
            // {
            //     field: 'locked',
            //     width: 24,
            //     title: '<i class="fas fa-lock"></i>',
            //     formatter: function (value, row, index) {
            //         let icon = (value ? 'fas fa-lock' : 'fas fa-unlock');
            //         return "<i style='color:black' class='" + icon + "'></i>";
            //     },
            // },
            {
                field: 'hidden',
                width: 24,
                title: '<i class="fas fa-eye"></i>',
                formatter: function (value, row, index) {
                    if (value) {
                        // let color = (value ? '#CCC' : 'black');
                        // return "<i style='color:" + color + "' class='material-outlined wt-icon-show'></i>";
                        return "<i class='material-outlined wt-icon-hide' style='cursor:pointer'></i>";
                    } else {
                        return "<i class='material-outlined wt-icon-show' style='cursor:pointer'></i>";
                    }
                },
            },
            {
                field: 'idObjectClone',
                width: 24,
                title: '<i class="faTool material wt-icon-clone"></i>',
                formatter: function (value, row, index) {
                   return "<i class='material-outlined wt-icon-clone' style='cursor:pointer'></i>";
                },
            },
            {
                field: 'idObject',
                title: '#',
                align: 'right',
                width: 56,
            },
            {
                field: 'idFrame',
                title: 'idFrame',
                hidden: true,
            },
            {
                field: 'idFE',
                title: 'idFE',
                hidden: true,
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
                field: 'start',
                title: 'Start Frame [Time]',
                align: 'right',
                sortable: true,
                width: 112,
                formatter: function (value, row, index) {
                    return "<span  class='gridPaneFrame'>" + row.startFrame + " [" + row.startTime + "s]" + "</span>";
                },
            },
            {
                field: 'end',
                title: 'End Frame [Time]',
                align: 'right',
                width: 112,
                formatter: function (value, row, index) {
                    return "<span  class='gridPaneFrame'>" + row.endFrame + " [" + row.endTime + "s]" + "</span>";
                },
            },
            {
                field: 'frameFe',
                title: 'FrameNet Frame.FE',
                width: 185,
                formatter: function (value, row, index) {
                    return (row.frame !== '') ? "<span  class='gridPaneFrameFE'>" + row.frame + "." + row.fe + "</span>" : '';
                },
            },
            {
                field: 'lu',
                title: 'CV_Name (LU)',
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
                title: "<i class='material-outlined wt-icon-status'></i>",
                formatter: function (value, row, index) {
                    //console.log(row);
                    //if ((row.idFE != null) && (row.idFE !== -1)&& (row.idFE !== '')) {
                    if (row.idFE !== "") {
                        return "<i style='color:green' class='fas fa-check'></i>";
                    } else {
                        return "<i style='color:gold' class='fas fa-exclamation-triangle'></i>";
                    }
                },
            },
            {
                field: 'idDynamicObjectMM',
                title: 'idObjectMM',
                sortable: true,
            },
        ];
        let toolbar = [
            {
                text: 'Hide All',
                iconCls: 'faTool material-outlined wt-icon-hide',
                handler: function () {
                    var rows = $('#gridObjects').datagrid('getRows');
                    $.each(rows, function (index, row) {
                        that.$store.dispatch('hideObject', row.idObject);
                        row.hidden = true;
                        $('#gridObjects').datagrid('refreshRow', index);
                    });
                }
            },
            {
                text: 'Show All',
                iconCls: 'faTool material-outlined wt-icon-show',
                handler: function () {
                    var rows = $('#gridObjects').datagrid('getRows');
                    $.each(rows, function (index, row) {
                        that.$store.dispatch('showObject', row.idObject);
                        row.hidden = false;
                        $('#gridObjects').datagrid('refreshRow', index);
                    });
                }
            },
            {
                text: 'Delete checked',
                iconCls: 'faTool material wt-icon-delete',
                handler: async function () {
                    var toDelete = [];
                    var checked = $('#gridObjects').datagrid('getChecked');
                    $.each(checked, function (index, row) {
                        // console.log(row);
                        // that.$store.dispatch('deleteObjectById', row.idObject);
                        toDelete.push(row.idObjectMM);
                    });
                    // that.$store.commit('objectsTrackerState', 'dirty');
                    await dynamicAPI.deleteObjects(toDelete);
                    annotationVideoModel.currentIdObjectMM = -1;
                    that.$store.commit('updateGridPane', true)
                    that.$store.commit('currentObject', null)
                    that.$store.commit('currentState', 'videoPaused')
                    $.messager.alert('Ok', 'Objects deleted.','info');
                }
            },
            // {
            //     text: 'Duplicate checked',
            //     iconCls: 'faTool material wt-icon-clone',
            //     handler: function () {
            //         var toDuplicate = [];
            //         var checked = $('#gridObjects').datagrid('getChecked');
            //         $.each(checked, function (index, row) {
            //             toDuplicate.push(row);
            //         });
            //         that.duplicateObjects(that, toDuplicate);
            //     }
            // },
        ];

        $('#gridObjects').datagrid({
            data: that.objects,
            border: 1,
            // width: 1200,
            // height: 544,
            fit: true,
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
                //let currentMode = that.$store.state.currentMode;
                let currentState = that.$store.state.currentState;
                //if (currentMode === 'video') {
                    if (currentState === 'videoPaused') {
                        if (that.fieldClicked === 'locked') {
                            that.$store.dispatch('lockObject', row.idObject);
                        } else if (that.fieldClicked === 'hidden') {
                            if (row.hidden) {
                                that.$store.dispatch('showObject', row.idObject);
                                row.hidden = false;
                            } else {
                                that.$store.dispatch('hideObject', row.idObject);
                                row.hidden = true;
                            }
                            $('#gridObjects').datagrid('refreshRow', index);
                            that.$store.commit('redrawFrame', true);
                        } else if (that.fieldClicked === 'idObjectClone') {
                            that.duplicateObjects(that, [row.idObject])
                        } else if (that.fieldClicked === 'start') {
                            that.$store.commit('currentFrame', row.startFrame);
                            that.$store.dispatch('selectObject', row.idObject);
                        } else if (that.fieldClicked === 'end') {
                            that.$store.commit('currentFrame', row.endFrame);
                            that.$store.dispatch('selectObject', row.idObject);
                        } else {
                            that.$store.commit('currentFrame', row.startFrame);
                            that.$store.dispatch('selectObject', row.idObject);
                        }
                    }
                //}
            },
            onClickCell: function (index, field, value) {
                // let currentMode = that.$store.state.currentMode;
                let currentState = that.$store.state.currentState;
                // if (currentMode === 'video') {
                    if (currentState === 'videoPaused') {
                        that.fieldClicked = field;
                    }
                // }
            },
            // onLoadSuccess: function (data) {
            //     let currentObject = that.$store.state.currentObject;
            //     if (currentObject) {
            //         let index = $('#gridObjects').datagrid('getRowIndex', currentObject.idObject);
            //         $('#gridObjects').datagrid('scrollTo', index);
            //     }
            // },
            onBeforeSelect: function () {
                return false;
            },
        });
        $('#gridObjects').datagrid('getPanel').panel('panel').attr('tabindex', 1).unbind('keydown');
    },
    methods: {
        async duplicateObjects(that, toDuplicate) {
            let idObjectSource = toDuplicate[0];
            let sourceObject = dynamicObjects.get(idObjectSource)
            let cloneObject = new AnnotatedObject();
            cloneObject.cloneFrom(sourceObject);
            console.log(sourceObject, cloneObject)
            await dynamicObjects.saveObject(cloneObject, {
                idObjectMM: null,
                idDocumentMM: annotationVideoModel.documentMM.idDocumentMM,
                startFrame: cloneObject.startFrame,
                endFrame: cloneObject.endFrame,
                idFrame:null,
                idFrameElement: null,
                idLU: null,
                startTime: (cloneObject.startFrame - 1) / annotationVideoModel.fps,
                endTime:(cloneObject.endFrame - 1) / annotationVideoModel.fps,
            });
            $.messager.alert('Confirmation', 'Object #' + idObjectSource + ' duplicated.', 'info');


            // let newCurrentObject = null;
            // let msg = '';
            // $.each(toDuplicate, function (index, sourceAnnotatedObject) {
            //     console.log(sourceAnnotatedObject);
            //     let annotatedObject = new AnnotatedObject();
            //     annotatedObject.cloneFrom(sourceAnnotatedObject);
            //     //that.$store.dispatch('objectsTrackerAdd', annotatedObject);
            //     annotationVideoModel.objectsTracker.add(annotatedObject);
            //     newCurrentObject = annotatedObject;
            //     msg = msg + ' #' + annotatedObject.idObject;
            // });
            // this.objects = annotationVideoModel.objectsTracker.tracker.annotatedObjects;
            // $('#gridObjects').datagrid({
            //     data: this.objects
            // })
            // $.messager.alert('Confirmation', 'Object(s) ' + msg + ' created.', 'info');
        }
    }
}
