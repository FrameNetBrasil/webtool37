<script type="text/javascript">

    let doodle = document.querySelector('#doodle');
    let canvas = document.querySelector('#canvas');
    let ctx = canvas.getContext('2d');
    //let videoFile = document.querySelector('#videoFile');
    //let zipFile = document.querySelector('#zipFile');
    //let xmlFile = document.querySelector('#xmlFile');
    //let videoDimensionsElement = document.querySelector('#videoDimensions');
    //let extractionProgressElement = document.querySelector('#extractionProgress');
    //let downloadFramesButton = document.querySelector('#downloadFrames');
    //let playButton = document.querySelector('#play');
    //let pauseButton = document.querySelector('#pause');
    //let speedInput = document.querySelector('#speed');
    //let sliderElement = document.querySelector('#slider');
    //let generateXmlButton = document.querySelector('#generateXml');

    let slider = {
        init: function (min, max, onChange) {
            //$(sliderElement).slider('option', 'min', min);
            //$(sliderElement).slider('option', 'max', max);
            $('#slider').slider({
                min: min,
                max: max
            });
            $('#slider').on('slidestop', (e, ui) => {
                onChange(ui.value);
            });
            $('#slider').slider('enable');
        },
        setPosition: function (frameNumber) {
            $('#slider').slider({
                value: frameNumber
            });
        },
        reset: function () {
            $('#slider').slider({
                disabled: true
            });
        }
    };


    $('#btnPlay').linkbutton({
        iconCls: 'icon-save',
        size: null,
        disable: true,
        onClick: function () {
            playClicked();
        }
    });

    $('#btnPause').linkbutton({
        iconCls: 'icon-save',
        size: null,
        disable: true,
        onClick: function () {
            pauseClicked();
        }
    });

    $('#btnNewBox').linkbutton({
        iconCls: 'icon-save',
        size: null,
        disable: true,
        onClick: function () {
            newBboxElement();
        }
    });

    $('#btnUpdateObjects').linkbutton({
        iconCls: 'icon-save',
        size: null,
        disable: true,
        onClick: function () {
            generateXml();
        }
    });


</script>