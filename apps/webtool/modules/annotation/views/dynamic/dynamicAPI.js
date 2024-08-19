let dynamicAPI = {
    loadObjects: () => {
        return new Promise((resolve, reject) => {
            let url = "/index.php/webtool/annotation/dynamic/loadObjects";
            let objects = [];
            manager.doAjax(url, (response) => {
                resolve(response)
            }, {
                id: annotationVideoModel.documentMM.idDocumentMM
            });
        })
    },
    deleteObjects: (toDelete) => {
        let params = {
            toDelete: toDelete,
        }
        try {
            let url = "/index.php/webtool/annotation/dynamic/deleteObjects";
            manager.doAjax(url, (response) => {
                if (response.type === 'success') {
                    // $.messager.alert('Ok', 'Objects deleted.','info');
                } else if (response.type === 'error') {
                    throw new Error(response.message);
                }
            }, params);
        } catch (e) {
            $.messager.alert('Error', e.message,'error');
        }
    },
    listSentences: (idDocumentMM) => {
        let url = "/index.php/webtool/annotation/dynamic/sentences/" + idDocumentMM;
        let sentences = [];
        manager.doAjax(url, (response) => {
            sentences = response;
        }, {});
        return sentences;
    },
    listFrame: () => {
        let url = "/index.php/webtool/data/frame/combobox";
        let frames = [];
        manager.doAjax(url, (response) => {
            frames = response;
        }, {});
        return frames;
    },
    listFrameElement: () => {
        let url = "/index.php/webtool/data/frameelement/listAllDecorated";
        let frames = [];
        manager.doAjax(url, (response) => {
            frames = response;
        }, {});
        return frames;
    },
    updateObject: (params) => {
        return new Promise((resolve, reject) => {
            try {
                let result = {};
                let url = "/index.php/webtool/annotation/dynamic/updateObject";
                manager.doAjax(url, (response) => {
                    if (response.type === 'success') {
                        resolve(response.data);
                    } else if (response.type === 'error') {
                        throw new Error(response.message);
                    }
                }, params);

            } catch (e) {
                $.messager.alert('Error', e.message, 'error');
            }
        })
    },
    updateObjectData: (params) => {
        return new Promise((resolve, reject) => {
            try {
                let result = {};
                let url = "/index.php/webtool/annotation/dynamic/updateObjectData";
                manager.doAjax(url, (response) => {
                    if (response.type === 'success') {
                        resolve(response.data);
                    } else if (response.type === 'error') {
                        throw new Error(response.message);
                    }
                }, params);

            } catch (e) {
                $.messager.alert('Error', e.message, 'error');
            }
        })
    }

}