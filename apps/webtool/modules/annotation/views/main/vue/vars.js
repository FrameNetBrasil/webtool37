// _store object in memory before save to database
class AnnotatedObjectsSet {
    constructor() {
        this.currentId = 0;
        this.annotatedObjects = [];
    }

    find(id) {
        let j = -1;
        for (var i = 0; i < this.annotatedObjects.length; i++) {
            if (this.annotatedObjects[i].id == id) {
                j = i;
            }
        }
        return j;
    }

    add(annotatedObject) {
        this.currentId++;
        annotatedObject.id = this.currentId;
        this.annotatedObjects.push(annotatedObject);
    }

    get(id) {
        let i = this.find(id);
        return (i > -1) ? this.annotatedObjects[i] : null;
    }

    set(id, annotatedObject) {
        let i = this.find(id);
        if (i > -1) {
            console.log('setting');
            this.annotatedObjects[i] = annotatedObject;
        }
    }

    remove(annotatedObject) {
        let i = this.find(id);
        if (i > -1) {
            this.annotatedObjects.slice(i, 1);
        }
    }
}
