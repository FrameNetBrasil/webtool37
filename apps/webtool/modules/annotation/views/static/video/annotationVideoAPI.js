let annotationVideoAPI = {
    loadObjects: () => {
        return new Promise((resolve, reject) => {
            let url = "/index.php/webtool/annotation/multimodal/loadObjects";
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
            let url = "/index.php/webtool/annotation/multimodal/deleteObjects";
            manager.doAjax(url, (response) => {
                if (response.type === 'success') {
                    $.messager.alert('Ok', 'Objects deleted.','info');
                } else if (response.type === 'error') {
                    throw new Error(response.message);
                }
            }, params);
        } catch (e) {
            $.messager.alert('Error', e.message,'error');
        }
    },
    listFrame: () => {
        let url = "/index.php/webtool/data/frame/combobox";
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
                let url = "/index.php/webtool/annotation/multimodal/updateObject";
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