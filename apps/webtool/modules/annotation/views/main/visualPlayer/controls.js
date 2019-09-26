<script type="text/javascript">
    $(function () {
            $('#slider').slider({
                min: 0,
                max: 100,
                value: 0,
                onSlideStart: function () {
                    //console.log('slide start');
                },
                onSlideEnd: function () {
                    //console.log('slide end ' + this.value);
                    var sp = myPlayerData.status.seekPercent;
                    if (sp > 0) {
                        // Apply a fix to mp4 formats when the Flash is used.
                        if (fixFlash_mp4) {
                            ignore_timeupdate = true;
                            clearTimeout(fixFlash_mp4_id);
                            fixFlash_mp4_id = setTimeout(function () {
                                ignore_timeupdate = false;
                            }, 1000);
                        }
                        // Move the play-head to the value and factor in the seek percent.
                        myPlayer.jPlayer("playHead", this.value * (100 / sp));
                    } else {
                        // Create a timeout to reset this slider to zero.
                        setTimeout(function () {
                            myControl.progress.slider("setValue", 0);
                        }, 0);
                    }
                }
            });

            $('#btnNewBox').linkbutton({
                iconCls: 'far fa-square',
                disable: true,
                onClick: function () {
                    //newBboxElement();
                    doodle.style.cursor = 'crosshair';
                }
            });

            $('#btnUpdateObjects').linkbutton({
                iconCls: 'fa fa-save',
                disable: true,
                onClick: function () {
                    generateXml();
                }
            });

            $('#dlgObject').dialog({
                modal: true,
                closed: true,
                toolbar: '#dlgObject_tools',
                border: true,
                doSize: true
            });

            $('#dlgObjectUpdate').linkbutton({
                iconCls: 'icon-save',
                plain: true,
                size: null,
                onClick: function () {
                    annotation.updateObject();
                }
            });

            $('#dlgObjectEnd').linkbutton({
                iconCls: 'fa fa-stop',
                plain: true,
                size: null,
                onClick: function () {
                    annotation.endObject();
                }
            });

            $('#lookupFE').combogrid({
                panelWidth: 220,
                url: '',
                idField: 'idFrameElement',
                textField: 'name',
                mode: 'remote',
                fitColumns: true,
                columns: [[
                    {field: 'idFrameElement', hidden: true},
                    {field: 'name', title: 'Name', width: 202}
                ]],
                onChange: function (newValue, oldValue) {
                    if (newValue == '') {
                        $('#lookupFE').combogrid('setValue', '');
                    }
                }
            });

            $('#lookupFrame').combogrid({
                panelWidth: 220,
                url: latte.urlLookpFrame,
                idField: 'idFrame',
                textField: 'name',
                mode: 'remote',
                fitColumns: true,
                columns: [[
                    {field: 'idFrame', hidden: true},
                    {field: 'name', title: 'Name', width: 202}
                ]],
                onChange: function (newValue, oldValue) {
                    console.log('idFrame = ' + newValue + ' - ' + oldValue);
                    if (parseInt(newValue)) {
                        let urlFE = latte.urlLookupFE + '/' + newValue;
                        console.log('urlFE = ' + urlFE);
                        $('#lookupFE').combogrid({url: urlFE});
                        //$('#lookupFE').combogrid('reload');
                    }
                }
            });


            $('#formObject').form({
                success: function (data) {
                    alert(data)
                }
            });
        });
</script>