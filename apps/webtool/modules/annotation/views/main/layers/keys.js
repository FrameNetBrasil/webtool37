<script type="text/javascript">
    // layers/keys.js
    $(function () {

        $( "body" ).keypress(function( event ) {
            event = event||window.event // IE support
            var c = event.which;
            var ctrlDown = event.ctrlKey||event.metaKey; // Mac support
            // Check for Alt+Gr (http://en.wikipedia.org/wiki/AltGr_key)
            if (ctrlDown && event.altKey) {
            } else if (ctrlDown && c==115) { // ctrl-s
                event.preventDefault();
                annotation.save();
            }
        });


        $( "body" ).keydown(function( event ) {
            console.log('keydown');
            console.log(event);
            var c = event.which;
            switch(c) {
                case 27: { // esc
                    if (annotation.topDialog !== '') {
                        $(annotation.topDialog).dialog('close');
                        annotation.popTopDialog();
                    } else {
                        annotation.initCursor();
                    }
                }
                case 46: {// del
                    if (annotation.currentSelection.rowIndex >= 0) {
                        annotation.setFields(annotation.currentSelection, '');
                        annotation.showCursor();
                    }
                    event.stopPropagation();
                    break;
                }
                case 37: {// left
                    if (annotation.currentSelection.rowIndex >= 0) {
                        var prev = annotation.currentSelection.start - 1;
                        var field = 'wf' + prev;
                        if (typeof annotation.chars[field] != 'undefined') {
                            if (annotation.chars[field]['char'] == ' ') {
                                var prev = annotation.currentSelection.start - 2;
                                var field = 'wf' + prev;
                            }
                            if (typeof annotation.chars[field] != 'undefined') {
                                //annotation.clearSelection(annotation.currentSelection.rowIndex);
                                annotation.markSelection(annotation.currentSelection.rowIndex, field);
                            }
                        }
                    }
                    event.stopPropagation();
                    break;
                }
                case 39: {// right
                    if (annotation.currentSelection.rowIndex >= 0) {
                        var next = annotation.currentSelection.end + 1;
                        var field = 'wf' + next;
                        if (typeof annotation.chars[field] != 'undefined') {
                            if (annotation.chars[field]['char'] == ' ') {
                                var next = annotation.currentSelection.end + 2;
                                var field = 'wf' + next;
                            }
                            if (typeof annotation.chars[field] != 'undefined') {
                                //annotation.clearSelection(annotation.currentSelection.rowIndex);
                                annotation.currentSelection.fields = {};
                                //annotation.currentSelection.start = next;
                                annotation.markSelection(annotation.currentSelection.rowIndex, field);

                                //var cssClass = '.datagrid-cell-c1-' + field;
                                //console.log($(cssClass));
                                var $body = $('.datagrid-view2 div.datagrid-body');
                                console.log('scrollleft = ' + $($body).scrollLeft());
                                //console.log($body);
                                //var $cell = $(cssClass)[annotation.currentSelection.rowIndex + 1];
                                //console.log('next = ' + next);
                                var cellLength = (annotation.currentSelection.end + 1) * 12;
                                console.log('cell length = ' + cellLength);
                                var bodyWidth = $($body).width();
                                console.log('body width = ' + bodyWidth);
                                if (cellLength > bodyWidth) {
                                    var offset = cellLength - bodyWidth;
                                    console.log('offset = ' + offset);
                                    annotation.scrollLeft += offset;
                                } else {
                                    annotation.scrollLeft = 0;
                                }
                                $($body).animate({scrollLeft:  annotation.scrollLeft}, 1000);

                            }
                        }
                    }
                    event.stopPropagation();
                    break;
                }
                case 38: {// up
                    if (annotation.currentSelection.rowIndex >= 0) {
                        var prev = annotation.currentSelection.start - 1;
                        var field = 'wf' + prev;
                        if (typeof annotation.chars[field] != 'undefined') {
                            if (annotation.chars[field]['char'] == ' ') {
                                var prev = annotation.currentSelection.start - 2;
                                var field = 'wf' + prev;
                            }
                            if (typeof annotation.chars[field] != 'undefined') {
                                //annotation.clearSelection(annotation.currentSelection.rowIndex);
                                annotation.markSelection(annotation.currentSelection.rowIndex, field);
                            }
                        }
                    }
                    event.stopPropagation();
                    break;
                }
                case 40: {// down
                    if (annotation.currentSelection.rowIndex >= 0) {
                        var next = annotation.currentSelection.rowIndex + 1;
                        var row = $('#layers').datagrid('getRows')[next];
                        if (typeof row != 'undefined') {
                            if (row.idLayerType == 0) {
                                var next = annotation.currentSelection.rowIndex + 2;
                                var row = $('#layers').datagrid('getRows')[next];
                                if (typeof row != 'undefined') {
                                    annotation.markSelection(next, field);
                                }
                            }
                        }
                    }
                    event.stopPropagation();
                    break;
                }
            }
        });

        annotation.initCursor = function() {
            annotation.cursor.rowIndex = 1;
            annotation.cursor.field = 0;
            var rows = $('#layers').datagrid("getRows");
            var row = rows[annotation.cursor.rowIndex];
            if (typeof row != 'undefined') {
                if (annotation.currentSelection.rowIndex >= 0) {
                    annotation.clearSelection(annotation.currentSelection.rowIndex);
                }
                if (row.idAnnotationSet > -1) {
                    var field = 'wf' + annotation.cursor.field;
                    annotation.markSelection(annotation.cursor.rowIndex, field);
                }
            }
        }

        annotation.showCursor = function() {
            if (annotation.cursor.rowIndex >= 0) {
                var field = 'wf' + annotation.cursor.field;
                annotation.markSelection(annotation.cursor.rowIndex, field);
            }
        }

    });
</script>