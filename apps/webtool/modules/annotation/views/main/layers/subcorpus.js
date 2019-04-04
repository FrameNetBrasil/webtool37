<script type="text/javascript">
    // layers/subcorpus.js
    $(function () {

        annotation.addMWEManualSubcorpus  = function (wf, idLU, idSentence) {
            var data = [];
            var i = 0;
            for (idWord in annotation.words) {
                word = annotation.words[idWord];
                if (word.word != ' ') {
                    data[i++] = word;
                }
            }
            console.log(data);
            console.log(wf);
            $('#mwe').datagrid({data: data});
            var rows = $('#mwe').datagrid('getRows');
            for (r in rows) {
                if (rows[r].word == wf.word) {
                    $('#mwe').datagrid('checkRow', r);
                }
            }
            $('#mweIdLU').val(idLU);
            $('#mweIdSentence').val(idSentence);
            $('#dlgMWE').dialog('doLayout');
            $('#dlgMWE').dialog('open');
        }

        annotation.dlgMWESave = function() {
            var idLU = $('#mweIdLU').val();
            var idSentence = $('#mweIdSentence').val();
            var startChar = 4000;
            var endChar = -1;
            var rowsChecked = $('#mwe').datagrid('getChecked');
            var chars = [];
            for (w in rowsChecked) {
                word = rowsChecked[w];
                for(var i = word.startChar; i <= word.endChar; ++i) {
                    chars[i] = i;
                }
                if (word.startChar < startChar) {
                    startChar = word.startChar;
                }
                if (word.endChar > endChar) {
                    endChar = word.endChar;
                }
            }
            annotation.addManualSubcorpus(idLU, idSentence, startChar, endChar, chars);
        }

        annotation.addManualSubcorpus  = function (idLU, idSentence, startChar, endChar, chars) {
            $.ajax({
                type: "POST",
                url: {{$manager->getURL('annotation/main/addManualSubcorpus')}},
                data: {idLU: idLU, idSentence: idSentence, startChar: startChar, endChar: endChar, chars: chars},
                dataType: "json",
                async: false
            });
            annotation.refresh();
        }

    });
</script>