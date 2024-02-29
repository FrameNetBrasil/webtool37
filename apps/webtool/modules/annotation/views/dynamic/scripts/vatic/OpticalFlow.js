"use strict";

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
            console.log('not initialized');
            throw 'not initialized';
        }
        //console.log('opticalflow tracking');
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
            //console.log(bbox);
            let newBbox = null;

            if (bbox != null) {
                let before = [];
                let after = [];
//console.log('pointsPerObject = ' + pointsPerObject)
                //console.log(pointsStatus);
                for (let j = 0; j < pointsPerObject; j++, p++) {
                    if (pointsStatus[p] === 1) {
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
                        //console.log('!!! changing box');
                        newBbox = new BoundingBox(minX, minY, newWidth, newHeight);
                    }
                } else {
                    newBbox = new BoundingBox(bbox.x, bbox.y, bbox.width, bbox.height);
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

