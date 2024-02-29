<script>
    let objectPane = {
        template: '#object-pane',
        data() {
            return {
                currentObject: null,
                idObjectSelected: -1,
                title: 'Object #none',
                fe: {},
                object: {},
                frameData: []
            }
        },
        computed: {
            display: this.currentObject === null ? 'none' : 'flex',
        },
        created: function () {
            let url = "/index.php/webtool/data/frame/combobox";
            manager.doAjax(url, (response) => {
                this.frameData = response;
            }, {});
        },
        mounted: function () {
            //
            // watch change currentObject
            //
            this.$store.watch(
                (state, getters) => getters.currentObject,
                (currentObject) => {
                    if (currentObject === null) {
                        this.currentObject = null;
                        this.title = 'Object #none';
                        $('#btnSubmit').linkbutton('disable');
                        $('#btnRemove').linkbutton('disable');
                    } else {
                        this.currentObject = currentObject
                        this.title = 'Object #' + this.currentObject.idObject;
                        $('#btnSubmit').linkbutton('enable');
                        $('#btnRemove').linkbutton('enable');
                    }
                    this.updateForm();
                }
            )
            this.$nextTick(() => {
                let that = this;
                $('#objectPane').panel({
                    border: 1,
                    title: that.title,
                    height: 310
                });
                $('#lookupFrame').combobox({
                    width: 180,
                    panelWidth: 240,
                    data: that.frameData,
                    valueField: 'idFrame',
                    textField: 'name',
                    mode: 'local',
                    label: 'Frame Name',
                    labelPosition: 'top',
                    fitColumns: true,
                    value: (that.currentObject ? that.currentObject.idFrame : -1),
                    columns: [[
                        {field: 'idFrame', hidden: true},
                        {field: 'name', title: 'Name', width: 202}
                    ]],
                    onSelect: function (record) {
                        if (parseInt(record.idFrame)) {
                            let urlFE = manager.baseURL + 'webtool/data/frameelement/lookupDataDecorated' + '/' + record.idFrame;
                            $('#lookupFE').combogrid('clear');
                            $('#lookupFE').combogrid({method:'get'});
                            $('#lookupFE').combogrid({url: urlFE});
                        }
                    },
                    onClickRow: function (index, row) {
                        console.log(row);
                        that.frame = row;
                    }
                });

                $('#lookupFE').combogrid({
                    width: 180,
                    panelWidth: 240,
                    // url: that.model.urlLookupFE,
                    idField: 'idFrameElement',
                    textField: 'name',
                    mode: 'remote',
                    label: 'Frame Element',
                    labelPosition: 'top',
                    fitColumns: true,
                    value: (that.currentObject ? that.currentObject.idFE : -1),
                    columns: [[
                        {field: 'idFrameElement', hidden: true},
                        {
                            field: 'name', title: 'Name', width: 202, styler: function (value, row, index) {
                                return 'background-color:#' + row.rgbBg + ';color:#' + row.rgbFg;
                            }
                        }
                    ]],
                    onChange: function (newValue, oldValue) {
                        if (newValue == '') {
                            $('#lookupFE').combogrid('setValue', '');
                        }
                    },
                    onClickRow: function (index, row) {
                        console.log(row);
                        that.fe = row;
                    },
                    onLoadSuccess: function () {
                        if (that.currentObject) {
                            $('#lookupFE').combogrid('setValue', that.currentObject.idFE);
                        }
                    }
                });

                $('#btnSubmit').linkbutton({
                    width: 120,
                    text: 'Submit',
                    disabled: true,
                    onClick: that.updateObject
                })

                $('#btnRemove').linkbutton({
                    width: 120,
                    text: 'Remove',
                    disabled: true,
                    onClick: that.removeEntity
                })
               $('#objectPane').keypress(function (event) {
                    if (event.which === 13) {
                        event.preventDefault();
                        that.updateObject();
                    }
                })

            });

        },
        methods: {
            getObjectBoxesData() {
                let data = [];
                if (this.currentObject) {
                    for (box of this.currentObject.boxes) {
                        if (box.bbox !== null) {
                            data.push({
                                frameNumber: 0,
                                frameTime: 0.0,
                                x: box.bbox.x,
                                y: box.bbox.y,
                                width: box.bbox.width,
                                height: box.bbox.height,
                                blocked: box.blocked,
                            })
                        }
                    }
                }
                return data;
            },
            updateForm() {
                $('#objectPane').panel({
                    title: this.title
                });
                if (this.currentObject == null) {
                    $('#lookupFrame').combobox('setValue', -1);
                    $('#lookupFE').combogrid('setValue', -1);
                } else {
                    $('#lookupFrame').combobox('setValue', this.currentObject.idFrame);
                    let urlFE = manager.baseURL + 'webtool/data/frameelement/lookupDataDecorated' + '/' + this.currentObject.idFrame;
                    $('#lookupFE').combogrid({url: urlFE});
                    $('#lookupFE').combogrid('setValue', this.currentObject.idFE);
                }
            },
            updateObject() {
                console.log(this.currentObject);
                let params = {
                    idStaticObjectSentenceMM: this.currentObject.idObjectSentenceMM,
                    idStaticSentenceMM: this.$store.state.model.sentenceMM.idStaticSentenceMM,
                    idStaticObjectMM: this.currentObject.idObjectMM,
                    startTime: 0.0,
                    endTime: 0.0,
                    idFrame: $('#lookupFrame').combobox('getValue'),
                    frame: $('#lookupFrame').combobox('getText'),
                    idFrameElement: $('#lookupFE').combogrid('getValue'),
                    fe: $('#lookupFE').combogrid('getText'),
                }
                let feRow = null;
                var g = $('#lookupFE').combogrid('grid');	// get datagrid object
                var feData = g.datagrid('getData');
                for (let i = 0; i < feData.rows.length; i++) {
                    if (feData.rows[i].idFrameElement === params.idFrameElement) {
                        feRow = feData.rows[i];
                    }
                }
                try {
                    // params.frames = this.getObjectBoxesData();
console.log('params', params);
                    let url = "/index.php/webtool/annotation/static/updateImageObject";
                    manager.doAjax(url, (response) => {
                        if (response.type === 'success') {
                            let data = response.data;
                            console.log(data);
                            this.currentObject.idObjectMM = data.idStaticObjectMM;
                            this.currentObject.idFrame = params.idFrame;
                            this.currentObject.idFE = params.idFrameElement;
                            this.currentObject.frame = params.frame;
                            this.currentObject.fe = params.fe;
                            //this.currentObject.color = feRow ? '#' + feRow.rgbBg : '#D3D3D3';
                            this.currentObject.state = 'clean';
                            //$('#gridObjectBoxes').datagrid({
                            //    data: this.getObjectBoxesData()
                            //});
                            console.log(this.currentObject);
                            //$.messager.alert('Ok', this.title + ' saved.', 'info');
                            $.messager.alert('Ok', data.message, 'info');
                            this.$store.commit('currentObjectState', 'updated');
                            this.$store.commit('redrawFrame', true);
                        } else if (response.type === 'error') {
                            throw new Error(response.message);
                        }
                    }, params);

                } catch (e) {
                    $.messager.alert('Error', e.message, 'error');
                }
            },
            removeEntity() {
                    console.log(this.currentObject);
                    $('#lookupFrame').combobox('clear')
                    $('#lookupFE').combogrid('clear')
                    this.updateObject();
            }
        }
    }
</script>

<script type="text/x-template" id="object-pane">
    <div id="objectPane">
        <div id="objectPaneLayout" style="display:flex; flex-direction:row;">
            <div id="objectPaneLeft" style="width:210px;padding:8px">
                <div id="objectPaneForm" style="display:flex; flex-direction:column;">
                    <div><input id="lookupFrame"/></div>
                    <div><input id="lookupFE"/></div>
                    <div style="margin-top:8px"><a href="#" id="btnSubmit"/></div>
                    <div style="margin-top:8px"><a href="#" id="btnRemove"/></div>
                </div>
            </div>
            <!--
            <div id="objectPaneCenter" style="width:300px;padding:8px">
                <div id="gridObjectBoxes">
                </div>
            </div>
            -->
        </div>
    </div>
</script>

