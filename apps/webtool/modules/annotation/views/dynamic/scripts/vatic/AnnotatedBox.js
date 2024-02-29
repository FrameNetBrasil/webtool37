"use strict";

/**
 * Represents a bounding box associated to an (image) object.
 */
class AnnotatedBox {
    constructor(id, dom, bbox, isGroundTruth, idObject) {
        this.id = id;
        this.dom = dom;
        this.bbox = bbox;
        this.isGroundTruth = isGroundTruth;
        this.blocked = false;
        this.idObject = idObject;
        this.status = 0;
    }

    isVisible() {
        return this.bbox != null;
    }
}
