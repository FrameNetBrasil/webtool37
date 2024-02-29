let annotationVideoStore = new Vuex.Store({
    state: {
        // model: {},
        // objectsTracker: null,
        objectsTrackerState: 'clean', // dirty
        // objects: [],
        currentObject: null,
        currentObjectState: 'none',// creating, created, selected, editingFE, editingBox, stopping, updated
        currentObjectStateFlag: 0,
        // //idObjectSelected: -1,
        currentRowGrid: 0,
        // annotatedObject: null,
        currentMode: 'video',
        currentState: 'paused',
        //
        // framesManager: new FramesManager(),
        currentFrame: 1,
        currentStopFrame: 0,
        // playFrame: 0,
        // totalFrames: 0,
        // framesRange: {},
        currentTime: 0,
        // endTime: 0,
        //
        currentVideoState: 'loading', // playing, paused, loading, loaded
        currentSliderPosition: 0,
        //
        // redrawFrame: false,
        // videoLoaded: false,
        // video2Loaded: false,
        // objectLoaded: false,
    },
    getters: {
        currentFrame(state) {
            return state.currentFrame
        },
        currentStopFrame(state) {
            return state.currentStopFrame
        },
        currentTime(state) {
            return state.currentTime
        },
        // endTime(state) {
        //     return state.endTime
        // },
        // playFrame(state) {
        //     return state.playFrame
        // },
        currentVideoState(state) {
            return state.currentVideoState
        },
        currentObject(state) {
            return state.currentObject
        },
        currentObjectState(state) {
            return state.currentObjectState
        },
        currentRowGrid(state) {
            return state.currentRowGrid
        },
        currentSliderPosition(state) {
            return state.currentSliderPosition
        },
        currentObjectStateFlag(state) {
            return state.currentObjectStateFlag
        },
        currentMode(state) {
            return state.currentMode
        },
        currentState(state) {
            return state.currentState
        },
        // // idObjectSelected(state) {
        // //     return state.idObjectSelected
        // // },
        // framesRange(state) {
        //     return state.framesRange
        // },
        // objectsTracker(state) {
        //     return state.objectsTracker;
        // },
        objectsTrackerState(state) {
            return state.objectsTrackerState;
        },
        annotatedObject: (state) => (id) => {
            return annotationVideoModel.objectsTracker.get(id);
        },
        // allAnnotatedObjects(state) {
        //     return state.objectsTracker.annotatedObjects;
        // },
        // videoLoaded(state) {
        //     //return state.videoLoaded && state.video2Loaded;
        //     return state.videoLoaded;
        // },
        // allLoaded(state) {
        //     //return state.videoLoaded && state.video2Loaded && state.objectLoaded;
        //     return state.videoLoaded && state.objectLoaded;
        // }
    },
    mutations: {
        // objects(state, value) {
        //     state.objects = value;
        // },
        currentFrame(state, value) {
            state.currentTime = (value - 1) / annotationVideoModel.fps;
            state.currentFrame = value;
        },
        currentStopFrame(state, value) {
            state.currentStopFrame = value;
        },
        currentRowGrid(state, value) {
            state.currentRowGrid = value;
        },
        // endTime(state, value) {
        //     state.endTime = value;
        // },
        // playFrame(state, value) {
        //     state.playFrame = value;
        // },
        currentSliderPosition(state, value) {
            state.currentSliderPosition = value;
        },
        currentVideoState(state, value) {
            state.currentVideoState = value;
        },
        currentObject(state, value) {
            state.currentObject = value;
        },
        currentObjectState(state, value) {
            state.currentObjectState = value;
        },
        currentObjectStateFlag(state, value) {
            state.currentObjectStateFlag = value;
        },
        currentMode(state, value) {
            state.currentMode = value;
        },
        currentState(state, value) {
            state.currentState = value;
        },
        // // idObjectSelected(state, value) {
        // //     console.log('mutation idObjectSelected', value);
        // //     state.idObjectSelected = value;
        // // },
        // totalFrames(state, value) {
        //     state.totalFrames = value;
        // },
        // model(state, model) {
        //     state.model = model;
        // },
        // annotation(state, annotation) {
        //     state.annotation = annotation;
        // },
        // framesRange(state, value) {
        //     state.framesRange = value;
        // },
        // objectsTracker(state, value) {
        //     state.objectsTracker = value;
        // },
        objectsTrackerState(state, value) {
            state.objectsTrackerState = value;
        },
        // redrawFrame(state, value) {
        //     state.redrawFrame = value;
        // },
        // videoLoaded(state, value) {
        //     state.videoLoaded = true;
        // },
        // video2Loaded(state, value) {
        //     state.video2Loaded = true;
        // },
        // objectLoaded(state, value) {
        //     state.objectLoaded = true;
        // },
    },
    actions: {
        // setEndTime(context, endTime) {
        //     context.commit('endTime', endTime);
        //     context.commit('totalFrames', endTime * context.state.model.fps);
        // },
        // updateFramesRange(context, framesRange) {
        //     context.commit('framesRange', framesRange);
        //
        // },
        // objectsTrackerInit(context) {
        //     context.commit('objectsTracker', new AnnotatedObjectsTracker(context.state.framesManager));
        //     console.log('objectsTrackerInit ok')
        // },
        objectsTrackerAdd(context, annotatedObject) {
            // annotatedObject.idObject = context.state.objectsTracker.getLength() + 1;
            // context.state.objectsTracker.add(annotatedObject);
            annotationVideoModel.objectsTracker.add(annotatedObject);
            context.commit('currentObject', annotatedObject);
            context.commit('idObjectSelected', annotatedObject.idObject);
            context.commit('currentObjectState', 'created');
            context.commit('objectsTrackerState', 'dirty');
            context.commit('currentState', 'editing');
            console.log('new Object');
        },
        // objectsTrackerPush(context, annotatedObject) { // use to loaded objects in objectsTracker
        //     context.state.objectsTracker.add(annotatedObject);
        // },
        // objectsTrackerClear(context, annotatedObject) {
        //     context.state.objectsTracker.clear(annotatedObject);
        // },
        // objectsTrackerClearAll(context) {
        //     context.state.objectsTracker.clearAll();
        // },
        clearAnnotatedObject(context, idObject) {
            let annotatedObject = context.getters.annotatedObject(idObject);
            $(annotatedObject.dom).remove();
            context.commit('currentObjectState', '');
            context.commit('currentObject', null);
            annotationVideoModel.tracker.remove(idObject);
            //context.commit('objectsTrackerState', 'dirty');
        },
        newObject(context) {
            context.commit('currentObjectState', 'creating')
        },
        selectObject(context, idObject) {
            // let idObjectSelected = context.state.idObjectSelected;
            // if ((idObject === idObjectSelected) && (context.state.currentObjectState === 'selected')) {
            if (context.state.currentObject && (context.state.currentObject.idObject === idObject)) {
                context.commit('currentObjectState', '');
                context.commit('currentObject', null);
                //context.commit('idObjectSelected', -1);
            } else {
                let annotatedObject = annotationVideoModel.objectsTracker.get(idObject);
                if (annotatedObject) {
                    context.commit('currentObject', annotatedObject);
                    //context.commit('idObjectSelected', idObject);
                    context.commit('currentObjectState', 'selected');
                }
            }
        },
        // lockObject(context, idObject) {
        //     let annotatedObject = context.getters.annotatedObject(idObject);
        //     if (annotatedObject) {
        //         annotatedObject.locked = !annotatedObject.locked;
        //         context.commit('objectsTrackerState', 'dirty');
        //     }
        // },
        // hideObject(context, idObject) {
        //     let annotatedObject = context.getters.annotatedObject(idObject);
        //     if (annotatedObject) {
        //         annotatedObject.hidden = !annotatedObject.hidden;
        //         context.commit('redrawFrame', true)
        //         context.commit('objectsTrackerState', 'dirty');
        //     }
        // },
        // updateObject(context, updatedObject) {
        //     let idObject = updatedObject.idObject;
        //     console.log('updating ' + idObject);
        //     let annotatedObject = context.getters.annotatedObject(idObject);
        //     if (annotatedObject) {
        //         annotatedObject.idFrame = updatedObject.idFrame;
        //         annotatedObject.frame = updatedObject.frame;
        //         annotatedObject.idFE = updatedObject.idFE;
        //         annotatedObject.fe = updatedObject.fe;
        //         annotatedObject.color = updatedObject.color;
        //         annotatedObject.idObjectMM = updatedObject.idObjectMM;
        //         context.commit('currentObject', annotatedObject)
        //         //context.commit('idObjectSelected', idObject)
        //         context.commit('currentObjectState', 'updated')
        //         context.commit('objectsTrackerState', 'dirty');
        //     }
        // },
        endObject(context) {
            if (context.state.currentObject) {
                let idObject = context.state.currentObject.idObject;
                console.log('end ' + idObject);
                let annotatedObject = context.getters.annotatedObject(idObject);
                if (annotatedObject) {
                    annotatedObject.endFrame = context.state.currentFrame - 1;
                    annotatedObject.dom.style.display = 'none';
                    annotatedObject.add(new AnnotatedFrame(context.state.currentFrame, null, true));
                    //context.commit('currentObject', null)
                    //context.commit('idObjectSelected', -1)
                    annotatedObject.setState('dirty');
                    context.commit('currentObjectState', 'updated')
                    context.commit('objectsTrackerState', 'dirty');
                    context.commit('currentObjectStateFlag', annotatedObject.endFrame);
                }
            }
        },
        startTrackObject(context) {
            if (context.state.currentObject) {
                let idObject = context.state.currentObject.idObject;
                console.log('startTrack ' + idObject);
                let annotatedObject = context.getters.annotatedObject(idObject);
                if (annotatedObject) {
                    annotatedObject.endFrame = annotationVideoModel.framesRange.last;
                    context.commit('currentObject', annotatedObject);
                    context.commit('currentObjectState', 'editing');
                    //context.commit('objectsTrackerState', 'clean');
                    console.log('object tracking - endFrame = ', annotatedObject.endFrame);
                }
            }
        },
        objectBlocked(context) {
            if (context.state.currentObject) {
                let idObject = context.state.currentObject.idObject;
                let annotatedObject = context.getters.annotatedObject(idObject);
                if (annotatedObject) {
                    let frame = annotatedObject.get(context.state.currentFrame);
                    if (frame) {
                        if (!frame.blocked) {
                            frame.blocked = true;
                            frame.isGroundTruth = true;
                            console.log(frame);
                            context.commit('redrawFrame', true)
                        }
                    }
                }
            }
        },
        objectVisible(context) {
            if (context.state.currentObject) {
                let idObject = context.state.currentObject.idObject;
                let annotatedObject = context.getters.annotatedObject(idObject);
                if (annotatedObject) {
                    let frame = annotatedObject.get(context.state.currentFrame);
                    if (frame) {
                        if (frame.blocked) {
                            frame.blocked = false;
                            frame.isGroundTruth = true;
                            console.log(frame);
                            context.commit('redrawFrame', true)
                        }
                    }
                }
            }
        },
        clearObject(context) {
            if (context.state.currentObject) {
                let idObject = context.state.currentObject.idObject;
                let annotatedObject = context.getters.annotatedObject(idObject);
                if (annotatedObject) {
                    annotatedObject.removeFrame(context.state.currentFrame);
                    context.commit('currentObjectState', 'cleared');
                    context.commit('objectsTrackerState', 'dirty');
                }
            }
        },
        deleteObject(context) {
            if (context.state.currentObject) {
                let idObject = context.state.currentObject.idObject;
                let annotatedObject = context.getters.annotatedObject(idObject);
                if (annotatedObject) {
                    context.commit('currentObjectState', '');
                    context.commit('currentObject', null);
                    annotationVideoModel.objectsTracker.clear(annotatedObject);
                    //context.dispatch('objectsTrackerClear', annotatedObject);
                    context.commit('currentObjectState', 'cleared');
                    context.commit('objectsTrackerState', 'dirty');
                }
            }
        },
        deleteObjectById(context, idObject) {
            let annotatedObject = context.getters.annotatedObject(idObject);
            if (annotatedObject) {
                context.commit('currentObjectState', '');
                context.commit('currentObject', null);
                annotationVideoModel.objectsTracker.clear(annotatedObject);
                context.commit('currentObjectState', 'cleared');
            }
        },
        setObjectState(context, params) { // force update ObjectPane
            console.log(params.object);
            if (params.object) {
                console.log('changing state', params.state);
                params.object.setState(params.state);
                context.commit('currentObjectState', 'editingBox');
                console.log('changing flag', params.flag);
                context.commit('currentObjectStateFlag', params.flag);
            }
        },
    },
})

