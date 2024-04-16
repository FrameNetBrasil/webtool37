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
        created() {
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
                    console.log('watch currentObject', currentObject);
                    this.currentObject = currentObject;
                    if (this.currentRowSelected !== -1) {
                        $('#gridSentenceObjects').datagrid('refreshRow', this.currentRowSelected);
                    }
                    if (currentObject) {
                        let currentRowSelected = -1;
                        let idObjectSentenceMM = -1;
                        let rows = $('#gridSentenceObjects').datagrid('getRows');
                        for (var index in rows) {
                            let row = rows[index];
                            $('#gridSentenceObjects').datagrid('scrollTo', index);
                            if (row.idObject == currentObject.idObject) {
                                $('#gridSentenceObjects').datagrid('refreshRow', index);
                                currentRowSelected = index;
                                idObjectSentenceMM = row.idStaticObjectSentenceMM;
                            }
                        }
                        this.currentRowSelected = currentRowSelected;
                        if (idObjectSentenceMM !== -1) {
                            this.idObjectSentenceMM = idObjectSentenceMM;
                        }
                    } else {
                        this.currentRowSelected = -1;
                    }
                    //console.log('sort1');
                    //$('#gridSentenceObjects').datagrid('sort', 'idObject');
                    this.toogleSubmit(this.currentObject && (this.currentPhrase !== ''));
                }
            )


            //
            // watch change currentObjectState
            //
            this.$store.watch(
                (state, getters) => getters.currentObjectState,
                (currentObjectState) => {
//                    console.log('object pane currentObjectState = ' + currentObjectState)
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

            // $('#idLemma').combobox({
            //     loader: that.lemmaLoader,
            //     mode: 'remote',
            //     valueField: 'idLemma',
            //     textField: 'name',
            //     disabled: true,
            // })

            let columns = [
                {
                    field: 'idObjectSentenceMM',
                    hidden: true,
                },
                {
                    field: 'idObject',
                    title: '#Object',
                    width: 56,
                },
                {
                    field: 'startWord',
                    title: 'start',
                    width: 56,
                },
                {
                    field: 'endWord',
                    title: 'end',
                    width: 56,
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
                fit: true,
                idField: 'idObjectSentence',
                title: 'Annotations',
                showHeader: true,
                singleSelect: true,
                checkOnSelect: false,
                columns: [
                    columns
                ],
                rowStyler: function (index, row) {
                    let currentObject = that.$store.state.currentObject;
                    if (currentObject && (row)) {
                        if (currentObject.idObject === row.idObject) {
                            return 'background-color:#6293BB;color:#fff;'; // return inline style
                        } else {
                            return 'background-color:white;color:black;'; // return inline style
                        }
                    }
                },
                onClickRow: function (index, row) {
                    that.idObjectSentenceMM = row.idObjectSentenceMM;
                    that.$store.dispatch('selectObject', row.idObject);
                },
                onLoadSuccess: function () {
                    $('#gridSentenceObjects').datagrid('sort', 'idObject');
                    that.decorateWords();
                },
            });


        },
        methods: {
            createWords() {
                //let words = this.sentence.split(' ');
                let words = this.sentence.split(' ').map(t => t.split(/([,.])/g)).flat().filter(x => x != "");
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
                    for (let w = object.startWord; w <= object.endWord; w++) {
                        this.words[w].color = annotatedObject.color;
                        this.words[w].idObject = parseInt(annotatedObject.idObject);
                        this.words[w].idObjectSentenceMM = parseInt(object.idStaticObjectSentenceMM);
                    }
                }
            },
            onClickWord(i) {
                console.log(this.words[i].idObject);
                if (this.words[i].idObject > 0) {
                    this.idObjectSentenceMM = this.words[i].idObjectSentenceMM;
                    this.$store.dispatch('selectObject', this.words[i].idObject);
                } /* else {
                    if (this.currentPhraseStart === -1) {
                        this.currentPhraseStart = i;
                        this.currentPhraseEnd = i;
                    } else if (i < this.currentPhraseStart) {
                        this.currentPhraseStart = i;
                    } else {
                        this.currentPhraseEnd = i;
                    }
                    this.toogleSubmit((this.currentObject !== null));
                } */
            },
            onPhraseDelete() {
                this.currentPhraseStart = -1;
                this.currentPhraseEnd = -1;
                $('#btnSubmitAnnotation').linkbutton({
                    disabled: true
                })
            },
            // submitAnnotation() {
            //     let params = {
            //         idStaticObjectSentenceMM: this.idObjectSentenceMM,
            //         idSentenceMM: this.$store.state.model.sentenceMM.idStaticSentenceMM,
            //         idStaticObjectMM: this.currentObject.idObjectMM,
            //         startWord: this.currentPhraseStart,
            //         endWord: this.currentPhraseEnd,
            //         idLemma_name: $('#idLemma').textbox('getValue'),
            //     }
            //     try {
            //         let url = "/index.php/webtool/annotation/static/updateImageAnnotation";
            //         manager.doAjax(url, (response) => {
            //             if (response.type == 'success') {
            //                 $('#gridSentenceObjects').datagrid({
            //                     data: this.getSentenceObjectsData(),
            //                 });
            //                 $('#gridSentenceObjects').datagrid('clearChecked');
            //                 $.messager.alert('Ok', response.message, 'info');
            //             } else if (response.type == 'error') {
            //                 throw new Error(response.message);
            //             }
            //         }, params);
            //
            //     } catch (e) {
            //         $.messager.alert('Error', e.message, 'error');
            //     }
            // },
            deleteSentenceObjects(toDelete) {
                let params = {
                    toDelete: toDelete,
                }
                try {
                    let url = "/index.php/webtool/annotation/static/deleteImageAnnotation";
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
                    idStaticSentenceMM: this.$store.state.model.sentenceMM.idStaticSentenceMM,
                }
                try {
                    let url = "/index.php/webtool/annotation/static/getImageAnnotation";
                    manager.doAjax(url, (response) => {
                        if (response.type === 'success') {
                            // console.log(response);
                            data = response.data;
                            for (var d of data) {
                                let phrase = '';
                                d.startWord = parseInt(d.startWord);
                                d.endWord = parseInt(d.endWord);
                                d.idStaticObjectSentenceMM = parseInt(d.idStaticObjectSentenceMM);
                                console.log(this.words);
                                console.log(d);
                                if (this.words.length > 0) {
                                    if (d.startWord >= 0) {
                                        for (let i = d.startWord; i <= d.endWord; i++) {
                                            phrase = phrase + this.words[i].word + ' ';
                                        }
                                    }
                                }
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
            // lemmaLoader(param, success, error) {
            //     var q = param.q || '';
            //     if (q.length <= 2) {
            //         return false
            //     }
            //     let params = {
            //         q: q
            //     }
            //     console.log('**  in lemma loader');
            //     let url = "/index.php/webtool/annotation/static/lemmaLoader";
            //     manager.doAjax(url, (response) => {
            //         console.log(response);
            //         if (response.type == 'success') {
            //             let data = response.data;
            //             let items = $.map(data, function (item, index) {
            //                 return {
            //                     idLemma: item.idLemma,
            //                     name: item.fullname
            //                 };
            //             });
            //             console.log(items);
            //             success(items);
            //         } else if (response.type == 'error') {
            //             throw new Error(response.message);
            //         }
            //     }, params);
            // },
            toogleSubmit(canSubmit) {
                $('#btnSubmitAnnotation').linkbutton({
                    disabled: !canSubmit
                })
                $('#idLemma').textbox({
                    disabled: !canSubmit
                })
            }

        },
        watch: {
            idObjectSentenceMM(value) {
                console.log('watch idObjectSentenceMM', value);
                if (value) {
                    if (value !== -1) {
                        for (var data of this.sentenceObjects) {
                            console.log(data);
                            if (data.idStaticObjectSentenceMM === value) {
                                this.currentPhraseStart = data.startWord;
                                this.currentPhraseEnd = data.endWord;
                                // $('#idLemma').textbox('setValue', data.idLemma);
                                // let idCurrentObject = (this.currentObject ? this.currentObject.idObject : -1);
                                // if (idCurrentObject !== data.idObject) {
                                //     this.$store.dispatch('selectObject', data.idObject);
                                // }
                                //this.$store.dispatch('selectObject', data.idObject);
                            }
                        }
                    }
                }
            }
        },
    }

</script>

<script type="text/x-template" id="sentence-pane">
    <div id="sentencePane" style="width:100%; height:400px;padding:8px;display: flex; flex-direction:column;">
        <div id="annotatedSentence"
             style="width:100%; margin-bottom:8px;display: flex; flex-direction:row; flex-wrap:wrap;">
            <div class="annotatedWord" v-for="x of words" :key="x.id" :id="x.id" :style="{ color: x.color }">
                @{{ x.word }}
            </div>
        </div>
        <!--
        <div id="phraseAnnotation"
             style="width:100%; margin-bottom:8px;display: flex; flex-direction:row; flex-wrap:wrap;">
            <div id="currentPhrasePane"
                 style="width:500px; margin-bottom:8px;display: flex; flex-direction:row; flex-wrap:wrap;">
                <div class="currentPhrase">Current phrase:</div>
                <div class="currentPhrase actualPhrase">@{{ currentPhrase }}</div>
            </div>
            <div id="currentObjectPane"
                 style="width:200px; margin-bottom:8px;display: flex; flex-direction:row; flex-wrap:wrap;">
                <div class="currentPhrase">Current object:</div>
                <div v-if="currentObject != null" class="currentPhrase">#@{{ currentObject.idObject }}</div>
                <div v-if="currentObject === null" class="currentPhrase">#none</div>
            </div>
            <div id="currentNamePane"
                 style="width:100%; margin-bottom:8px;display: flex; flex-direction:row;">
                <div>
                    <span class="currentPhrase">Flickr30k_Name:</span>
                    <span class="currentPhrase">
                        @{{this.$store.state.model.sentenceMM.idStaticSentenceMM}}
                    </span>
                </div>
                <div>
                    <span class="currentPhrase">Lemma:</span>
                    <div class="currentPhrase">
                        <input id="idLemma">
                    </div>
                </div>
                <div><a href="#" id="btnSubmitAnnotation"/></div>
            </div>
        </div>
        -->
        <div id="gridSentences" style="height:300px">
            <div ref="gridSentenceObjects" id="gridSentenceObjects"></div>
        </div>
    </div>
</script>

