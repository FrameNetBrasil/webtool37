<script>
    let sentencePane = {
        template: '#sentence-pane',
        props: [],
        data() {
            return {
                sentence: '',
                words: [],
                objects: [],
                sentenceObjects: [],
                currentPhraseStart: -1,
                currentPhraseEnd: -1,
                currentObject: null,
                currentRowSelected: -1,
                idObjectSentenceMM: -1,
                luData: [],
                lu: {}
            }
        },
        computed: {
            currentPhrase() {
                let currentPhrase = '';
                if (this.words.length > 0) {
                    if (this.currentPhraseStart >= 0) {
                        for (let i = this.currentPhraseStart; i <= this.currentPhraseEnd; i++) {
                            currentPhrase = currentPhrase + this.words[i].word + ' ';
                        }
                    }
                }
                this.toogleSubmit(this.currentObject && (currentPhrase !== ''));
                return currentPhrase;
            }
        },
        created: function () {
            let url = this.$store.state.model.urlLookupLUs + '?q=tes';
            console.log('lus: ' + url);
            manager.doAjax(url, (response) => {
                console.log(response);
                this.luData = response;
            }, {});
        },
        mounted: function () {

            this.objects = this.$store.state.objectsTracker.annotatedObjects;
            this.sentence = this.$store.state.model.sentence.text.trim();
            this.createWords();
            this.sentenceObjects = this.getSentenceObjectsData();
            this.decorateWords();

            //
            // watch change currentObject
            //
            this.$store.watch(
                (state, getters) => getters.currentObject,
                (currentObject) => {
                    this.currentObject = currentObject;
                    if (this.currentRowSelected !== -1) {
                        $('#gridSentenceObjects').datagrid('refreshRow', this.currentRowSelected);
                    }
                    if (currentObject) {
                        let rows = $('#gridSentenceObjects').datagrid('getRows');
                        for (var index in rows) {
                            let row = rows[index];
                            if (row.idObject == currentObject.idObject) {
                                $('#gridSentenceObjects').datagrid('scrollTo', index);
                                $('#gridSentenceObjects').datagrid('refreshRow', index);
                                this.currentRowSelected = index;
                            }
                        }
                    } else {
                        this.currentRowSelected = -1;
                    }
                    this.toogleSubmit(this.currentObject && (this.currentPhrase !== ''));
                }
            )


            //
            // watch change currentObjectState
            //
            this.$store.watch(
                (state, getters) => getters.currentObjectState,
                (currentObjectState) => {
                    console.log('object pane currentObjectState = ' + currentObjectState)
                    if (currentObjectState === 'updated') {
                        this.decorateWords();
                    }
                }
            )

            let that = this;

            $('#sentencePane').panel({
                border: 1,
                title: 'Sentence',
            });
            $('#btnSubmitAnnotation').linkbutton({
                width: 160,
                text: 'Submit Annotation',
                disabled: true,
                onClick: that.submitAnnotation
            })

            // $('#name').textbox({
            //     width: 160,
            //     disabled: true,
            // })

            $('#lookupLU').combobox({
                width: 230,
                panelWidth: 240,
                data: that.luData,
                valueField: 'idLU',
                textField: 'fullname',
                mode: 'local',
                fitColumns: true,
                value: (that.currentObject ? that.currentObject.idLU : -1),
                columns: [[
                    {field: 'idLU', hidden: true},
                    {field: 'fullname', title: 'Name', width: 202}
                ]],
                onClickRow: function (index, row) {
                    console.log(row);
                    that.lu = row;
                }
            });

            let columns = [
                {
                    field: 'chkObject',
                    checkbox: true,
                },
                {
                    field: 'idObjectSentenceMM',
                    hidden: true,
                },
                {
                    field: 'idLU',
                    hidden: true,
                },
                {
                    field: 'idObject',
                    title: 'Entity',
                    width: 48,
                },
                {
                    field: 'startWord',
                    title: 'start',
                    width: 48,
                },
                {
                    field: 'endWord',
                    title: 'end',
                    width: 48,
                },
                {
                    field: 'text',
                    title: 'phrase',
                    width: 500,
                },
                {
                    field: 'name',
                    title: 'Flickr30k_Name',
                    //hidden: true,
                    width: 130,
                },
                {
                    field: 'lu',
                    title: 'LU',
                    width: 130,
                },
            ];
            let toolbar = [
                {
                    text: 'Delete checked',
                    iconCls: 'fas fa-trash-alt fa16px',
                    handler: function () {
                        var toDelete = [];
                        var checked = $('#gridSentenceObjects').datagrid('getChecked');
                        $.each(checked, function (index, row) {
                            //that.$store.dispatch('deleteObjectById', row.idObject);
                            toDelete.push(row.idObjectSentenceMM);
                        });
                        that.deleteSentenceObjects(toDelete);
                    }
                },
            ];

            $('#gridSentenceObjects').datagrid({
                data: that.sentenceObjects,
                border: 1,
                width: 965,
                //height:350,
                fit: true,
                idField: 'idObjectSentence',
                title: 'Annotations',
                showHeader: true,
                singleSelect: true,
                checkOnSelect: false,
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
                    that.idObjectSentenceMM = row.idObjectSentenceMM;
                },
                onLoadSuccess: function () {
                    that.decorateWords();
                },
            });


        },
        methods: {
            createWords() {
                let words = this.sentence.split(' ');
                let iword = [];
                iword.push({
                    i: 0,
                    id: 'word_' + 0,
                    word: '',
                    color: 'black',
                    idObject: -1,
                    idObjectSentenceMM: -1,
                });
                for (let i = 1; i <= words.length; i++) {
                    iword.push({
                        i: i,
                        id: 'word_' + i,
                        word: words[i - 1],
                        color: 'black',
                        idObject: -1,
                        idObjectSentenceMM: -1,
                    });
                }
                this.words = iword;
            },
            decorateWords() {
                for (let i = 0; i < this.sentenceObjects.length; i++) {
                    let object = this.sentenceObjects[i];
                    let annotatedObject = this.sentenceObjects[i].annotatedObject;
                    let start = parseInt(object.startWord);
                    let end = parseInt(object.endWord);
                    for (let w = start; w <= end; w++) {
                        this.words[w].color = annotatedObject.color;
                        this.words[w].idObject = annotatedObject.idObject;
                        this.words[w].idObjectSentenceMM = object.idObjectSentenceMM;
                    }
                }
            },
            onClickWord(i) {
                if (this.words[i].idObject > 0) {
                    console.log(this.words[i]);
                    let idCurrentObject = (this.currentObject ? this.currentObject.idObject : -1);
                    if (idCurrentObject !== this.words[i].idObject) {
                        this.$store.dispatch('selectObject', this.words[i].idObject);
                    }
                    this.idObjectSentenceMM = this.words[i].idObjectSentenceMM;
                } else {
                    if (this.currentPhraseStart === -1) {
                        this.currentPhraseStart = i;
                        this.currentPhraseEnd = i;
                    } else if (i < this.currentPhraseStart) {
                        this.currentPhraseStart = i;
                    } else {
                        this.currentPhraseEnd = i;
                    }
                    this.toogleSubmit((this.currentObject !== null));
                }
            },
            onPhraseDelete() {
                this.currentPhraseStart = -1;
                this.currentPhraseEnd = -1;
                $('#btnSubmitAnnotation').linkbutton({
                    disabled: true
                })
            },
            submitAnnotation() {
                let params = {
                    idObjectSentenceMM: this.idObjectSentenceMM,
                    idSentenceMM: this.$store.state.model.sentenceMM.idSentenceMM,
                    idObjectMM: this.currentObject.idObjectMM,
                    startWord: this.currentPhraseStart,
                    endWord: this.currentPhraseEnd,
                    //name: $('#name').textbox('getValue'),
                    idLU: $('#lookupLU').combobox('getValue'),
                }
                try {
                    let url = "/index.php/webtool/annotation/multimodal/updateImageAnnotation";
                    manager.doAjax(url, (response) => {
                        if (response.type == 'success') {
                            $('#gridSentenceObjects').datagrid({
                                data: this.getSentenceObjectsData(),
                            });
                            $('#gridSentenceObjects').datagrid('clearChecked');
                            $.messager.alert('Ok', response.message, 'info');
                        } else if (response.type == 'error') {
                            throw new Error(response.message);
                        }
                    }, params);

                } catch (e) {
                    $.messager.alert('Error', e.message, 'error');
                }
            },
            deleteSentenceObjects(toDelete) {
                let params = {
                    toDelete: toDelete,
                }
                try {
                    let url = "/index.php/webtool/annotation/multimodal/deleteImageAnnotation";
                    manager.doAjax(url, (response) => {
                        if (response.type == 'success') {
                            $.messager.alert('Ok', 'Annotation(s) deleted.', 'info');
                            $('#gridSentenceObjects').datagrid({
                                data: this.getSentenceObjectsData(),
                            });
                            $('#gridSentenceObjects').datagrid('clearChecked');
                        } else if (response.type == 'error') {
                            throw new Error(response.message);
                        }
                    }, params);
                } catch (e) {
                    $.messager.alert('Error', e.message, 'error');
                }
            },
            getSentenceObjectsData() {
                let data = [];
                let params = {
                    idSentenceMM: this.$store.state.model.sentenceMM.idSentenceMM,
                }
                try {
                    let url = "/index.php/webtool/annotation/multimodal/getImageAnnotation";
                    manager.doAjax(url, (response) => {
                        if (response.type === 'success') {
                            console.log(response);
                            data = response.data;
                            for (var d of data) {
                                let phrase = '';
                                if (this.words.length > 0) {
                                    let start = parseInt(d.startWord);
                                    let end = parseInt(d.endWord);
                                    if (start >= 0) {
                                        for (let i = start; i <= end; i++) {
                                            phrase = phrase + this.words[i].word + ' ';
                                        }
                                    }
                                }
                                console.log('lllllllllllll');
                                d.annotatedObject = this.$store.getters.annotatedObjectByIdObjectMM(d.idStaticObjectMM);
                                d.idObject = d.annotatedObject.idObject;
                                d.text = phrase;
                            }
                        } else if (response.type == 'error') {
                            throw new Error(response.message);
                        }
                    }, params);

                } catch (e) {
                    $.messager.alert('Error', e.message, 'error');
                }
                this.sentenceObjects = data;
                return data;
            },
            toogleSubmit(canSubmit) {
                $('#btnSubmitAnnotation').linkbutton({
                    disabled: !canSubmit
                })
                $('#name').textbox({
                    disabled: !canSubmit
                })
            }

        },
        watch: {
            idObjectSentenceMM(value) {
                if (value !== -1) {
                    for(var data of this.sentenceObjects) {
                        if (data.idObjectSentenceMM === value) {
                            this.currentPhraseStart = parseInt(data.startWord);
                            this.currentPhraseEnd = parseInt(data.endWord);
                            //$('#name').textbox('setValue', data.name);
                            $('#lookupLU').combobox('setValue', data.idLU);
                            this.$store.dispatch('selectObject', data.idObject);
                        }
                    }
                }
            }
        },
    }

</script>

<script type="text/x-template" id="sentence-pane">
    <div id="sentencePane" style="width:100%; height:100%;padding:8px">
        <div id="annotatedSentence"
             style="width:100%; margin-bottom:8px;display: flex; flex-direction:row; flex-wrap:wrap;">
            <div class="annotatedWord" v-for="x of words" :key="x.id" :id="x.id" :style="{ color: x.color }"
                 @click="onClickWord(x.i)">
                @{{ x.word }}
            </div>
        </div>
        <div id="phraseAnnotation"
             style="width:100%; margin-bottom:8px;display: flex; flex-direction:row; flex-wrap:wrap;">
            <div id="currentPhrasePane"
                 style="width:500px; margin-bottom:8px;display: flex; flex-direction:row; flex-wrap:wrap;">
                <div class="currentPhrase">Current phrase:</div>
                <div class="currentPhrase actualPhrase">@{{ currentPhrase }}</div>
                <LinkButton v-if="currentPhrase != ''" id="btnPhraseDelete" iconCls="faTool fas fa-trash-alt"
                            :plain="true"
                            @click="onPhraseDelete"
                            title="Delete Phrase"></LinkButton>

            </div>
            <div id="currentObjectPane"
                 style="width:200px; margin-bottom:8px;display: flex; flex-direction:row; flex-wrap:wrap;">
                <div class="currentPhrase">Current entity:</div>
                <div v-if="currentObject != null" class="currentPhrase">#@{{ currentObject.idObject }}</div>
                <div v-if="currentObject === null" class="currentPhrase">#none</div>
                <!--
                <LinkButton v-if="currentPhrase != ''" id="btnPhraseDelete" iconCls="faTool fas fa-trash-alt"
                            :plain="true"
                            @click="onPhraseDelete"
                            title="Delete Phrase"></LinkButton>
                            -->

            </div>
            <div id="currentNamePane"
                 style="width:300px; margin-bottom:8px;display: flex; flex-direction:row; flex-wrap:wrap;">
                <div class="currentPhrase">LU:</div>
                <div class="currentPhrase">
                    <input id="lookupLU">
                </div>

                <!--
                <LinkButton v-if="currentPhrase != ''" id="btnPhraseDelete" iconCls="faTool fas fa-trash-alt"
                            :plain="true"
                            @click="onPhraseDelete"
                            title="Delete Phrase"></LinkButton>
                            -->

            </div>
            <div><a href="#" id="btnSubmitAnnotation"/></div>
        </div>
        <div ref="gridSentenceObjects" id="gridSentenceObjects">
        </div>
    </div>
</script>

