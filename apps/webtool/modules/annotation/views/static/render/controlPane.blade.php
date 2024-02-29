<script>
    controlPane = {
        slider: {
            min: 0,
            max: 0,
            value: 0,
            //disable: true,
        },
        currentTime: 0,
        currentFrame: 0,
        currentState: () => storage.currentState.get(),
        changeValue: function (v) {
            if ((this.currentState() === 'paused') || (this.currentState() === 'loaded')) {
                if ((v >= this.slider.min) && (v <= this.slider.max)) {
                    this.currentFrame = v;
                    storage.currentFrame.set(v);
                }
            }
        },
        onForwardClick: function () {
            if ((this.currentState() === 'paused') || (this.currentState() === 'loaded')) {
                if (this.slider.value < this.slider.max) {
                    this.slider.value = this.slider.value + 1;
                    storage.currentFrame.set(this.slider.value);
                }
            }
        },
        onBackwardClick: function () {
            if ((this.currentState() === 'paused') || (this.currentState() === 'loaded')) {
                if (this.slider.value > this.slider.min) {
                    this.slider.value = this.slider.value - 1;
                    storage.currentFrame.set(this.slider.value);
                }
            }
        },
        onBeginningClick: function () {
            if ((this.currentState() === 'paused') || (this.currentState() === 'loaded')) {
                if (this.slider.value > this.slider.min) {
                    this.slider.value = this.slider.min;
                    storage.currentFrame.set(this.slider.value);
                }
            }
        },
        onPlayClick: function () {
            if ((this.currentState() === 'paused') || (this.currentState() === 'loaded')) {
                storage.currentState.set('playing');
            }
        },
        onPauseClick: function () {
            if ((this.currentState() === 'playing') || (this.currentState() === 'loaded')) {
                storage.currentState.set('paused');
            }
        },
        updateSlider: function () {
            this.slider.value = this.currentFrame;
            let value = Math.round((100 * (this.currentFrame - this.slider.min) / (this.slider.max - this.slider.min)));
            console.log('=ccc ===', value);
            $('#sliderVideo').slider('setValue', value);
        },
        mount: function () {
            console.log('mounting control pane');
            let framesRange = videoPane.framesRange;
            this.slider.min = framesRange.first;
            this.slider.max = framesRange.last;
            this.slider.value = framesRange.first;
            $('#sliderVideo').slider({
                min: 0,
                max: 100,
                value: controlPane.slider.value,
                disabled: false,
                width: 'auto',
                onComplete: function (value) {
                    //console.log('on complete ' + value);
                    let v = Math.round(controlPane.slider.min + (controlPane.slider.max - controlPane.slider.min) * (value / 100));
                    //let v = value;
                    //console.log('v = ' + v);
                    if ((v >= controlPane.slider.min) && (v <= controlPane.slider.max)) {
                        storage.currentFrame.set(v);
                    }
                },
                onSlideStart: function () {
                    //console.log('slider start');
                    storage.currentState.set('dragging');
                },
                onSlideEnd: function () {
                    storage.currentState.set('paused');
                },
                onChange(newValue) {
                    console.log('=====', newValue);
                    storage.currentSlider.set(newValue);
                }
            });
        }
    }

</script>