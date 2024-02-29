<script>
    let boxPane = {
        template: '#box-pane',
        data() {
            return {
                currentBox: null,
                title: 'Boxes',
                boxesData: [],
                currentRowSelected: -1,
            }
        },
        computed: {},
        created: function () {
        },
        mounted: function () {
            this.boxesData = this.getBoxesData();
            //
            // watch boxesState
            //
            this.$store.watch(
                (state, getters) => getters.boxesState,
                (boxesState) => {
                    if (boxesState === 'dirty') {
                        let boxesData = this.getBoxesData();
                        $('#gridBoxes').datagrid({
                            data: boxesData
                        })

                    }
                }
            )
            //
            // watch change currentBox
            //
            this.$store.watch(
                (state, getters) => getters.currentBox,
                (currentBox) => {
                    this.currentBox = currentBox;
                    if (this.currentRowSelected !== -1) {
                        $('#gridBoxes').datagrid('refreshRow', this.currentRowSelected);
                    }
                    if (currentBox) {
                        let index = $('#gridBoxes').datagrid('getRowIndex', currentBox.id);
                        $('#gridBoxes').datagrid('scrollTo', index);
                        $('#gridBoxes').datagrid('refreshRow', index);
                        this.currentRowSelected = index;
                    } else {
                        this.currentRowSelected = -1;
                    }
                }
            )

            this.$nextTick(() => {
                let that = this;
                $('#boxPane').panel({
                    border: 1,
                    title: that.title,
                    height: 260
                });

                $('#gridBoxes').datagrid({
                    data: that.boxesData,
                    border: 0,
                    showHeader: true,
                    idField: 'id',
                    singleSelect: true,
                    fit: true,
                    rowStyler: function (index, row) {
                        let style = 'background-color:white;';
                        if (that.currentBox && row) {
                            if (that.currentBox.id === row.id) {
                                style = 'background-color:#FFFFE0;'
                            }
                        }
                        return style;
                    },
                    onLoadSuccess: function() {console.log('cleaning boxes state');
                        that.$store.commit('boxesState', 'clean');
                    },
                    onClickRow: function (index, row) {
                        that.$store.dispatch('selectBox', row.box);
                    },
                    columns: [[
                        {
                            field: 'id',
                            hidden: true,
                        },
                        {
                            field: 'idObject',
                            width: 56,
                            title: 'Entity',
                        },
                        {
                            field: 'x',
                            width: 40,
                            title: 'x',
                        },
                        {
                            field: 'y',
                            width: 40,
                            title: 'y',
                        },
                        {
                            field: 'height',
                            width: 48,
                            title: 'Height',
                        },
                        {
                            field: 'width',
                            width: 48,
                            title: 'Width',
                        },
                        {
                            field: 'blocked',
                            width: 56,
                            title: 'Blocked',
                        },
                        {
                            field: 'status',
                            width: 24,
                            title: '<i class="fas fa-info"></i>',
                            formatter: function (value, row, index) {
                                if ((row.id > 0) && (row.status === 1)) {
                                    return "<i style='color:green' class='fas fa-check'></i>";
                                } else {
                                    return "<i style='color:gold' class='fas fa-exclamation-triangle'></i>";
                                }
                            },
                        },
                    ]],
                });

            });

        },
        methods: {
            getBoxesData() {
                let data = [];
                let objects = this.$store.state.objectsTracker.annotatedObjects;
                for (var object of objects) {
                    if (object.boxes.length > 0) {
                        for (box of object.boxes) {
                            if (box.bbox !== null) {
                                data.push({
                                    id: box.id,
                                    idObject: box.idObject,
                                    x: box.bbox.x,
                                    y: box.bbox.y,
                                    width: box.bbox.width,
                                    height: box.bbox.height,
                                    blocked: box.blocked,
                                    status: box.status,
                                    box: box
                                })
                            }
                        }
                    }
                }
                return data;
            }
        }
    }
</script>

<script type="text/x-template" id="box-pane">
    <div id="boxPane">
        <div id="boxPaneLayout" style="height:100%;display:flex; flex-direction:row;">
            <div id="gridBoxes">
            </div>
        </div>
    </div>
</script>

