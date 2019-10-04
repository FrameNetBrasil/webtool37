let mouse = {
    x: 0,
    y: 0,
    startX: 0,
    startY: 0
};


doodle.onmousemove = function (e) {
    console.log('onmousemove');
    let ev = e || window.event;
    if (ev.pageX) {
        mouse.x = ev.pageX;
        mouse.y = ev.pageY;
    } else if (ev.clientX) {
        mouse.x = ev.clientX;
        mouse.y = ev.clientY;
    }
    mouse.x -= doodle.offsetLeft;
    mouse.y -= doodle.offsetTop;

    if (tmpAnnotatedObject !== null) {
        tmpAnnotatedObject.width = Math.abs(mouse.x - mouse.startX);
        tmpAnnotatedObject.height = Math.abs(mouse.y - mouse.startY);
        tmpAnnotatedObject.x = (mouse.x - mouse.startX < 0) ? mouse.x : mouse.startX;
        tmpAnnotatedObject.y = (mouse.y - mouse.startY < 0) ? mouse.y : mouse.startY;

        tmpAnnotatedObject.dom.style.width = tmpAnnotatedObject.width + 'px';
        tmpAnnotatedObject.dom.style.height = tmpAnnotatedObject.height + 'px';
        tmpAnnotatedObject.dom.style.left = tmpAnnotatedObject.x + 'px';
        tmpAnnotatedObject.dom.style.top = tmpAnnotatedObject.y + 'px';
    }
}

doodle.onclick = function (e) {
    if (doodle.style.cursor != 'crosshair') {
        return;
    }

    if (tmpAnnotatedObject != null) {
        let annotatedObject = new AnnotatedObject();
        annotatedObject.dom = tmpAnnotatedObject.dom;
        let bbox = new BoundingBox(tmpAnnotatedObject.x, tmpAnnotatedObject.y, tmpAnnotatedObject.width, tmpAnnotatedObject.height);
        annotatedObject.add(new AnnotatedFrame(player.currentFrame, bbox, true));
        annotatedObjectsTracker.annotatedObjects.push(annotatedObject);
        tmpAnnotatedObject = null;

        interactify(
            annotatedObject.dom,
            (x, y, width, height) => {
                console.log('annotated object changing');
                let bbox = new BoundingBox(x, y, width, height);
                annotatedObject.add(new AnnotatedFrame(player.currentFrame, bbox, true));
            }
        );

        addAnnotatedObjectControls(annotatedObject);

        doodle.style.cursor = 'default';
    } else {
        mouse.startX = mouse.x;
        mouse.startY = mouse.y;

        let dom = newBboxElement();
        dom.style.left = mouse.x + 'px';
        dom.style.top = mouse.y + 'px';
        tmpAnnotatedObject = {dom: dom};
    }
}
