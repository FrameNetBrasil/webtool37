<script type="text/javascript">
    console.log('==========================');
    console.log(window.latte);
    $(function () {
        console.log(window.latte);
            let myPlayer = $("#jquery_jplayer_1"),
                myPlayerData,
                fixFlash_mp4, // Flag: The m4a and m4v Flash player gives some old currentTime values when changed.
                fixFlash_mp4_id, // Timeout ID used with fixFlash_mp4
                ignore_timeupdate, // Flag used with fixFlash_mp4
                options = {
                    ready: function () {
                        console.log('jsplayer ready');
                        $(this).jPlayer("setMedia", {
                            title: latte.title,
                            //m4v: "http://www.jplayer.org/video/m4v/Big_Buck_Bunny_Trailer.m4v",
                            //ogv: "http://www.jplayer.org/video/ogv/Big_Buck_Bunny_Trailer.ogv",
                            //poster: "http://www.jplayer.org/video/poster/Big_Buck_Bunny_Trailer_480x270.png"
                            m4v: latte.mv4,
                        });
                    },
                    timeupdate: function (event) {
                        if (!ignore_timeupdate) {
                            //console.log(event.jPlayer.status.currentTime);
                            config.currentTime = event.jPlayer.status.currentTime;
                            myControl.progress.slider("setValue", event.jPlayer.status.currentPercentAbsolute);
                        }
                    },
                    cssSelectorAncestor: "#jp_container_1",
                    swfPath: latte.swfPath,
                    supplied: "m4v",
                    useStateClassSkin: true,
                    autoBlur: false,
                    smoothPlayBar: true,
                    keyEnabled: true,
                    remainingDuration: true,
                    toggleDuration: true,
                    size: {
                        width: "640px",
                        height: "360px",
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

            myPlayer.jPlayer({
                loadedData: function (e) {
                    console.log('loaded data');
                    console.log(e);
                }
            })
        });
</script>