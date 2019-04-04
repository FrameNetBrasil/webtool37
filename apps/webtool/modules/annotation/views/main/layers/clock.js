<script type="text/javascript">
    // layers/clock.js 
    $(function () {

        annotation.clock = {
            min: annotation.timeout,
            sec: 30
        }

        annotation.startCountdown = function () {
            var activeSession = true;
            if (annotation.clock.sec <= 0 ){
                annotation.clock.sec = 60;
                annotation.clock.min -= 1;
            }
            //console.log(annotation.clock);
            if ((annotation.clock.min == 0) && (annotation.clock.sec == 60)){
                var msg = 'Session will expire in 2 min. ';
                if (!annotation.dataIsSaved) {
                    msg = msg + 'Save your work!'
                }
                $.messager.alert('Warning', msg);
            }
            if (annotation.clock.min <= -1){
                activeSession = false;
                $.messager.alert({
                    title: 'Error',
                    icon: 'error',
                    msg: 'Session expired!',
                    fn: function(){
                        console.log({{$manager->getURL('main/main')}});
                        window.location = {{$manager->getURL('main/main')}};
                    }
                });
            } else {
                annotation.clock.sec -= 10;
                if(annotation.clock.sec < 10) {
                    //seconds = "0" + seconds;
                }
                setTimeout("annotation.startCountdown()",10000);
            }
        }

        if (annotation.chronometer == 0) {
            annotation.startCountdown();
            annotation.chronometer = 1;
        }

    });
</script>