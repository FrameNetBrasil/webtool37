
$(function () {
    let config = {
        // Should be higher than real FPS to not skip real frames
        // Hardcoded due to JS limitations
        fps: 30,

        // Low rate decreases the chance of losing frames with poor browser performances
        playbackRate: 0.4,

        // Format of the extracted frames
        //imageMimeType: 'image/jpeg',
        //imageExtension: '.jpg',

        // Name of the extracted frames zip archive
        //framesZipFilename: 'extracted-frames.zip'
        currentTime: 0
    };

    let annotation = {
        editObject: {

        },
        updateObject() {
            console.log('update object');
            this.editObject.idFE = $('#lookupFE').combogrid('getValue');
            this.editObject.fe = $('#lookupFrame').combogrid('getText')  + '.' + $('#lookupFE').combogrid('getText');
            console.log(this.editObject);
            annotatedObjectsSet.set(this.editObject.idObject, this.editObject);
            updateObjectsGrid();
            $('#dlgObject').dialog('close');
        },
        endObject() {
            console.log(this.editObject);
            annotatedObjectsSet.get(this.editObject.idObject).endTime = config.currentTime;
            updateObjectsGrid();
            $('#dlgObject').dialog('close');
        }
    }

    //extractionFileUploaded();
        //console.log(player);
    /*
    $("#jquery_jplayer_1").jPlayer({
        ready: function () {
            $(this).jPlayer("setMedia", {
                title: "Big Buck Bunny Trailer",
                //m4v: "http://www.jplayer.org/video/m4v/Big_Buck_Bunny_Trailer.m4v",
                //ogv: "http://www.jplayer.org/video/ogv/Big_Buck_Bunny_Trailer.ogv",
                //poster: "http://www.jplayer.org/video/poster/Big_Buck_Bunny_Trailer_480x270.png"
                m4v: {{$manager->getBaseURL() . '/apps/webtool/files/multimodal/videos/fnbr1_ed.mp4'}},
            });
        },
        cssSelectorAncestor: "#jp_container_1",
        swfPath: {{$manager->getBaseURL() . '/apps/webtool/public/scripts/jplayer/'}},
        supplied: "m4v",
        useStateClassSkin: true,
        autoBlur: false,
        smoothPlayBar: true,
        keyEnabled: true,
        remainingDuration: true,
        toggleDuration: true
    });

     */

    var myPlayer = $("#jquery_jplayer_1"),
        myPlayerData,
        fixFlash_mp4, // Flag: The m4a and m4v Flash player gives some old currentTime values when changed.
        fixFlash_mp4_id, // Timeout ID used with fixFlash_mp4
        ignore_timeupdate, // Flag used with fixFlash_mp4
        options = {
            ready: function () {
                $(this).jPlayer("setMedia", {
                    title: "PedroPeloMundo_Se01Ep06Bl01",
                    //m4v: "http://www.jplayer.org/video/m4v/Big_Buck_Bunny_Trailer.m4v",
                    //ogv: "http://www.jplayer.org/video/ogv/Big_Buck_Bunny_Trailer.ogv",
                    //poster: "http://www.jplayer.org/video/poster/Big_Buck_Bunny_Trailer_480x270.png"
                    m4v: {{$manager->getBaseURL() . '/apps/webtool/files/multimodal/videos/fnbr1_ed.mp4'}},
                });
            },
            timeupdate: function(event) {
                if(!ignore_timeupdate) {
                    //console.log(event.jPlayer.status.currentTime);
                    config.currentTime = event.jPlayer.status.currentTime;
                    myControl.progress.slider("setValue", event.jPlayer.status.currentPercentAbsolute);
                }
            },
            cssSelectorAncestor: "#jp_container_1",
            swfPath: {{$manager->getBaseURL() . '/apps/webtool/public/scripts/jplayer/'}},
            supplied: "m4v",
            useStateClassSkin: true,
            autoBlur: false,
            smoothPlayBar: true,
            keyEnabled: true,
            remainingDuration: true,
            toggleDuration: true,
            size: {
                width: "640px",
                    height:"360px",
                cssClass: "jp-video-360p"
            }
        },
        myControl = {
            progress: $('#slider') //$(options.cssSelectorAncestor + " .jp-progress-slider"),
            //volume: $(options.cssSelectorAncestor + " .jp-volume-slider")
        };

    // Instance jPlayer
    myPlayer.jPlayer(options);

    // A pointer to the jPlayer data object
    myPlayerData = myPlayer.data("jPlayer");



    $('#slider').slider({
        min: 0,
        max: 100,
        value : 0,
        onSlideStart: function() {
            //console.log('slide start');
        },
        onSlideEnd: function() {
            //console.log('slide end ' + this.value);
            var sp = myPlayerData.status.seekPercent;
            if(sp > 0) {
                // Apply a fix to mp4 formats when the Flash is used.
                if(fixFlash_mp4) {
                    ignore_timeupdate = true;
                    clearTimeout(fixFlash_mp4_id);
                    fixFlash_mp4_id = setTimeout(function() {
                        ignore_timeupdate = false;
                    },1000);
                }
                // Move the play-head to the value and factor in the seek percent.
                myPlayer.jPlayer("playHead", this.value * (100 / sp));
            } else {
                // Create a timeout to reset this slider to zero.
                setTimeout(function() {
                    myControl.progress.slider("setValue", 0);
                }, 0);
            }
        }
    });

    /*
    let slider = {
        init: function (min, max) {
            $('#slider').slider({
                min: min,
                max: max,
                showTip: true,
            });
            $('#slider').slider('enable');
        },
        setPosition: function (frameNumber) {
            //console.log('set position ' + frameNumber);
            $('#slider').slider({
                value: frameNumber
            });
        },
        reset: function () {
            $('#slider').slider('disable');
        }
    };


    $('#btnPlay').linkbutton({
        disable: false,
        onClick: function () {
            console.log('play clicked');
            //playClicked();
        }
    });

    $('#btnPause').linkbutton({
        disable: true,
        onClick: function () {
            console.log('pause clicked');
            pauseClicked();
        }
    });

    $('#btnBackward').linkbutton({
        disable: true,
        onClick: function () {
            player.backward();
        }
    });

    $('#btnForward').linkbutton({
        disable: true,
        onClick: function () {
            player.forward();
        }
    });

*/

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
        modal:true,
        closed:true,
        toolbar:'#dlgObject_tools',
        border:true,
        doSize:true
    });

    $('#dlgObjectUpdate').linkbutton({
        iconCls:'icon-save',
        plain:true,
        size:null,
        onClick: function() {
            annotation.updateObject();
        }
    });

    $('#dlgObjectEnd').linkbutton({
        iconCls:'fa fa-stop',
        plain:true,
        size:null,
        onClick: function() {
            annotation.endObject();
        }
    });

    $('#lookupFE').combogrid({
        panelWidth:220,
        url: '',
        idField:'idFrameElement',
        textField:'name',
        mode:'remote',
        fitColumns:true,
        columns:[[
            {field:'idFrameElement', hidden:true},
            {field:'name', title:'Name', width:202}
        ]],
        onChange: function (newValue, oldValue) {
            if (newValue == '') {
                $('#lookupFE').combogrid('setValue', '');
            }
        }
    });

    $('#lookupFrame').combogrid({
        panelWidth:220,
        url: {{$manager->getBaseURL() . '/index.php/webtool/data/frame/lookupData'}},
        idField:'idFrame',
        textField:'name',
        mode:'remote',
        fitColumns:true,
        columns:[[
            {field:'idFrame', hidden:true},
            {field:'name', title:'Name', width:202}
        ]],
        onChange: function(newValue, oldValue) {
            console.log('idFrame = ' + newValue + ' - ' + oldValue);
            if(parseInt(newValue)) {
                let urlFE = {{$manager->getBaseURL() . '/index.php/webtool/data/frameelement/lookupData'}} + '/' + newValue;
                console.log('urlFE = ' + urlFE);
                $('#lookupFE').combogrid({url: urlFE });
                //$('#lookupFE').combogrid('reload');
            }
        }
    });


    $('#formObject').form({
        success:function(data){
            alert(data)
        }
    });

    var columns = [
        {field:'idObject', title:'ID'},
        {field:'visible', title:'Visible', editor:{type:'checkbox',options:{on:'True',off:'False'}}},
        {field:'hide', title:'Hide Others', editor:{type:'checkbox',options:{on:'True',off:'False'}}},
        {field:'idFE', title:'idFE'},
        {field:'fe', title:'FE'},
        {field:'startTime', title:'Start Time'},
        {field:'endTime', title:'End Time'},
    ];


    console.log(columns);

    $('#objectsGrid').datagrid({
        data: [],
        title: 'Objects',
        showHeader: true,
        columns: [columns],
        onClickRow: function(index,row) {
            console.log(row);
            annotation.editObject = row;
            $('#formObject').form('load', row);
            $('#dlgObject').dialog('open');
            document.getElementById('currentTime').innerHTML = config.currentTime;
        },
        onBeforeSelect: function() {return false;},
    });

    class AnnotatedObjectsSet {
        constructor() {
            this.currentId = 0;
            this.annotatedObjects = [];
        }
        add(annotatedObject) {
            this.currentId++;
            annotatedObject.id = this.currentId;
            this.annotatedObjects.push(annotatedObject);
        }
        get(id) {
            let j = -1;
            for (let i = 0; i < this.annotatedObjects.length; i++) {
                if (this.annotatedObjects[i].id == id) {
                    j = i;
                }
            }
            return (j > -1) ? this.annotatedObjects[j] : null;
        }
        set(id, annotatedObject) {
            let j = -1;
            for (let i = 0; i < this.annotatedObjects.length; i++) {
                if (this.annotatedObjects[i].id == id) {
                    j = i;
                }
            }
            if (j > -1) {
                console.log('setting');
                this.annotatedObjects[j] = annotatedObject;
            }
        }
        remove(annotatedObject) {
            let j = -1;
            for (let i = 0; i < this.annotatedObjects.length; i++) {
                if (this.annotatedObjects[i].id == annotatedObject.id) {
                    j = i;
                }
            }
            if (j > -1) {
                this.annotatedObjects.slice(j,1);
            }
        }
    }

    let framesManager = new FramesManager();
    let annotatedObjectsTracker = new AnnotatedObjectsTracker(framesManager);
    let annotatedObjectsSet = new AnnotatedObjectsSet();

    function updateObjectsGrid() {
        let data = [];
        for (let i = 0; i < annotatedObjectsSet.annotatedObjects.length; i++) {
            let annotatedObject = annotatedObjectsSet.annotatedObjects[i];
            console.log(annotatedObject);
            let row = {
                idObject: annotatedObject.id,
                visible: annotatedObject.visible,
                hide: annotatedObject.hide,
                idFE: annotatedObject.idFE,
                fe: annotatedObject.fe,
                startTime: annotatedObject.startTime,
                endTime: annotatedObject.endTime,
            }
            data.push(row);
        }
        $('#objectsGrid').datagrid({
            data: data
        })
    }

    let player = {
        currentFrame: 0,
        isPlaying: false,
        isReady: false,
        timeout: null,

        initialize: function () {
            this.currentFrame = 0;
            this.isPlaying = false;
            this.isReady = false;

            // playButton.disabled = true;
            // playButton.style.display = 'block';
            // pauseButton.disabled = true;
            // pauseButton.style.display = 'none';
            $('#btnPlay').linkbutton('disable');
            $('#btnPause').linkbutton('disable');
            $('#btnBackward').linkbutton('disable');
            $('#btnForward').linkbutton('disable');
        },

        ready: function () {
            this.isReady = true;
            $('#btnPlay').linkbutton('enable');
            //playButton.disabled = false;
        },

        seek: function (frameNumber) {
            if (!this.isReady) {
                return;
            }
            console.log('playre.seek = ' + frameNumber);

            this.pause();

            if (frameNumber >= 0 && frameNumber < framesManager.frames.totalFrames()) {
                this.drawFrame(frameNumber);
                this.currentFrame = frameNumber;
            }
        },

        forward: function() {
            this.seek(this.currentFrame + 1);
        },

        backward: function() {
            this.seek(this.currentFrame - 1);
        },

        play: function () {
            if (!this.isReady) {
                return;
            }

            this.isPlaying = true;

            //playButton.disabled = true;
            //playButton.style.display = 'none';
            //pauseButton.disabled = false;
            //pauseButton.style.display = 'block';
            $('#btnPlay').linkbutton('disable');
            $('#btnPause').linkbutton('enable');
            $('#btnBackward').linkbutton('disable');
            $('#btnForward').linkbutton('disable');
            this.nextFrame();
        },

        pause: function () {
            if (!this.isReady) {
                return;
            }

            this.isPlaying = false;
            if (this.timeout != null) {
                clearTimeout(this.timeout);
                this.timeout = null;
            }

            //pauseButton.disabled = true;
            //pauseButton.style.display = 'none';
            //playButton.disabled = false;
            //playButton.style.display = 'block';
            $('#btnPlay').linkbutton('enable');
            $('#btnPause').linkbutton('disable');
            $('#btnBackward').linkbutton('enable');
            $('#btnForward').linkbutton('enable');
            console.log('pause');

        },

        toogle: function () {
            if (!this.isPlaying) {
                this.play();
            } else {
                this.pause();
            }
        },

        nextFrame: function () {
            if (!this.isPlaying) {
                return;
            }

            if (this.currentFrame >= framesManager.frames.totalFrames()) {
                this.done();
                return;
            }

            this.drawFrame(this.currentFrame).then(() => {
                this.currentFrame++;
                this.timeout = setTimeout(() => this.nextFrame(), 1000 / (config.fps * parseFloat(speedInput.value)));
            });
        },

        drawFrame: function (frameNumber) {
            return new Promise((resolve, _) => {
                annotatedObjectsTracker.getFrameWithObjects(frameNumber).then((frameWithObjects) => {
                    ctx.drawImage(frameWithObjects.img, 0, 0);

                    for (let i = 0; i < frameWithObjects.objects.length; i++) {
                        let object = frameWithObjects.objects[i];
                        let annotatedObject = object.annotatedObject;
                        let annotatedFrame = object.annotatedFrame;
                        if (annotatedFrame.isVisible()) {
                            annotatedObject.dom.style.display = 'block';
                            annotatedObject.dom.style.width = annotatedFrame.bbox.width + 'px';
                            annotatedObject.dom.style.height = annotatedFrame.bbox.height + 'px';
                            annotatedObject.dom.style.left = annotatedFrame.bbox.x + 'px';
                            annotatedObject.dom.style.top = annotatedFrame.bbox.y + 'px';
                            annotatedObject.visible.prop('checked', true);
                        } else {
                            annotatedObject.dom.style.display = 'none';
                            annotatedObject.visible.prop('checked', false);
                        }
                    }

                    let shouldHideOthers = frameWithObjects.objects.some(o => o.annotatedObject.hideOthers);
                    if (shouldHideOthers) {
                        for (let i = 0; i < frameWithObjects.objects.length; i++) {
                            let object = frameWithObjects.objects[i];
                            let annotatedObject = object.annotatedObject;
                            if (!annotatedObject.hideOthers) {
                                annotatedObject.dom.style.display = 'none';
                            }
                        }
                    }

                    slider.setPosition(this.currentFrame);

                    resolve();
                });
            });
        },

        done: function () {
            this.currentFrame = 0;
            this.isPlaying = false;

            //playButton.disabled = false;
            //playButton.style.display = 'block';
            //pauseButton.disabled = true;
            //pauseButton.style.display = 'none';
            $('#btnPlay').linkbutton('enable');
            $('#btnPause').linkbutton('disable');

        }
    };

    function clearAllAnnotatedObjects() {
        for (let i = 0; i < annotatedObjectsTracker.annotatedObjects.length; i++) {
            clearAnnotatedObject(i);
        }
    }

    function clearAnnotatedObject(i) {
        let annotatedObject = annotatedObjectsTracker.annotatedObjects[i];
        annotatedObject.controls.remove();
        $(annotatedObject.dom).remove();
        annotatedObjectsTracker.annotatedObjects.splice(i, 1);
        annotatedObjectsSet.remove(annotatedObject)
    }

    function interactify(dom, onChange) {
        let bbox = $(dom);
        bbox.addClass('bbox');

        let createHandleDiv = (className) => {
            let handle = document.createElement('div');
            handle.className = className;
            bbox.append(handle);
            return handle;
        };

        bbox.resizable({
            handles: "n, e, s, w",
            onStopResize: (e) => {
                let position = bbox.position();
                onChange(Math.round(position.left), Math.round(position.top), Math.round(bbox.width()), Math.round(bbox.height()));
            }
        });

        let x = createHandleDiv('handle center-drag');
        console.log(x);
        bbox.draggable({
            //containment: 'parent',
            handle: $(x),
            onDrag: (e) => {
                var d = e.data;
                if (d.left < 0){d.left = 0}
                if (d.top < 0){d.top = 0}
                if (d.left + $(d.target).outerWidth() > $(d.parent).width()){
                    d.left = $(d.parent).width() - $(d.target).outerWidth();
                }
                if (d.top + $(d.target).outerHeight() > $(d.parent).height()){
                    d.top = $(d.parent).height() - $(d.target).outerHeight();
                }
            },
            //stop: (e, ui) => {
            onStopDrag: (e) => {
                let position = bbox.position();
                onChange(Math.round(position.left), Math.round(position.top), Math.round(bbox.width()), Math.round(bbox.height()));
            }
        });
    }

    let mouse = {
        x: 0,
        y: 0,
        startX: 0,
        startY: 0
    };

    let tmpAnnotatedObject = null;

    doodle.onmousemove = function (e) {
        console.log('onmousemove');
        let ev = e || window.event;
        if (ev.pageX) {
            mouse.x = ev.pageX;
            mouse.y = ev.pageY;
        } else if (ev.clientX) {
            mouse.x = ev.clientX;
            mouse.y = ev.clientY;
        }
        mouse.x -= doodle.offsetLeft;
        mouse.y -= doodle.offsetTop;

        if (tmpAnnotatedObject !== null) {
            tmpAnnotatedObject.width = Math.abs(mouse.x - mouse.startX);
            tmpAnnotatedObject.height = Math.abs(mouse.y - mouse.startY);
            tmpAnnotatedObject.x = (mouse.x - mouse.startX < 0) ? mouse.x : mouse.startX;
            tmpAnnotatedObject.y = (mouse.y - mouse.startY < 0) ? mouse.y : mouse.startY;

            tmpAnnotatedObject.dom.style.width = tmpAnnotatedObject.width + 'px';
            tmpAnnotatedObject.dom.style.height = tmpAnnotatedObject.height + 'px';
            tmpAnnotatedObject.dom.style.left = tmpAnnotatedObject.x + 'px';
            tmpAnnotatedObject.dom.style.top = tmpAnnotatedObject.y + 'px';
        }
    }

    doodle.onclick = function (e) {
        if (doodle.style.cursor != 'crosshair') {
            return;
        }

        if (tmpAnnotatedObject != null) {
            let annotatedObject = new AnnotatedObject();
            annotatedObject.dom = tmpAnnotatedObject.dom;
            let bbox = new BoundingBox(tmpAnnotatedObject.x, tmpAnnotatedObject.y, tmpAnnotatedObject.width, tmpAnnotatedObject.height);
            annotatedObject.add(new AnnotatedFrame(player.currentFrame, bbox, true));
            annotatedObjectsTracker.annotatedObjects.push(annotatedObject);
            tmpAnnotatedObject = null;

            interactify(
                annotatedObject.dom,
                (x, y, width, height) => {
                    console.log('annotated object changing');
                    let bbox = new BoundingBox(x, y, width, height);
                    annotatedObject.add(new AnnotatedFrame(player.currentFrame, bbox, true));
                }
            );

            addAnnotatedObjectControls(annotatedObject);

            doodle.style.cursor = 'default';
        } else {
            mouse.startX = mouse.x;
            mouse.startY = mouse.y;

            let dom = newBboxElement();
            dom.style.left = mouse.x + 'px';
            dom.style.top = mouse.y + 'px';
            tmpAnnotatedObject = {dom: dom};
        }
    }

    function newBboxElement() {
        let dom = document.createElement('div');
        dom.className = 'bbox';
        doodle.appendChild(dom);
        console.log('new box');
        console.log(dom);
        return dom;
    }

    function addAnnotatedObjectControls(annotatedObject) {
        /*
        let name = $('<input type="text" value="Name?" />');
        if (annotatedObject.name) {
            name.val(annotatedObject.name);
        }
        name.on('change keyup paste mouseup', function () {
            annotatedObject.name = this.value;
        });

        let id = $('<input type="text" value="ID?" />');
        if (annotatedObject.id) {
            id.val(annotatedObject.id);
        }
        id.on('change keyup paste mouseup', function () {
            annotatedObject.id = this.value;
        });

        let visibleLabel = $('<label>');
        let visible = $('<input type="checkbox" checked="checked" />');
        annotatedObject.visible = visible;
        visible.change(function () {
            let bbox;
            if (this.checked) {
                annotatedObject.dom.style.display = 'block';
                let jquery = $(annotatedObject.dom);
                let position = jquery.position();
                bbox = new BoundingBox(Math.round(position.left), Math.round(position.top), Math.round(jquery.width()), Math.round(jquery.height()));
            } else {
                annotatedObject.dom.style.display = 'none';
                bbox = null;
            }
            annotatedObject.add(new AnnotatedFrame(player.currentFrame, bbox, true));
        });
        visibleLabel.append(visible);
        visibleLabel.append('Is visible?');

        let hideLabel = $('<label>');
        let hide = $('<input type="checkbox" />');
        annotatedObject.hide = hide;
        hide.change(function () {
            annotatedObject.hideOthers = this.checked;
        });
        hideLabel.append(hide);
        hideLabel.append('Hide others?');

        let del = $('<input type="button" value="Delete" />');
        del.click(function () {
            for (let i = 0; annotatedObjectsTracker.annotatedObjects.length; i++) {
                if (annotatedObject === annotatedObjectsTracker.annotatedObjects[i]) {
                    clearAnnotatedObject(i);
                    break;
                }
            }
        });

        let div = $('<div></div>');
        div.css({
            'border': '1px solid black',
            'display': 'inline-block',
            'margin': '5px',
            'padding': '10px'
        });
        div.append(name);
        div.append($('<br />'));
        div.append(id);
        div.append($('<br />'));
        div.append(visibleLabel);
        div.append($('<br />'));
        div.append(hideLabel);
        div.append($('<br />'));
        div.append(del);

        annotatedObject.controls = div;



        $('#objects').append(div);

         */
        annotatedObject.name = '';
        annotatedObject.visible = true;
        annotatedObject.hide = false;
        annotatedObject.idFE = -1;
        annotatedObject.fe = '';
        annotatedObject.startTime = config.currentTime;
        annotatedObject.endTime = config.currentTime;
        annotatedObjectsSet.add(annotatedObject)
        let border = 'color' + annotatedObject.id;
        annotatedObject.dom.classList.add(border);
        updateObjectsGrid();
    }

    function generateXml() {
        let xml = '<?xml version="1.0" encoding="utf-8"?>\n';
        xml += '<annotation>\n';
        xml += '  <folder>not available</folder>\n';
        xml += '  <filename>not available</filename>\n';
        xml += '  <source>\n';
        xml += '    <type>video</type>\n';
        xml += '    <sourceImage>vatic frames</sourceImage>\n';
        xml += '    <sourceAnnotation>vatic</sourceAnnotation>\n';
        xml += '  </source>\n';

        let totalFrames = framesManager.frames.totalFrames();
        for (let i = 0; i < annotatedObjectsTracker.annotatedObjects.length; i++) {
            let annotatedObject = annotatedObjectsTracker.annotatedObjects[i];

            xml += '  <object>\n';
            xml += '    <name>' + annotatedObject.name + '</name>\n';
            xml += '    <moving>true</moving>\n';
            xml += '    <action/>\n';
            xml += '    <verified>0</verified>\n';
            xml += '    <id>' + annotatedObject.id + '</id>\n';
            xml += '    <createdFrame>0</createdFrame>\n';
            xml += '    <startFrame>0</startFrame>\n';
            xml += '    <endFrame>' + (totalFrames - 1) + '</endFrame>\n';

            for (let frameNumber = 0; frameNumber < totalFrames; frameNumber++) {
                let annotatedFrame = annotatedObject.get(frameNumber);
                if (annotatedFrame == null) {
                    window.alert('Play the video in full before downloading the XML so that bounding box data is available for all frames.');
                    return;
                }

                let bbox = annotatedFrame.bbox;
                if (bbox != null) {
                    let isGroundThrugh = annotatedFrame.isGroundTruth ? 1 : 0;

                    xml += '    ';
                    xml += '<polygon>';
                    xml += '<t>' + frameNumber + '</t>';
                    xml += '<pt><x>' + bbox.x + '</x><y>' + bbox.y + '</y><l>' + isGroundThrugh + '</l></pt>';
                    xml += '<pt><x>' + bbox.x + '</x><y>' + (bbox.y + bbox.height) + '</y><l>' + isGroundThrugh + '</l></pt>';
                    xml += '<pt><x>' + (bbox.x + bbox.width) + '</x><y>' + (bbox.y + bbox.height) + '</y><l>' + isGroundThrugh + '</l></pt>';
                    xml += '<pt><x>' + (bbox.x + bbox.width) + '</x><y>' + bbox.y + '</y><l>' + isGroundThrugh + '</l></pt>';
                    xml += '</polygon>\n';
                }
            }

            xml += '  </object>\n';
        }

        xml += '</annotation>\n';

        /*
        let writeStream = streamSaver.createWriteStream('output.xml').getWriter();
        let encoder = new TextEncoder();
        writeStream.write(encoder.encode(xml));
        writeStream.close();
         */
    }

    function importXml() {
        if (this.files.length != 1) {
            return;
        }

        var reader = new FileReader();
        reader.onload = (e) => {
            if (e.target.readyState != 2) {
                return;
            }

            if (e.target.error) {
                throw 'file reader error';
            }

            let xml = $($.parseXML(e.target.result));
            let objects = xml.find('object');
            for (let i = 0; i < objects.length; i++) {
                let object = $(objects[i]);
                let name = object.find('name').text();
                let id = object.find('id').text();

                let annotatedObject = new AnnotatedObject();
                annotatedObject.name = name;
                annotatedObject.id = id;
                annotatedObject.dom = newBboxElement();
                annotatedObjectsTracker.annotatedObjects.push(annotatedObject);

                interactify(
                    annotatedObject.dom,
                    (x, y, width, height) => {
                        let bbox = new BoundingBox(x, y, width, height);
                        annotatedObject.add(new AnnotatedFrame(player.currentFrame, bbox, true));
                    }
                );

                addAnnotatedObjectControls(annotatedObject);

                let lastFrame = -1;
                let polygons = object.find('polygon');
                for (let j = 0; j < polygons.length; j++) {
                    let polygon = $(polygons[j]);
                    let frameNumber = parseInt(polygon.find('t').text());
                    let pts = polygon.find('pt');
                    let topLeft = $(pts[0]);
                    let bottomRight = $(pts[2]);
                    let isGroundThrough = parseInt(topLeft.find('l').text()) == 1;
                    let x = parseInt(topLeft.find('x').text());
                    let y = parseInt(topLeft.find('y').text());
                    let w = parseInt(bottomRight.find('x').text()) - x;
                    let h = parseInt(bottomRight.find('y').text()) - y;

                    if (lastFrame + 1 != frameNumber) {
                        let annotatedFrame = new AnnotatedFrame(lastFrame + 1, null, true);
                        annotatedObject.add(annotatedFrame);
                    }

                    let bbox = new BoundingBox(x, y, w, h);
                    let annotatedFrame = new AnnotatedFrame(frameNumber, bbox, isGroundThrough);
                    annotatedObject.add(annotatedFrame);

                    lastFrame = frameNumber;
                }

                if (lastFrame + 1 < framesManager.frames.totalFrames()) {
                    let annotatedFrame = new AnnotatedFrame(lastFrame + 1, null, true);
                    annotatedObject.add(annotatedFrame);
                }
            }

            player.drawFrame(player.currentFrame);
        };
        reader.readAsText(this.files[0]);
    }



});