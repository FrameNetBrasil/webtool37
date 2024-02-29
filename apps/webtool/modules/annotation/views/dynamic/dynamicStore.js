let dynamicStore = new Vuex.Store({
    state: {
        //objectsTrackerState: 'clean', // dirty
        currentObject: null,
        //currentObjectState: 'none',// creating, created, selected, editingFE, editingBox, stopping, updated
        //currentObjectStateFlag: 0,
        currentIdObjectMM: - 1,
        currentRowGrid: 0,
        //currentMode: 'video',
        currentState: 'videoPaused', // videoPlaying, videoPaused, videoPlaying2, videoPlaying5, videoPlaying8, videoDragging, objectCreating, objectEditing, objectTracking, objectPaused
        currentFrame: 1,
        currentStopFrame: 1,
        currentTime: 0,
        currentVideoState: 'loading', // playing, paused, loading, loaded
        currentSliderPosition: 0,
        objects: [],
        updateGridPane: false,
        updateObjectPane: false,
        redrawFrame: false,
        showBoxes: true,
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
        currentVideoState(state) {
            return state.currentVideoState
        },
        currentObject(state) {
            return state.currentObject
        },
        currentIdObjectMM(state) {
            return state.currentIdObjectMM
        },
        // currentObjectState(state) {
        //     return state.currentObjectState
        // },
        currentRowGrid(state) {
            return state.currentRowGrid
        },
        currentSliderPosition(state) {
            return state.currentSliderPosition
        },
        // currentObjectStateFlag(state) {
        //     return state.currentObjectStateFlag
        // },
        // currentMode(state) {
        //     return state.currentMode
        // },
        currentState(state) {
            return state.currentState
        },
        // objectsTrackerState(state) {
        //     return state.objectsTrackerState;
        // },
        annotatedObject: (state) => (id) => {
            return dynamicObjects.get(id);
        },
        objects(state) {
            return state.objects
        },
        updateGridPane(state) {
            return state.updateGridPane
        },
        updateObjectPane(state) {
            return state.updateObjectPane
        },
        redrawFrame(state) {
            return state.redrawFrame
        },
        showBoxes(state) {
            return state.showBoxes
        },
    },
    mutations: {
        currentFrame(state, value) {
            value = parseInt(value);
            if (value === 0) {
                value = 1;
            }
            state.currentTime = (value - 1) / annotationVideoModel.fps;
            state.currentFrame = value;
        },
        currentStopFrame(state, value) {
            state.currentStopFrame = value;
        },
        currentRowGrid(state, value) {
            state.currentRowGrid = value;
        },
        currentSliderPosition(state, value) {
            state.currentSliderPosition = value;
        },
        currentVideoState(state, value) {
            state.currentVideoState = value;
        },
        currentObject(state, value) {
            state.currentObject = value;
            if (value) {
                state.currentIdObjectMM = value.idObjectMM;
            }
        },
        currentIdObjectMM(state, value) {
            state.currentIdObjectMM = value;
        },
        // currentObjectState(state, value) {
        //     state.currentObjectState = value;
        // },
        // currentObjectStateFlag(state, value) {
        //     state.currentObjectStateFlag = value;
        // },
        // currentMode(state, value) {
        //     state.currentMode = value;
        // },
        currentState(state, value) {
            state.currentState = value;
        },
        // objectsTrackerState(state, value) {
        //     state.objectsTrackerState = value;
        // },
        objects(state, value) {
            state.objects = value;
        },
        updateGridPane(state, value) {
            state.updateGridPane = value;
        },
        updateObjectPane(state, value) {
            state.updateObjectPane = value;
        },
        redrawFrame(state, value) {
            state.redrawFrame = value;
        },
        showBoxes(state, value) {
            state.showBoxes = value;
        },
    },
    actions: {
        showObject(context, idObject) {
            let annotatedObject = dynamicObjects.get(idObject);
            annotatedObject.hidden = false;
        },
        hideObject(context, idObject) {
            let annotatedObject = dynamicObjects.get(idObject);
            annotatedObject.hidden = true;
        },
        currentObjectEndFrame(context, endFrame) {
            if (context.state.currentObject) {
               let idObject = context.state.currentObject.idObject;
               let annotatedObject = context.getters.annotatedObject(idObject);
               if (annotatedObject) {
                   context.state.currentObject.endFrame = endFrame;
                   annotatedObject.endFrame = endFrame;
               }
            }
        },
        // objectsTrackerAdd(context, annotatedObject) {
        //     dynamicObjects.add(annotatedObject);
        //     context.commit('currentObject', annotatedObject);
        //     context.commit('idObjectSelected', annotatedObject.idObject);
        //     context.commit('currentObjectState', 'created');
        //     context.commit('objectsTrackerState', 'dirty');
        //     context.commit('currentState', 'editing');
        //     console.log('new Object');
        // },
        // clearAnnotatedObject(context, idObject) {
        //     let annotatedObject = context.getters.annotatedObject(idObject);
        //     $(annotatedObject.dom).remove();
        //     context.commit('currentObjectState', '');
        //     context.commit('currentObject', null);
        //     annotationVideoModel.tracker.remove(idObject);
        // },
        // findObjectByIdObjectMM(context, idObjectMM) {
        //     let annotatedObject = dynamicObjects.getByIdObjectMM(idObjectMM);
        //     if (annotatedObject) {
        //         context.commit('currentObject', annotatedObject);
        //         // context.commit('currentObjectState', 'selected');
        //     }
        // },
        // newObject(context) {
        //     // context.commit('currentObjectState', 'creating')
        // },
        selectObject(context, idObject) {
            if (context.state.currentObject && (context.state.currentObject.idObject === idObject)) {
                // context.commit('currentObjectState', '');
                context.commit('currentObject', null);
            } else {
                let annotatedObject = dynamicObjects.get(idObject);
                if (annotatedObject) {
                    context.commit('currentObject', annotatedObject);
                    // context.commit('currentObjectState', 'selected');
                }
            }
        },
        selectObjectMM(context, idObjectMM) {
            let annotatedObject = dynamicObjects.getByIdObjectMM(idObjectMM);
            console.log('selectObjectMM', idObjectMM, annotatedObject);
            if (annotatedObject) {
                context.commit('currentObject', annotatedObject);
                // context.commit('currentObjectState', 'selected');
            }
        },
        // endObject(context) {
        //     if (context.state.currentObject) {
        //         let idObject = context.state.currentObject.idObject;
        //         console.log('end ' + idObject);
        //         let annotatedObject = context.getters.annotatedObject(idObject);
        //         if (annotatedObject) {
        //             console.log('endObject',annotatedObject);
                    // annotatedObject.endFrame = context.state.currentFrame - 1;
                    // annotatedObject.dom.style.display = 'none';
                    // annotatedObject.add(new AnnotatedFrame(context.state.currentFrame, null, true));
                    // annotatedObject.setState('dirty');
                    // context.commit('currentObjectState', 'updated')
                    // context.commit('objectsTrackerState', 'dirty');
                    // context.commit('currentObjectStateFlag', annotatedObject.endFrame);
        //         }
        //     }
        // },
        // startTrackObject(context) {
        //     if (context.state.currentObject) {
        //         let idObject = context.state.currentObject.idObject;
        //         console.log('startTrack ' + idObject);
        //         let annotatedObject = context.getters.annotatedObject(idObject);
        //         if (annotatedObject) {
        //             annotatedObject.endFrame = annotationVideoModel.framesRange.last;
        //             context.commit('currentObject', annotatedObject);
        //             // context.commit('currentObjectState', 'editing');
        //             console.log('object tracking - endFrame = ', annotatedObject.endFrame);
        //         }
        //     }
        // },
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
                            dynamicObjects.saveCurrentObject();
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
                            dynamicObjects.saveCurrentObject();
                        }
                    }
                }
            }
        },
        // clearObject(context) {
        //     if (context.state.currentObject) {
        //         let idObject = context.state.currentObject.idObject;
        //         let annotatedObject = context.getters.annotatedObject(idObject);
        //         if (annotatedObject) {
        //             annotatedObject.removeFrame(context.state.currentFrame);
        //             context.commit('currentObjectState', 'cleared');
        //             context.commit('objectsTrackerState', 'dirty');
        //         }
        //     }
        // },
        // deleteObject(context) {
        //     if (context.state.currentObject) {
        //         let idObject = context.state.currentObject.idObject;
        //         let annotatedObject = context.getters.annotatedObject(idObject);
        //         if (annotatedObject) {
        //             context.commit('currentObjectState', '');
        //             context.commit('currentObject', null);
        //             dynamicObjects.clear(annotatedObject);
        //             context.commit('currentObjectState', 'cleared');
        //             context.commit('objectsTrackerState', 'dirty');
        //         }
        //     }
        // },
        // deleteObjectById(context, idObject) {
        //     let annotatedObject = context.getters.annotatedObject(idObject);
        //     if (annotatedObject) {
        //         context.commit('currentObjectState', '');
        //         context.commit('currentObject', null);
        //         dynamicObjects.clear(annotatedObject);
        //         context.commit('currentObjectState', 'cleared');
        //     }
        // },
        // setObjectState(context, params) { // force update ObjectPane
        //     console.log(params.object);
        //     if (params.object) {
        //         console.log('changing state', params.state);
        //         params.object.setState(params.state);
        //         context.commit('currentObjectState', 'editingBox');
        //         console.log('changing flag', params.flag);
        //         context.commit('currentObjectStateFlag', params.flag);
        //     }
        // },
    },
})

