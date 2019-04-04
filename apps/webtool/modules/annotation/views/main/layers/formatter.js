<script type="text/javascript">
    // layers/formatter.js
    $(function () {

        annotation.styleSelected = function (jq) {
            jq.css('background-color','#CCC').css('color','black');
        }

        annotation.rowStyler = function(index,row){
            //console.log('refreshing row ' + index);
            if (!row.show) {
                return 'display:none';
            }
            if (row.idLayerType == 0){
                return {class:'layerTarget'};
            } else {
                return {class:'layerSelected'};
            }
        }

        annotation.cellFormatter = function (value,row,index) {
            if (this.field == 'name') {
                if ((typeof annotation.layers[row.idLayer] != 'undefined') ) {
                    return annotation.labelTypes[value]['label'];
                }
            }
            if (this.field == 'wf0') {
                if ((typeof annotation.layers[row.idLayer] != 'undefined') ) {
                    annotation.layers[row.idLayer]['currentLabel'] = 0;
                }
            }
            var text = '';
            var idLabelType = (typeof value != 'undefined') ? value : '';
            if (idLabelType != '') {
                if (row.idLayerType > 0){
                    var pos = 0;
                    var label = annotation.labelTypes[idLabelType]['label'];
                    if (idLabelType != annotation.layers[row.idLayer]['currentLabel']) {
                        annotation.layers[row.idLayer]['currentLabel'] = idLabelType;
                        pos = annotation.layers[row.idLayer]['currentLabelPos'] = 0;
                    } else {
                        pos = ++annotation.layers[row.idLayer]['currentLabelPos'];
                    }
                    text = label.charAt(pos);
                } else {
                    text = idLabelType;
                }
            } else {
                if ((typeof annotation.layers[row.idLayer] != 'undefined') ) {
                    if (annotation.layers[row.idLayer]['currentLabelPos'] > 0) {
                        annotation.layers[row.idLayer]['currentLabel'] = 0;
                        annotation.layers[row.idLayer]['currentLabelPos'] = 0;
                    }
                }
            }
            return text;
        }

        annotation.cellLayerFormatter = function (value,row,index) {
            var text = value;
            if (row.idLayerType == 0){
                text = '';//text = "<span>[" + row.idAnnotationSet + "]</span>"
                text = text + "<a class='fa fa-info-circle fa16px' style=':hover {color:green};text-decoration:none;' onclick='annotation.ASInfo(" + row.idAnnotationSet + ")'>&nbsp</a>";
                text = text + "<a class='fa fa-comment-o fa16px' style=':hover {color:green};text-decoration:none;' onclick='annotation.ASComments(" + row.idAnnotationSet + ")'>&nbsp</a>" + annotation.annotationSets[row.idAnnotationSet]['name'];
            }
            if (row.layerTypeEntry == 'lty_udrelation'){
                console.log(row);
                console.log(index);
                text = text + "&nbsp<a class='fa fa-sitemap fa16px' onclick='annotation.UDTree(" + index + ")'>&nbsp</a>";
            }
            return text;
        }

        annotation.cellNIFormatter = function (value,row,index) {
            var text = '';
            var i = 0;
            if (row.idLayerType == -1){
                text = '';
                i = 1;
            } else {
                var nis = annotation.nis[row.idLayer];
                if (nis) {
                    jQuery.each(nis, function (idLabel, ni) {
                        var color = annotation.rgbColors[ni.idColor];
                        var style = 'background-color:' + color.rgbBg + ';color:' + color.rgbFg + ';';
                        text = text + "<div class='easyui-tooltip divNI' title='" + ni.fe + "' style='" + style + "'>" + ni.label + "</div>";
                        i++;
                    });
                }
                var width = i * 30;
            }
            return "<div style='width:" + width + "px'>" + text + "</div>";
        }

        annotation.cellStyler = function (value,row,index) {
            var style = '';
            if ((typeof value != 'undefined') && (value != '')) {
                var idLabelType;
                var idColor;
                if (row.idLayerType == -1){
                    idColor = 1;
                } else {
                    if (row.idLayerType == 0) { // Target
                        idLabelType = annotation.layerLabels[row.idLayer][0];
                    } else {
                        idLabelType = value;
                    }
                    idColor = annotation.labelTypes[idLabelType]['idColor'];
                }
                //console.log('cellStyler');
                //console.log('idLabelType = ' + idLabelType);
                //console.log(row);
                var color = annotation.rgbColors[idColor];
                var style = 'background-color:' + color.rgbBg + ';color:'+ color.rgbFg + ';';
            }
            return style;
        }

    });
</script>