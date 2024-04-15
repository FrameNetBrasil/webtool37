@php
    $videoWidth = $data->documentMM->videoWidth;
    $timeWidth = 175;
    $sliderWidth = $videoWidth - (2 * $timeWidth);
@endphp
<script>
    let timePane = {
        template: '#time-pane',
        props: [],
        data() {
            return {
                slider: {
                    min: 0,
                    max: 0,
                    value: 0,
                },
                currentFrame: 1,
                currentVideoState: this.$store.state.currentVideoState,
            }
        },
        computed: {
            displayTime: function () {
                return this.formatTime(this.$store.getters.currentTime);
            },
            totalDuration: function () {
                //return this.formatTime(this.$store.getters.endTime);
                return this.formatTime(annotationVideoModel.time.end);
            }
        },
        created() {

        },
        methods: {
            formatTime(timeToFormat) {
                let dt1 = timeToFormat;
                let ft = parseFloat(dt1);
                let i = parseInt(dt1);
                let ms = parseInt((ft - i) * 1000);
                let show = i + '.' + ms  + 's';
                return show;
            },
            changeValue(v) {
                if ((this.currentState === 'paused') || (this.currentVideoState === 'loaded')) {
                    if ((v >= this.slider.min) && (v <= this.slider.max)) {
                        this.$store.commit('currentFrame', v);
                    }
                }
            },
            updateSlider() {
                this.slider.value = this.currentFrame;
                let value = 0;
                if (this.currentFrame > 0) {
                    value = Math.round((100 * (this.currentFrame - this.slider.min) / (this.slider.max - this.slider.min)));
                }
                $('#sliderVideo').slider('setValue', value);
            },
        },
        mounted: function () {
            this.$store.watch(
                (state, getters) => getters.currentVideoState,
                (currentVideoState) => {
                    this.currentVideoState = currentVideoState;
                }
            )
            this.$store.watch(
                (state, getters) => getters.currentFrame,
                (currentFrame) => {
                    this.currentFrame = currentFrame;
                    this.updateSlider();
                }
            )
            // this.$store.watch(
            //     (state, getters) => getters.playFrame,
            //     (playFrame) => {
            //         this.currentFrame = playFrame;
            //         this.updateSlider();
            //     }
            // )
            this.slider.min = annotationVideoModel.framesRange.first;
            this.slider.max = annotationVideoModel.framesRange.last;
            this.slider.value = annotationVideoModel.framesRange.first;
            let that = this;
            $('#sliderVideo').slider({
                min: 0,
                max: 100,
                value: that.slider.value,
                disabled: false,
                width: 'auto',
                onComplete: function (value) {
                    let v = Math.round(that.slider.min + (that.slider.max - that.slider.min) * (value / 100));
                    if ((v >= that.slider.min) && (v <= that.slider.max)) {
                        that.$store.commit('currentFrame', v);
                    }
                },
                onSlideStart: function () {
                    that.$store.commit('currentState', 'dragging');
                },
                onSlideEnd: function () {
                    that.$store.commit('currentState', 'paused');
                },
                onChange(newValue) {
                    that.$store.commit('currentSliderPosition', newValue);
                }
            });
        },
    }

</script>

<script type="text/x-template" id="time-pane">
    <div id="timePaneDiv" style="width:{{$videoWidth}}px; display:flex; flex-direction: row;">
        <div id="timePaneLeft" style="width:{{$timeWidth}}px">
            <span>@{{slider.value}} | @{{displayTime}}</span>
        </div>
        <div id="sliderContainer" style="width:{{$sliderWidth}}px">
            <input id="sliderVideo"></input>
        </div>
        <div id="timePaneRight" style="width:{{$timeWidth}}px">
            <span>@{{totalDuration}} | @{{slider.max}}</span>
        </div>
    </div>
</script>

