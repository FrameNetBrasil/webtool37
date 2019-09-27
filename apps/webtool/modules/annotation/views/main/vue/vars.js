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

vue.$store.commit('annotation', {
    fps: 30,
    // Low rate decreases the chance of losing frames with poor browser performances
    playbackRate: 0.4,
    // currentTime of the video
    currentTime: 0,
    annotatedObjectsSet: new AnnotatedObjectsSet(),
    // object on modal form
    editObject: {},
})

let annotation = {
    // action on modal form: update object & grid
    updateObject() {
        console.log('update object');
        let state = store.state.annotation;
        state.editObject.idFE = $('#lookupFE').combogrid('getValue');
        this.editObject.fe = $('#lookupFrame').combogrid('getText') + '.' + $('#lookupFE').combogrid('getText');
        console.log(this.editObject);
        this.annotatedObjectsSet.set(this.editObject.idObject, this.editObject);
        updateObjectsGrid();
        $('#dlgObject').dialog('close');
    },
    // action on modal form: set the endTime of object
    endObject() {
        console.log(this.editObject);
        this.annotatedObjectsSet.get(this.editObject.idObject).endTime = this.currentTime;
        updateObjectsGrid();
        $('#dlgObject').dialog('close');
    }
}
