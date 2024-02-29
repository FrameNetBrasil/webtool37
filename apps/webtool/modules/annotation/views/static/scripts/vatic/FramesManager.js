"use strict";

class FramesManager {
    constructor() {
        this.frames = {
            totalFrames: () => {
                return 0;
            }
        };
        this.db = {};

        this.onReset = [];
        //this.video = document.createElement('video');

        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');

        this.dimensionsInitialized = false;
        this.totalFrames = 0;
        this.processedFrames = 0;
        this.lastApproxFrame = -1;
        this.lastProgressFrame = -1;
        this.frameNumber = -1;
        this.interval = 0;
    }

    onBrowserAnimationFrame() {
        if (this.dimensionsInitialized && this.video.ended) {
            if (this.processedFrames == this.totalFrames) {
                this.videoEnded();
            }
            return;
        }

        compatibility.requestAnimationFrame(this.onBrowserAnimationFrame);

        if (this.video.readyState !== this.video.HAVE_CURRENT_DATA &&
            this.video.readyState !== this.video.HAVE_FUTURE_DATA &&
            this.video.readyState !== this.video.HAVE_ENOUGH_DATA) {
            return;
        }

        let currentApproxFrame = Math.round(this.video.currentTime * this.config.fps);
        if (currentApproxFrame != this.lastApproxFrame) {
            this.lastApproxFrame = currentApproxFrame;
            this.frameNumber = this.totalFrames;
            this.totalFrames++;

            if (!this.dimensionsInitialized) {
                this.dimensionsInitialized = true;
                this.canvas.width = this.video.videoWidth;
                this.canvas.height = this.video.videoHeight;
            }

            this.ctx.drawImage(this.video, 0, 0);
            this.canvas.toBlob(
                (blob) => {
                    this.processedFrames++;

                    if (this.frameNumber > this.lastProgressFrame) {
                        this.lastProgressFrame = frameNumber;
                        progress(this.video.currentTime / this.video.duration, this.processedFrames, blob);
                    }

                    if (this.video.ended && this.this.processedFrames == totalFrames) {
                        this.videoEnded();
                    }
                },
                this.config.imageMimeType
            );
        }
    }

    progress(percentage, framesSoFar, lastFrameBlob) {
        blobToImage(lastFrameBlob).then((img) => {
            if (!this.dimensionsInitialized) {
                this.dimensionsInitialized = true;
                //initializeCanvasDimensions(img);
            }
            this.ctx.drawImage(img, 0, 0);

            //videoDimensionsElement.innerHTML = 'Video dimensions determined: ' + img.width + 'x' + img.height;
            //extractionProgressElement.innerHTML = (percentage * 100).toFixed(2) + ' % completed. ' + framesSoFar + ' frames extracted.';
        })
    }

    videoEnded() {
        if (this.video.src != '') {
            URL.revokeObjectURL(this.video.src);
            this.video.src = '';

            resolve({
                totalFrames: () => {
                    return totalFrames;
                },
                getFrame: (frameNumber) => {
                    //return db.getAttachment(frameNumber.toString(), attachmentName);
                }
            });
        }
    }

    setConfig(config) {
        this.config = config;
        this.video = document.getElementById(config.idVideoDOMElement);
        if (!this.dimensionsInitialized) {
            this.dimensionsInitialized = true;
            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;
        }
        this.interval = 1000 / this.config.fps;
    }

    async getFrameFromVideo(frameNumber) {
        //let jumpToTime = (frameNumber - 1 ) * (this.interval);
        //this.video.currentTime = jumpToTime;
        //console.log('getFrameFromVideo currentTime = ' + this.video.currentTime);
        this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
        return new Promise((resolve, reject) => {
            this.canvas.toBlob(
                (blob) => {
                    //console.log(blob);
                    resolve(blob);
                },
                this.config.imageMimeType
            );
        });
    }

    addFrame(frameNumber, frame, imageMimeType) {
        let attachment = frame;//new Blob(frame, {type: imageMimeType});
        /*
        this.db.putAttachment(frameNumber.toString(), this.attachmentName, attachment, imageMimeType).then(function (result) {
            console.log('put in db ' + frameNumber.toString());
        }).catch(function (err) {
            console.log(err);
        });
        */
        this.db[frameNumber] = frame;
    }

    async getFrame(frameNumber) {
        //return this.db.getAttachment(frameNumber.toString(), this.attachmentName);
        let frame = this.db[frameNumber];
        //if (typeof frame === 'undefined') {
        //    let f = '0000' + frameNumber;
        //    let frame = await getFrameFromUrl(this.config.url + '/' + f.substr(-4) + this.config.imageExtension);
        //    this.addFrame(frameNumber, frame, this.config.imageMimeType);
        //    return frame;
        //}
        if (typeof frame === 'undefined') {
            //let f = '0000' + frameNumber;
            let frame = await this.getFrameFromVideo(frameNumber);
            //console.log(frame);
            this.addFrame(frameNumber, frame, this.config.imageMimeType);
            return frame;
        }
        return frame;
    }

    set(frames) {
        this.frames = frames;
        for (let i = 0; i < this.onReset.length; i++) {
            this.onReset[i]();
        }
    }
}

