// this file get the vars processed by Latte, just to avoid mixing template stuff in javascript files
<script>
    vue.$store.commit('fromLatte', {
        title: "PedroPeloMundo_Se01Ep06Bl01",
        m4v: {{$manager->getBaseURL() . '/apps/webtool/files/multimodal/videos/fnbr1_ed.mp4'}},
        swfPath: {{$manager->getBaseURL() . '/apps/webtool/public/scripts/jplayer/'}},
        urlLookupFrame: {{$manager->getBaseURL() . '/index.php/webtool/data/frame/lookupData'}},
        urlLookupFE: {{$manager->getBaseURL() . '/index.php/webtool/data/frameelement/lookupData'}}
    })

</script>

