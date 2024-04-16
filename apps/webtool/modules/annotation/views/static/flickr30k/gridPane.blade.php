<script>
    let gridPane = {
        template: '#grid-pane',
        props: [],
        data() {
            return {
                fieldClicked: '',
                currentVideoState: '',
                currentRowSelected: -1,
                objects: [],
            }
        },
        created() {

        },
        mounted: function () {
            this.objects = this.$store.state.objectsTracker.annotatedObjects;
            //
            // watch change currentObject
            //
            this.$store.watch(
                (state, getters) => getters.currentObject,
                (currentObject) => {
                    if (this.currentRowSelected !== -1) {
                        $('#gridObjects').datagrid('refreshRow', this.currentRowSelected);
                    }
                    if (currentObject) {
                        let index = $('#gridObjects').datagrid('getRowIndex', currentObject.idObject);
                        $('#gridObjects').datagrid('scrollTo', index);
                        $('#gridObjects').datagrid('refreshRow', index);
                        this.currentRowSelected = index;
                    } else {
                        this.currentRowSelected = -1;
                    }
                }
            )
            //
            // watch change currentObjectState
            //
            this.$store.watch(
                (state, getters) => getters.currentObjectState,
                (currentObjectState) => {
                    if (currentObjectState === 'updated') {
                        $('#gridObjects').datagrid('refreshRow', this.currentRowSelected);
                        this.$store.commit('currentObjectState', 'selected');
                    }
                }
            )
            //
            // watch objectsTrackerState
            //
            this.$store.watch(
                (state, getters) => getters.objectsTrackerState,
                (objectsTrackerState) => {
                    if (objectsTrackerState === 'dirty') {
                        this.objects = this.$store.state.objectsTracker.annotatedObjects;
                        $('#gridObjects').datagrid({
                            data: this.objects
                        })
                        this.$store.commit('objectsTrackerState', 'clean');
                    }
                }
            )
            let that = this;
            let columns = [
                {
                    field: 'idObjectSentenceMM',
                    hidden: true,
                },
                {
                    field: 'idObjectMM',
                    hidden: true,
                },
                {
                    field: 'idObject',
                    title: '#Object',
                    align: 'right',
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
                    field: 'idFrame',
                    title: 'idFrame',
                    hidden: true,
                },
                {
                    field: 'frame',
                    title: 'Frame',
                    width: 275,
                },
                {
                    field: 'idFE',
                    title: 'idFE',
                    hidden: true,
                },
                {
                    field: 'fe',
                    title: 'FE',
                    width: 275,
                },
                // {
                //     field: 'origin',
                //     title: 'Origin',
                //     width: 100,
                //     formatter: function (value, row, index) {
                //         if (row.origin === '1') {
                //             return "flickr30k";
                //         }
                //         if (row.origin === '2') {
                //             return "manual";
                //         }
                //     },
                // },
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
                    text: 'Add Entity',
                    iconCls: 'fas fa-plus fa16px',
                    handler: function () {
                        let annotatedObject = new AnnotatedObject();
                        that.$store.dispatch('objectsTrackerAdd', annotatedObject);
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
                        that.deleteObjects(toDelete);
                    }
                },
            ];

            $('#gridObjects').datagrid({
                data: that.objects,
                border:1,
                width:660,
                height:310,
                //fit:true,
                //fit:true,
                idField: 'idObject',
                title: 'Entities',
                showHeader: true,
                singleSelect: false,
                columns: [
                    columns
                ],
                rowStyler: function(index,row){
                    let currentObject = that.$store.state.currentObject;
                    if (currentObject && (row)) {
                        if (currentObject.idObject === row.idObject) {
                            return 'background-color:#6293BB;color:#fff;'; // return inline style
                        }
                    }
                },
                onClickRow: function (index, row) {
                    if (that.fieldClicked === 'locked') {
                        that.$store.dispatch('lockObject', row.idObject);
                    } else if (that.fieldClicked === 'hidden') {
                        that.$store.dispatch('hideObject', row.idObject);
                    } else {
                        console.log('select object', row.idObject)
                        that.$store.dispatch('selectObject', row.idObject);
                        that.$store.commit('currentFrame', row.startFrame);
                    }
                },
                onClickCell: function (index, field, value) {
                    that.fieldClicked = field;
                },
                onLoadSuccess: function() {
                    $('#gridObjects').datagrid('sort','idObject');
                    let currentObject = that.$store.state.currentObject;
                    if (currentObject) {
                        let index = $('#gridObjects').datagrid('getRowIndex', currentObject.idObject);
                        $('#gridObjects').datagrid('scrollTo', index);
                    }
                    that.$store.commit('objectsTrackerState', 'clean');
                },
                onBeforeSelect: function () {
                    return false;
                },
            });
        },
        methods: {
            deleteObjects(toDelete) {
                let params = {
                    toDelete: toDelete,
                }
                try {
                    let url = "/index.php/webtool/annotation/static/deleteObjects";
                    manager.doAjax(url, (response) => {
                        if (response.type == 'success') {
                            $.messager.alert('Ok', 'Objects deleted.','info');
                        } else if (response.type == 'error') {
                            throw new Error(response.message);
                        }
                    }, params);

                } catch (e) {
                    $.messager.alert('Error', e.message,'error');
                }
            }
        },
    }

</script>

<script type="text/x-template" id="grid-pane">
    <div ref="gridObjects" id="gridObjects">
    </div>
</script>

