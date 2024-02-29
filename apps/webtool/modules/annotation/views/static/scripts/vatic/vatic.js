"use strict";

let vatic = {
    colors: ["#f21f26",
        "#91c879",
        "#5780d4",
        "#cdeb2d",
        "#4a3c44",
        "#69e2da",
        "#012aaf",
        "#f88006",
        "#53e052",
        "#199601",
        "#ff31d5",
        "#bf5e70",
        "#84059a",
        "#999867",
        "#f8b90d"],
   blobToImage: function (blob) {
        return new Promise((result, _) => {
            let img = new Image();
            img.crossOrigin = "anonymous";
            img.onload = function () {
                result(img);
                URL.revokeObjectURL(this.src);
            };
            img.src = URL.createObjectURL(blob);
        });
    },
    /*
    getFrameFromUrl: function (url) {
        //console.log(url);
        return fetch(url)       // 1) fetch the url
            .then(function (response) {                       // 2) filter on 200 OK
                if (response.status === 200 || response.status === 0) {
                    return Promise.resolve(response.blob());
                } else {
                    return Promise.reject(new Error(response.statusText));
                }
            });
    },
    */
    getFramesForObjects: async function (framesManager, config) {
        //framesManager.setConfig(config);
        return {
            totalFrames: () => {
                return framesToLoad.length;
            },
            getFrame: (frameNumber) => {
                return framesManager.getFrame(frameNumber);
            }
        };
    },


    getColor(i) {
        return this.colors[((i - 1) % 14)];
    }
}