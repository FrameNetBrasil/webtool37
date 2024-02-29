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
                frameData: [],
                lastFrame: 0
            }
        },
        computed: {
            display: this.currentObject === null ? 'none' : 'flex',
        },
        created: function () {
            this.frameData = annotationVideoAPI.listFrame();
            this.frameData.push({idFrame: -1, name: ''})
        },
        mounted: function () {
            console.log(this.title);
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
                    } else {
                        this.currentObject = currentObject
                        this.title = 'Object #' + this.currentObject.idObject;
                        $('#btnSubmit').linkbutton('enable');
                    }
                    this.updateForm();
                }
            )
            //
            // watch change currentObjectStateFlag
            //
            this.$store.watch(
                (state, getters) => getters.currentObjectStateFlag,
                (currentObjectStateFlag) => {
                    let currentObjectState = this.$store.state.currentObjectState;
                    console.log('object pane currentObjectStateFlag = ' + currentObjectStateFlag, currentObjectState)
                    if ((currentObjectState === 'editingBox') || (currentObjectState === 'updated')) {
                        this.updateGrid();
                    }
                }
            )
            //
            // watch change currentObjectState
            //
            this.$store.watch(
                (state, getters) => getters.currentObjectState,
                (currentObjectState) => {
                    if  (currentObjectState === 'updated') {
                        this.updateGrid();
                    }
                }
            )
            this.$nextTick(() => {
                let that = this;
                $('#objectPane').panel({
                    border: 1,
                    title: that.title,
                    height: 260
                });
                $('#lookupFrame').combobox({
                    panelWidth: 240,
                    data: that.frameData,
                    valueField: 'idFrame',
                    textField: 'name',
                    mode: 'local',
                    label: 'Frame Name',
                    labelPosition: 'top',
                    fitColumns: true,
                    value: (that.currentObject ? that.currentObject.idFrame : null),
                    columns: [[
                        {field: 'idFrame', hidden: true},
                        {field: 'name', title: 'Name', width: 202}
                    ]],
                    onSelect: function (record) {
                        if (parseInt(record.idFrame)) {
                            let urlFE = manager.baseURL + annotationVideoModel.url.lookupFE + '/' + record.idFrame;
                            $('#lookupFE').combogrid({url: urlFE});
                            $('#lookupFE').combogrid('setValue', '');
                        }
                    },
                    onClickRow: function (index, row) {
                        console.log(row);
                        that.frame = row;
                    }
                });

                $('#lookupFE').combogrid({
                    panelWidth: 240,
                    // url: that.model.urlLookupFE,
                    idField: 'idFrameElement',
                    textField: 'name',
                    mode: 'remote',
                    label: 'Frame Element',
                    labelPosition: 'top',
                    fitColumns: true,
                    value: (that.currentObject ? that.currentObject.idFE : null),
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

                $('#lookupLU').combogrid({
                    panelWidth: 360,
                    width: 360,
                    url: manager.baseURL + annotationVideoModel.url.lookupLU,
                    idField: 'idLU',
                    textField: 'fullname',
                    mode: 'remote',
                    fitColumns: true,
                    label: 'CV Name',
                    labelPosition: 'top',
                    columns: [[
                        {field: 'idLU', hidden: true},
                        {field: 'fullname', title: 'Name', width: 480}
                    ]]
                });

                $('#startFrame').numberbox({
                    label: 'StartFrame',
                    labelPosition: 'top',
                    width: 96,
                    value: (that.currentObject ? that.currentObject.startFrame : 0),
                })

                $('#endFrame').numberbox({
                    label: 'EndFrame',
                    labelPosition: 'top',
                    width: 96,
                    value: (that.currentObject ? that.currentObject.endFrame : 0),
                })

                $('#btnSubmit').linkbutton({
                    width: 120,
                    text: 'Save',
                    disabled: true,
                    onClick: that.updateObject
                })

                $('#gridObjectFrames').datagrid({
                    data: [],
                    border: 1,
                    showHeader: true,
                    width: 350,
                    height: 200,
                    rowStyler: function (index, row) {
                        let style = 'background-color:white;';
                        if (that.currentObject && row) {
                            if (that.currentObject.state === 'dirty') {
                                style = 'background-color:#FFFFE0;'
                            }
                        }
                        return style;
                    },
                    columns: [[
                        {
                            field: 'frameNumber',
                            width: 56,
                            title: 'frame',
                        },

                        {
                            field: 'x',
                            width: 56,
                            title: 'x',
                        },
                        {
                            field: 'y',
                            width: 56,
                            title: 'y',
                        },
                        {
                            field: 'height',
                            width: 56,
                            title: 'Height',
                        },
                        {
                            field: 'width',
                            width: 56,
                            title: 'Width',
                        },
                        {
                            field: 'blocked',
                            width: 56,
                            title: 'Blocked',
                        },


                    ]],
                });

                $('#gridObjectFrames').datagrid('getPanel').panel('panel').attr('tabindex', 1).unbind('keydown');

                $('#objectPane').keypress(function (event) {
                    if (event.which === 13) {
                        event.preventDefault();
                        that.updateObject();
                    }
                })

            });

        },
        methods: {
            getObjectFrameData() {
                let data = [];
                this.lastFrame = this.currentObject.endFrame;
                for (frame of this.currentObject.frames) {
                    if (frame.bbox !== null) {
                        data.push({
                            frameNumber: frame.frameNumber,
                            frameTime: frame.frameNumber / annotationVideoModel.fps,
                            x: frame.bbox.x,
                            y: frame.bbox.y,
                            width: frame.bbox.width,
                            height: frame.bbox.height,
                            blocked: frame.blocked,
                        })
                        this.lastFrame = frame.frameNumber;
                    }
                }
                return data;
            },
            updateGrid() {
                console.log('updating object grid');
                let currentObject = this.$store.state.currentObject;
                let objectFrames = this.getObjectFrameData();
                $('#gridObjectFrames').datagrid({
                    data: objectFrames
                });
                console.log(currentObject);
                $('#startFrame').numberbox('setValue', currentObject.startFrame);
                $('#endFrame').numberbox('setValue', this.lastFrame);
            },
            updateForm() {
                $('#objectPane').panel({
                    title: this.title
                });
                if (this.currentObject == null) {
                    $('#startFrame').numberbox('setValue', 0);
                    $('#endFrame').numberbox('setValue', 0);
                    $('#lookupFrame').combobox('setValue', null);
                    $('#lookupFE').combogrid('setValue', null);
                    $('#lookupLU').combogrid('setValue', null);
                    $('#gridObjectFrames').datagrid({
                        data: []
                    });
                } else {
                    $('#startFrame').numberbox('setValue', this.currentObject.startFrame);
                    $('#endFrame').numberbox('setValue', this.currentObject.endFrame);
                    $('#lookupFrame').combobox('setValue', this.currentObject.idFrame);
                    let urlFE = manager.baseURL + annotationVideoModel.url.lookupFE + '/' + this.currentObject.idFrame;
                    $('#lookupFE').combogrid({url: urlFE});
                    $('#lookupFE').combogrid('setValue', this.currentObject.idFE);
                    $('#lookupLU').combogrid('setValue', this.currentObject.lu);
                    //console.log(this.currentObject.frames);
                    $('#gridObjectFrames').datagrid({
                        data: this.getObjectFrameData()
                    });
                }
            },
            async updateObject() {
                let params = {
                    //idSentenceMM: this.$store.state.model.idSentenceMM,
                    idObjectMM: this.currentObject.idObjectMM,
                    idDocumentMM: annotationVideoModel.documentMM.idDocumentMM,
                    startFrame: parseInt($('#startFrame').numberbox('getValue')),
                    endFrame: parseInt($('#endFrame').numberbox('getValue')),
                    idFrame: $('#lookupFrame').combobox('getValue'),
                    frame: $('#lookupFrame').combobox('getText'),
                    idFrameElement: $('#lookupFE').combogrid('getValue'),
                    fe: $('#lookupFE').combogrid('getText'),
                    idLU: $('#lookupLU').combogrid('getValue'),
                    lu: $('#lookupLU').combogrid('getText'),
                }
                params.startTime = (params.startFrame - 1) / annotationVideoModel.fps;
                params.endTime = (params.endFrame - 1) / annotationVideoModel.fps;
                let feRow = null;
                var g = $('#lookupFE').combogrid('grid');	// get datagrid object
                var feData = g.datagrid('getData');
                console.log(feData.rows);
                console.log(params.idFrameElement);
                for (let i = 0; i < feData.rows.length; i++) {
                    if (feData.rows[i].idFrameElement === params.idFrameElement) {
                        feRow = feData.rows[i];
                    }
                }
                console.log(feRow);
                console.log(params);

                if (params.startFrame > params.endFrame) {
                    throw new Error('endFrame must be greater or equal to startFrame.');
                }
                if (params.endFrame > this.currentObject.endFrame) {
                    let bbox = null;
                    let j = this.currentObject.frames.length - 1;
                    let polygon = this.currentObject.frames[j];
                    for (let i = this.currentObject.endFrame; i <= params.endFrame; i++) {
                        let frameNumber = i;
                        let isGroundThrough = true;
                        let x = parseInt(polygon.bbox.x);
                        let y = parseInt(polygon.bbox.y);
                        let w = parseInt(polygon.bbox.width);
                        let h = parseInt(polygon.bbox.height);
                        bbox = new BoundingBox(x, y, w, h);
                        let annotatedFrame = new AnnotatedFrame(frameNumber, bbox, isGroundThrough);
                        annotatedFrame.blocked = (parseInt(polygon.blocked) === 1);
                        this.currentObject.add(annotatedFrame);
                    }
                }

                if (params.startFrame < this.currentObject.startFrame) {
                    let bbox = null;
                    let polygon = this.currentObject.get(this.currentObject.startFrame);
                    console.log(polygon);
                    for (let i = params.startFrame; i < this.currentObject.startFrame; i++) {
                        let frameNumber = i;
                        let isGroundThrough = true;
                        let x = parseInt(polygon.bbox.x);
                        let y = parseInt(polygon.bbox.y);
                        let w = parseInt(polygon.bbox.width);
                        let h = parseInt(polygon.bbox.height);
                        bbox = new BoundingBox(x, y, w, h);
                        let annotatedFrame = new AnnotatedFrame(frameNumber, bbox, isGroundThrough);
                        annotatedFrame.blocked = (parseInt(polygon.blocked) === 1);
                        this.currentObject.add(annotatedFrame);
                    }
                }
                params.frames = this.getObjectFrameData();

                let data = await annotationVideoAPI.updateObject(params);

                this.currentObject.idObjectMM = data.idObjectMM;
                this.currentObject.idFrame = params.idFrame;
                this.currentObject.idFE = params.idFrameElement;
                this.currentObject.idLU = params.idLU;
                this.currentObject.frame = params.frame;
                this.currentObject.fe = params.fe;
                this.currentObject.lu = params.lu;
                this.currentObject.color = feRow ? '#' + feRow.rgbBg : '#D3D3D3';
                this.currentObject.startFrame = params.startFrame;
                this.currentObject.endFrame = params.endFrame;
                this.currentObject.state = 'clean';
                //this.$store.dispatch('updateObject', this.currentObject);
                $('#gridObjectFrames').datagrid({
                    data: this.getObjectFrameData()
                });
                //this.$store.dispatch('selectObject', this.currentObject.idObject);
                console.log(this.currentObject);
                $.messager.alert('Ok', this.title + ' saved.', 'info');
                //console.log(this.$store.state.currentObjectState);
                this.$store.commit('currentObjectState', 'updated');
                this.$store.commit('currentFrame', this.currentObject.startFrame);
                this.$store.commit('redrawFrame', true);
                $('#gridObjects').datagrid('refreshRow', this.$store.state.currentRowGrid);


            }
        }
    }
</script>

<script type="text/x-template" id="object-pane">
    <div id="objectPane">
        <div id="objectPaneLayout" style="display:flex; flex-direction:row;">
            <div id="objectPaneLeft" style="width:520px;padding:8px">
                <div id="objectPaneForm" style="display:flex; flex-direction:column;">
                    <div style="display:flex; flex-direction:row;width:100%">
                        <div style="margin-right:8px"><input id="startFrame"/></div>
                        <div><input id="endFrame" disabled/></div>
                    </div>
                    <div style="display:flex; flex-direction:row;width:100%">
                        <div style="margin-right:8px"><input id="lookupFrame"/></div>
                        <div><input id="lookupFE"/></div>
                    </div>
                    <div><input id="lookupLU"/></div>
                    <!--
                    <div style="display:flex; flex-direction:row;width:100%">
                        <div><input id="lookupLU"/></div>
                    </div>
                    -->
                    <div style="margin-top:8px"><a href="#" id="btnSubmit"/></div>
                </div>
            </div>
            <div id="objectPaneCenter" style="padding:8px">
                <div id="gridObjectFrames">
                </div>
            </div>
        </div>
    </div>
</script>

