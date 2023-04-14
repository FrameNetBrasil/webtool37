<script type="text/javascript">
    // layers/udtree.js

jsPlumb.ready(function () {

    // setup some defaults for jsPlumb.
    UDTree.instance = jsPlumb.getInstance({
        Endpoint: ["Dot", {radius: 2}],
        Connector:"StateMachine",
        HoverPaintStyle: {stroke: "#1e8151", strokeWidth: 2 },
        ConnectionOverlays: [
            [ "Arrow", {
                location: 1,
                id: "arrow",
                length: 14,
                foldback: 0.8
            } ],
            [ "Label", { label: "-", id: "label", cssClass: "aLabel" }]
        ],
        Container: 'UDTreeCanvas'
    });

    UDTree.instance.registerConnectionType("basic", { anchor:"Continuous", connector:"StateMachine" });

    window.jsp = UDTree.instance;

//    var windows = jsPlumb.getSelector(".statemachine-demo .w");
//	console.log(windows);

    // bind a click listener to each connection; the connection is deleted. you could of course
    // just do this: instance.bind("click", instance.deleteConnection), but I wanted to make it clear what was
    // happening.
    UDTree.instance.bind("click", function (c) {
        UDTree.instance.deleteConnection(c);
    });

    // bind a connection listener. note that the parameter passed to this function contains more than
    // just the new connection - see the documentation for a full list of what is included in 'info'.
    // this listener sets the connection's internal
    // id as the label overlay's text.
    UDTree.instance.bind("connection", function (info) {
        //info.connection.getOverlay("label").setLabel(info.connection.id);
		info.connection.getOverlay("label").setLabel(info.connection.source.ud);
    });

    // bind a double click listener to "canvas"; add new node when this occurs.
    //jsPlumb.on(canvas, "dblclick", function(e) {
    //    newNode(e.offsetX, e.offsetY);
    //});

    //
    // initialise element as connection targets and source.
    //
    UDTree.initNode = function(el) {

        // initialise draggable elements.
        UDTree.instance.draggable(el);

        UDTree.instance.makeSource(el, {
            filter: ".ep",
            anchor: "Top",//"Continuous",
            connectorStyle: { stroke: "#5c96bc", strokeWidth: 2, outlineStroke: "transparent", outlineWidth: 4 },
            connectionType:"basic",
            extract:{
                "action":"the-action"
            },
            maxConnections: 2,
            onMaxConnections: function (info, e) {
                alert("Maximum connections (" + info.maxConnections + ") reached");
            }
        });

        UDTree.instance.makeTarget(el, {
            dropOptions: { hoverClass: "dragHover" },
            anchor: "Bottom", //"Continuous",
            allowLoopback: false
        });

        // this is not part of the core demo functionality; it is a means for the Toolkit edition's wrapped
        // version of this demo to find out about new nodes being added.
        //
//        instance.fire("jsPlumbDemoNodeAdded", el);
    };

    UDTree.newNode = function(node) {
        console.log(node);
        var d = document.createElement("div");
        var id = jsPlumbUtil.uuid();
        d.className = "w";
        d.id = 'node' + node.id;
		d.ud = node.ud;
		d.idNode = node.id;
        //d.innerHTML = id.substring(0, 7) + "<div class=\"ep\"></div>";
		d.innerHTML = node.name + '&nbsp;&nbsp;&nbsp;' + (node.name != '.' ? "<br>[" + node.ud + ']&nbsp;&nbsp;&nbsp;' : '') +  "<div class=\"ep\"></div>";
        d.style.left = node.x + "px";
        d.style.top = 70 + (node.level * 120) + "px";
        UDTree.instance.getContainer().appendChild(d);
        UDTree.initNode(d);
        return d;
    };


    // suspend drawing and initialise.
    UDTree.draw = function () {
        var canvas = $('#' + UDTree.element);
        console.log(canvas);
        console.log(canvas.position());
        var x = canvas.position().left;
        var y = canvas.position().top;

        var nodes = UDTree.nodes;
        console.log('x = ' + x + '  y = ' + y);

        for (i in nodes) {
			nodes[i].level = 0;
            nodes[i].x = 10 * (nodes[i].start);
		}
		nodes['root'] = {
			id: 'root',
			name: '.',
			ud: '.',
			x: 480,
            level: 0
		};
		var maxLevel = 1;
		do
		{
			var changed = false;
			for (i in nodes) {
					var node = nodes[i];
					if (node.parent)
					{
						var linked = nodes[node.parent];
						var level = linked.level + 1;
						if (node.level != level)
						{
							node.level = level;
							changed = true;
							if (level > maxLevel)
							{
								maxLevel = level + 1;
							}
						}
					}
			}

		}
		while (changed);
		var fakeX = 1;
		for (i in nodes) {
			if ((i != 'root') && (nodes[i].parent == null))
			{
				nodes[i].level = maxLevel;
			}
		}


        for (i in nodes) {
//            initNode(windows[i], true);
			UDTree.newNode(nodes[i]);
        }
        for (i in nodes) {
			var node = nodes[i];
			if(node.parent) {
               UDTree.instance.connect({ source: 'node'+node.id, target: 'node'+node.parent, type:"basic" });
			}
        }
    }

	UDTree.saveTree = function() {
		console.log('saving');
        UDTree.refreshTree();
    }

	UDTree.refreshTree = function() {
		console.log('refreshing');
        var nodes = UDTree.nodes;
        for (i in nodes) {
			var node = nodes[i];
			var id = 'node' + node.id;
			var ar = UDTree.instance.getConnections({source:id, flat: true});
			console.log(ar);
			for (var c = 0; c < ar.length; c++) {
				node.parent = ar[c].targetId.substr(4,5);
			};
       }
        UDTree.nodes = nodes;
        UDTree.instance.empty(UDTree.element);
	   	UDTree.instance.batch(UDTree.draw);
	}

	UDTree.start = function(nodes) {
        UDTree.nodes = nodes;
        UDTree.instance.batch(UDTree.draw);
        jsPlumb.fire("jsPlumbDemoLoaded", UDTree.instance);
    }

    UDTree.destroy = function() {
        console.log('destroying');
        UDTree.instance.empty(UDTree.element);
    }

});

</script>