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
        this.video = document.getElementById('jp_video_1');
        //this.video.autoplay = false;
        //this.video.muted = true;
        //this.video.loop = false;
        //this.video.playbackRate = this.config.playbackRate;
        //this.video.src = URL.createObjectURL("http://127.0.0.1/webtool/apps/webtool/files/multimodal/video/PedroPeloMundo_Se01_Ep06_Bl01_Par01.mp4");
        //this.video.src = "http://127.0.0.1/webtool/apps/webtool/files/multimodal/video/PedroPeloMundo_Se01_Ep06_Bl01_Par01.mp4";
        //compatibility.requestAnimationFrame(this.onBrowserAnimationFrame);
        //this.video.play();
        //this.video.load();
        //this.video.onloadedmetadata = function() {
        //    console.log("****  Metadata for video loaded");
        //};
        if (!this.dimensionsInitialized) {
            this.dimensionsInitialized = true;
            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;
        }
        this.interval = 1000 / this.config.fps;
        //console.log(this);

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

function blobToImage(blob) {
    return new Promise((result, _) => {
        let img = new Image();
        img.crossOrigin = "anonymous";
        img.onload = function () {
            result(img);
            URL.revokeObjectURL(this.src);
        };
        img.src = URL.createObjectURL(blob);
    });
}

/*
function progress (percentage, framesSoFar, lastFrameBlob) => {
              blobToImage(lastFrameBlob).then((img) => {
                if (!dimensionsInitialized) {
                  dimensionsInitialized = true;
                  initializeCanvasDimensions(img);
                }
                ctx.drawImage(img, 0, 0);

                videoDimensionsElement.innerHTML = 'Video dimensions determined: ' + img.width + 'x' + img.height;
                extractionProgressElement.innerHTML = (percentage * 100).toFixed(2) + ' % completed. ' + framesSoFar + ' frames extracted.';
              });

 */
/**
 * Extracts the frame sequence of a video file.
 */

/*
function extractFramesFromVideo(config, file, progress) {
    let resolve = null;
    let db = null;
    let video = document.createElement('video');
    let canvas = document.createElement('canvas');
    let ctx = canvas.getContext('2d');
    let dimensionsInitialized = false;
    let totalFrames = 0;
    let processedFrames = 0;
    let lastApproxFrame = -1;
    let lastProgressFrame = -1;
    let attachmentName = 'img' + config.imageExtension;

    return new Promise((_resolve, _) => {
        resolve = _resolve;

        let dbName = 'vatic_js';
        db = new PouchDB(dbName).destroy().then(() => {
            db = new PouchDB(dbName);

            video.autoplay = false;
            video.muted = true;
            video.loop = false;
            video.playbackRate = config.playbackRate;
            video.src = URL.createObjectURL(file);
            compatibility.requestAnimationFrame(onBrowserAnimationFrame);
            video.play();
        });
    });

    function onBrowserAnimationFrame() {
        if (dimensionsInitialized && video.ended) {
            if (processedFrames == totalFrames) {
                videoEnded();
            }
            return;
        }

        compatibility.requestAnimationFrame(onBrowserAnimationFrame);

        if (video.readyState !== video.HAVE_CURRENT_DATA &&
            video.readyState !== video.HAVE_FUTURE_DATA &&
            video.readyState !== video.HAVE_ENOUGH_DATA) {
            return;
        }

        let currentApproxFrame = Math.round(video.currentTime * config.fps);
        if (currentApproxFrame != lastApproxFrame) {
            lastApproxFrame = currentApproxFrame;
            let frameNumber = totalFrames;
            totalFrames++;

            if (!dimensionsInitialized) {
                dimensionsInitialized = true;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            }

            ctx.drawImage(video, 0, 0);
            canvas.toBlob(
                (blob) => {
                    db.putAttachment(frameNumber.toString(), attachmentName, blob, config.imageMimeType).then((doc) => {
                        processedFrames++;

                        if (frameNumber > lastProgressFrame) {
                            lastProgressFrame = frameNumber;
                            progress(video.currentTime / video.duration, processedFrames, blob);
                        }

                        if (video.ended && processedFrames == totalFrames) {
                            videoEnded();
                        }
                    });
                },
                config.imageMimeType);
        }
    }

    function videoEnded() {
        if (video.src != '') {
            URL.revokeObjectURL(video.src);
            video.src = '';

            resolve({
                totalFrames: () => {
                    return totalFrames;
                },
                getFrame: (frameNumber) => {
                    return db.getAttachment(frameNumber.toString(), attachmentName);
                }
            });
        }
    }
}
*/
/**
 * Extracts the frame sequence from a previously generated zip file.
 */

/*
function extractFramesFromZip(config, file) {
    return new Promise((resolve, _) => {
        JSZip
            .loadAsync(file)
            .then((zip) => {
                let totalFrames = 0;
                for (let i = 0; ; i++) {
                    let file = zip.file(i + config.imageExtension);
                    if (file == null) {
                        totalFrames = i;
                        break;
                    }
                }

                resolve({
                    totalFrames: () => {
                        return totalFrames;
                    },
                    getFrame: (frameNumber) => {
                        return new Promise((resolve, _) => {
                            let file = zip.file(frameNumber + config.imageExtension);
                            file
                                .async('arraybuffer')
                                .then((content) => {
                                    let blob = new Blob([content], {type: config.imageMimeType});
                                    resolve(blob);
                                });
                        });
                    }
                });
            });
    });
}
*/
/**
 * Extracts the frame sequence from a previously generated zip file.
 */
function extractFramesFromZipUrl(config, url, segment) {
    return new Promise((resolve, _) => {
        //console.log(url);
        fetch(url)       // 1) fetch the url
            .then(function (response) {                       // 2) filter on 200 OK
                if (response.status === 200 || response.status === 0) {
                    return Promise.resolve(response.blob());
                } else {
                    return Promise.reject(new Error(response.statusText));
                }
            })
            .then(JSZip.loadAsync)                            // 3) chain with the zip promise
            .then(function (zip) {
                //console.log(zip);
                let first = 1;//(segment * 1000) + 1;
                let n = 0;
                let totalFrames = 0;
                for (let i = first; ; i++, n++) {
                    let s = '00000' + i;
                    let j = s.substr(-5);
                    //console.log(j + config.imageExtension);
                    let file = zip.file(j + config.imageExtension);
                    if (file == null) {
                        totalFrames = n;
                        break;
                    }
                }

                resolve({
                    totalFrames: () => {
                        return totalFrames;
                    },
                    getFrame: (frameNumber) => {
                        return new Promise((resolve, _) => {
                            let s = '00000' + frameNumber;
                            //console.log('getFrame ' + s);
                            let j = s.substr(-5);
                            //console.log(j + config.imageExtension);
                            let file = zip.file(j + config.imageExtension);
                            file
                                .async('arraybuffer')
                                .then((content) => {
                                    let blob = new Blob([content], {type: config.imageMimeType});
                                    resolve(blob);
                                });
                        });
                    }
                });
            });
    });
}

function getFrameFromUrl(url) {
    //console.log(url);
    return fetch(url)       // 1) fetch the url
        .then(function (response) {                       // 2) filter on 200 OK
            if (response.status === 200 || response.status === 0) {
                return Promise.resolve(response.blob());
            } else {
                return Promise.reject(new Error(response.statusText));
            }
        });
}

async function getFramesForObjects(framesManager, config) {
    framesManager.setConfig(config);
    //let framesToLoad = [];
    //framesToLoad.push(1);
    //for (var object of objectsToLoad) {
    //    let startFrame = parseInt(object.startFrame);
    //    let endFrame = parseInt(object.endFrame);
    //    for (let i = startFrame; i <= endFrame; i++) {
    //        framesToLoad.push(i);
    //    }
    //}
    //await Promise.all(framesToLoad.map(async (frameNumber) => {
    //    let f = '0000' + frameNumber;
        //let frame = await getFrameFromUrl(config.url + '/' + f.substr(-4) + config.imageExtension);
        //framesManager.addFrame(frameNumber, frame, config.imageMimeType);
    //}));

    return {
        totalFrames: () => {
            return framesToLoad.length;
        },
        getFrame: (frameNumber) => {
            //console.log('getting frame ' + frameNumber);
            return framesManager.getFrame(frameNumber);
            /*
            return framesManager.getFrame(frameNumber).then(function (blob) {
                resolve(blob);
            }).catch(function (err) {
                console.log(err);
            });;
            */
        }
    };
}

/**
 * Tracks point between two consecutive frames using optical flow.
 */
class OpticalFlow {
    constructor() {
        this.isInitialized = false;
        this.previousPyramid = new jsfeat.pyramid_t(3);
        this.currentPyramid = new jsfeat.pyramid_t(3);
    }

    init(imageData) {
        this.previousPyramid.allocate(imageData.width, imageData.height, jsfeat.U8_t | jsfeat.C1_t);
        this.currentPyramid.allocate(imageData.width, imageData.height, jsfeat.U8_t | jsfeat.C1_t);
        jsfeat.imgproc.grayscale(imageData.data, imageData.width, imageData.height, this.previousPyramid.data[0]);
        this.previousPyramid.build(this.previousPyramid.data[0]);
        this.isInitialized = true;
    }

    reset() {
        this.isInitialized = false;
    }

    track(imageData, bboxes) {
        if (!this.isInitialized) {
            throw 'not initialized';
        }
        //console.log('----tracking');
//console.log(imageData);
        jsfeat.imgproc.grayscale(imageData.data, imageData.width, imageData.height, this.currentPyramid.data[0]);
        this.currentPyramid.build(this.currentPyramid.data[0]);
//console.log(this.currentPyramid)
        // TODO: Move all configuration to config
        let bboxBorderWidth = 1;

        let pointsPerDimension = 11;
        let pointsPerObject = pointsPerDimension * pointsPerDimension;
        let pointsCountUpperBound = bboxes.length * pointsPerObject;
        let pointsStatus = new Uint8Array(pointsCountUpperBound);
        let previousPoints = new Float32Array(pointsCountUpperBound * 2);
        let currentPoints = new Float32Array(pointsCountUpperBound * 2);

        let pointsCount = 0;
        for (let i = 0, n = 0; i < bboxes.length; i++) {
            let bbox = bboxes[i];
            if (bbox != null) {
                for (let x = 0; x < pointsPerDimension; x++) {
                    for (let y = 0; y < pointsPerDimension; y++) {
                        previousPoints[pointsCount * 2] = bbox.x + x * (bbox.width / (pointsPerDimension - 1));
                        previousPoints[pointsCount * 2 + 1] = bbox.y + y * (bbox.height / (pointsPerDimension - 1));
                        pointsCount++;
                    }
                }
            }
        }
        if (pointsCount == 0) {
            throw 'no points to track';
        }

        jsfeat.optical_flow_lk.track(this.previousPyramid, this.currentPyramid, previousPoints, currentPoints, pointsCount, 30, 30, pointsStatus, 0.01, 0.001);
//console.log(previousPoints);
//console.log(currentPoints);

        //console.log(pointsStatus)
        let newBboxes = [];
        let p = 0;

        for (let i = 0; i < bboxes.length; i++) {
//            console.log('i = ' + i);
            let bbox = bboxes[i];
//            console.log(bbox);
            let newBbox = null;

            if (bbox != null) {
                let before = [];
                let after = [];
//console.log('pointsPerObject = ' + pointsPerObject)
                for (let j = 0; j < pointsPerObject; j++, p++) {
                    if (pointsStatus[p] == 1) {
                        let x = p * 2;
                        let y = x + 1;

                        before.push([previousPoints[x], previousPoints[y]]);
                        after.push([currentPoints[x], currentPoints[y]]);
                    }
                }
//console.log(before);
                if (before.length > 0) {
                    let diff = nudged.estimate('T', before, after);
                    let translation = diff.getTranslation();

                    let minX = Math.max(Math.round(bbox.x + translation[0]), 0);
                    let minY = Math.max(Math.round(bbox.y + translation[1]), 0);
                    let maxX = Math.min(Math.round(bbox.x + bbox.width + translation[0]), imageData.width - 2 * bboxBorderWidth);
                    let maxY = Math.min(Math.round(bbox.y + bbox.height + translation[1]), imageData.height - 2 * bboxBorderWidth);
                    let newWidth = maxX - minX;
                    let newHeight = maxY - minY;

                    if (newWidth > 0 && newHeight > 0) {
                        //le.log('!!! changing box');
                        newBbox = new BoundingBox(minX, minY, newWidth, newHeight);
                    }
                }
            }

            newBboxes.push(newBbox);
        }
//        console.log('---- end tracking');

        // Swap current and previous pyramids
        let oldPyramid = this.previousPyramid;
        this.previousPyramid = this.currentPyramid;
        this.currentPyramid = oldPyramid; // Buffer re-use

        return newBboxes;
    }
};

/**
 * Represents the coordinates of a bounding box
 */
class BoundingBox {
    constructor(x, y, width, height) {
        this.x = x;
        this.y = y;
        this.width = width;
        this.height = height;
    }
}

/**
 * Represents a bounding box at a particular frame.
 */
class AnnotatedFrame {
    constructor(frameNumber, bbox, isGroundTruth) {
        this.frameNumber = frameNumber;
        this.bbox = bbox;
        this.isGroundTruth = isGroundTruth;
        this.blocked = false;
    }

    isVisible() {
        return this.bbox != null;
    }
}

/**
 * Represents an object bounding boxes throughout the entire frame sequence.
 */
class AnnotatedObject {
    constructor() {
        this.frames = [];
        this.name = '';
        this.visible = true;
        this.hidden = false;
        this.locked = false;
        this.idFrame = -1;
        this.frame = '';
        this.idFE = -1;
        this.fe = '';
        this.color = 'white';
        this.idObject = -1;
        this.startFrame = -1;
        this.endFrame = -1;
    }

    add(frame) {
        //console.debug('adding frame in annotated object ' + this.idObject);
        //console.log(this.frames.length + '  frame number = ' + frame.frameNumber);
        for (let i = 0; i < this.frames.length; i++) {
            if (this.frames[i].frameNumber == frame.frameNumber) {
                this.frames[i] = frame;
                this.removeFramesToBeRecomputedFrom(i + 1);
                return;
            } else if (this.frames[i].frameNumber > frame.frameNumber) {
                this.frames.splice(i, 0, frame);
                this.removeFramesToBeRecomputedFrom(i + 1);
                this.injectInvisibleFrameAtOrigin();
                return;
            }
        }
        this.frames.push(frame);
        this.injectInvisibleFrameAtOrigin();
    }

    get(frameNumber) {
        /* Verifica se este AnnotatedObject existe no frame frameNumber */
        for (let i = 0; i < this.frames.length; i++) {
            let currentFrame = this.frames[i];
            if (currentFrame.frameNumber > frameNumber) {
                break;
            }

            if (currentFrame.frameNumber == frameNumber) {
                return currentFrame;
            }
        }

        return null;
    }

    inFrame(frameNumber) {
        return (this.startFrame <= frameNumber) && (this.endFrame >= frameNumber);
    }

    removeFrame(frameNumber) {
        for (let i = frameNumber; i < this.frames.length; i++) {
            let currentFrame = this.frames[i];
            if (currentFrame.frameNumber == frameNumber) {
                this.frames.splice(i, 1);
                return;
            }
        }
    }

    removeFramesToBeRecomputedFrom(frameNumber) {
        let count = 0;
        for (let i = frameNumber; i < this.frames.length; i++) {
            if (this.frames[i].isGroundTruth) {
                break;
            }
            count++;
        }
        if (count > 0) {
            this.frames.splice(frameNumber, count);
        }
    }

    injectInvisibleFrameAtOrigin() {
        if (this.frames.length == 0 || this.frames[0].frameNumber > 0) {
            this.frames.splice(0, 0, new AnnotatedFrame(0, null, false));
        }
    }
}

/**
 * Tracks annotated objects throughout a frame sequence using optical flow.
 */
class AnnotatedObjectsTracker {
    constructor(framesManager) {
        this.framesManager = framesManager;
        this.annotatedObjects = [];
        this.opticalFlow = new OpticalFlow();
        this.lastFrame = -1;
        this.ctx = document.createElement('canvas').getContext('2d');

        this.framesManager.onReset.push(() => {
            this.annotatedObjects = [];
            this.lastFrame = -1;
        });
    }

    getLength() {
        return this.annotatedObjects.length;
    }

    add(annotatedObject) {
        this.annotatedObjects.push(annotatedObject)
    }

    remove(i) {
        this.annotatedObjects.splice(i, 1);
    }

    clear(annotatedObject) {
        for (let i = 0; i < this.annotatedObjects.length; i++) {
            if (this.annotatedObjects[i].idObject == annotatedObject.idObject) {
                this.remove(i);
            }
        }
    }

    clearAll() {
        for (let i = 0; i < this.annotatedObjects.length; i++) {
            this.remove(i);
        }
    }

    getObjectsByFrame(frameNumber) {
        let result = [];
        for (let i = 0; i < this.annotatedObjects.length; i++) {
            let annotatedObject = this.annotatedObjects[i];
            let annotatedFrame = annotatedObject.get(frameNumber);
            if (annotatedFrame != null) {
                if (annotatedFrame.frameNumber == frameNumber) {
                    result.push({annotatedObject: annotatedObject, annotatedFrame: annotatedFrame});
                }
            }
        }
        return result;
    }

    getFrameWithObjects(frameNumber) {
        return new Promise((resolve, _) => {
            let i = this.startFrame(frameNumber);
            //console.log('startFrame = ' + i);
//console.log('getFrameWithObjects frameNumber = ' + frameNumber + '  i = ' + i);
            let trackNextFrame = () => {
                //console.log('tracking frame ' + i);
                this.track(i).then((frameWithObjects) => {
                    if (i == frameNumber) {
                        //console.log('i == frameNumber')
                        resolve(frameWithObjects);
                    } else {
                        i++;
                        //console.log('trackNextFrame i = ' + i)
                        trackNextFrame();
                    }
                });
            };

            trackNextFrame();
        });
    }

    startFrame(frameNumber) {
        let start = frameNumber;
        for (let i = 0; i < this.annotatedObjects.length; i++) {
            let annotatedObject = this.annotatedObjects[i];
            if (annotatedObject.inFrame(frameNumber)) { // objeto está no frame
                let hasStart = false;
                let objectStart = frameNumber;
                while (annotatedObject.startFrame <= objectStart) {
                    if (annotatedObject.get(objectStart) == null) { // mas tem dados da bbox
                        objectStart--;
                    } else {
                        hasStart = true;
                        if (objectStart < start) {
                            start = objectStart;
                        }
                        break;
                    }
                }
                if (!hasStart) {
                    throw 'corrupted object annotations';
                }
            }
        }
        //console.log('startFrame = ' + start);
        return start;

        /*  Está verificando se todos os objetos estão em pelo menos um frame - vou fazer isso no Save
        for (; frameNumber >= 0; frameNumber--) {
            let allObjectsHaveData = true;

            for (let i = 0; i < this.annotatedObjects.length; i++) {
                let annotatedObject = this.annotatedObjects[i];
                if (annotatedObject.get(frameNumber) == null) {
                    allObjectsHaveData = false;
                    break;
                }
            }

            if (allObjectsHaveData) {
                return frameNumber;
            }
        }

        throw 'corrupted object annotations';
        */
    }

    track(frameNumber) {
        return new Promise((resolve, _) => {
            this.framesManager.getFrame(frameNumber).then((blob) => {
                //console.log('track framenumber ' + frameNumber);
                //console.log(blob);
                blobToImage(blob).then((img) => {
                    let result = [];
                    let toCompute = [];
                    for (let i = 0; i < this.annotatedObjects.length; i++) {
                        let annotatedObject = this.annotatedObjects[i];
                        if (annotatedObject.inFrame(frameNumber)) {
                            //console.log('track object ' + annotatedObject.idObject + '  frameNumber = ' + frameNumber)
                            let annotatedFrame = annotatedObject.get(frameNumber);
                            //console.log(annotatedFrame);
                            if (annotatedFrame == null) { /* não existe o AnnotatedObject no frame frameNumber */
                                annotatedFrame = annotatedObject.get(frameNumber - 1);
                                if (annotatedFrame == null) { /* também não existe no anterior */
                                    //throw 'tracking must be done sequentially';
                                    continue; // passa para o próximo AnnotatedObject
                                }
                                //console.log('to compute');
                                //console.log(annotatedFrame.bbox)
                                /* existe no frame anterior mas não no corrent, então é preciso calcular a nova box */
                                toCompute.push({annotatedObject: annotatedObject, bbox: annotatedFrame.bbox});
                            } else {
                                /* existe o AnnotatedObject no frame frameNumber, então coloca-o no array result */
                                result.push({annotatedObject: annotatedObject, annotatedFrame: annotatedFrame});
                            }
                        }
                    }

                    let bboxes = toCompute.map(c => c.bbox);
                    //console.log(bboxes)
                    let hasAnyBbox = bboxes.some(bbox => bbox != null);
                    //console.log(hasAnyBbox)
                    let optionalOpticalFlowInit;
                    if (hasAnyBbox) { // se tem alguma box para ser calculada
                        optionalOpticalFlowInit = this.initOpticalFlow(frameNumber - 1);
                    } else {
                        optionalOpticalFlowInit = new Promise((r, _) => {
                            r();
                        });
                    }

                    optionalOpticalFlowInit.then(() => {
                        let newBboxes;
                        if (hasAnyBbox) {
                            //console.log('tracking lastFrame = ' + frameNumber)
                            let imageData = this.imageData(img);
                            newBboxes = this.opticalFlow.track(imageData, bboxes);
                            this.lastFrame = frameNumber;
                        } else {
                            //console.log('newBboxes = bboxes');
                            newBboxes = bboxes;
                        }
//console.log(newBboxes);
                        for (let i = 0; i < toCompute.length; i++) {
                            let annotatedObject = toCompute[i].annotatedObject;
                            let annotatedFrame = new AnnotatedFrame(frameNumber, newBboxes[i], false);
                            annotatedObject.add(annotatedFrame);
                            result.push({annotatedObject: annotatedObject, annotatedFrame: annotatedFrame});
                        }
                        resolve({img: img, objects: result});
                    });
                });
            });
        });
    }

    initOpticalFlow(frameNumber) {
        return new Promise((resolve, _) => {
            //console.log('initOpticalFlow lastFrame = ' + this.lastFrame + '   frameNumber = ' + frameNumber);
            if (this.lastFrame != -1 && this.lastFrame == frameNumber) {
                resolve();
            } else {
                this.opticalFlow.reset();
                //let blob = this.framesManager.getFrame(frameNumber);
                this.framesManager.getFrame(frameNumber).then((blob) => {
                    blobToImage(blob).then((img) => {
                        let imageData = this.imageData(img);
                        this.opticalFlow.init(imageData);
                        this.lastFrame = frameNumber;
                        resolve();
                    });
                });
            }
        });
    }

    imageData(img) {
        let canvas = this.ctx.canvas;
        canvas.width = img.width;
        canvas.height = img.height;
        this.ctx.drawImage(img, 0, 0);
        let imageData = this.ctx.getImageData(0, 0, canvas.width, canvas.height);
        return imageData;
    }
};
