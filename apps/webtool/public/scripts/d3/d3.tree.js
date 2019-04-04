var D3Tree;
(function (j) {
    D3Tree = Class.extend({
        defaults: {},
        element: '',
        width: 0,
        height: 0,
        graph: null,

        type: {
            CONSTRUCTION: {symbol: "circle", size: 200},
            SCHEMA: {symbol: "square", size: 200},
            common: {symbol: "circle", size: 80},
            ROLE: {symbol: "circle", size: 40},
            top: {symbol: "triangle-up", size: 100},
            frame: {symbol: "square", size: 100},
            fe: {symbol: "circle", size: 40},
            lu: {symbol: "circle", size: 80},
            ce: {symbol: "circle", size: 80},
            cxn: {symbol: "square", size: 100},
            const: {symbol: "circle", size: 40},
            meaning: {symbol: "circle", size: 40},
            ONTOLOGY: {symbol: "triangle-up", size: 80}
        },


        container: null,
        test: 'defaultvalue',
        node: null,
        entities: [],
        entitiesn: [],
        highlightNodes: [],
        names: [],
        cxn: [],
        schema: [],
        ont: [],
        struct: {},
        spec: {nodes: [], links: []},
        index: 1000,
        currentEntity: null,
        relationData: [],
        relations: [
            'rel_common',
            'rel_subclass',
            'rel_fe-frame',
            'rel_frame-fe',
            'rel_fe-lu',
            'rel_lu-fe',
            'rel_fe-telic',
            'rel_fe-agentive',
            'rel_fe-constitutive',
            'rel_metaphor',
            'rel_type-of',
            'rel_frame-cxn',
            'rel_meaning-frame',
            'rel_const-cxn',
            'rel_cxn-meaning',
            'rel_cxn-const'
        ],

        baseSVG: null,
        nodes: [],
        links: [],
        force: null,
        drag: null,

        // Calculate total nodes, max label length
        totalNodes: 0,
        maxLabelLength: 0,
        // variables for drag/drop
        selectedNode: null,
        draggingNode: null,
        // panning variables
        panSpeed: 200,
        panBoundary: 20, // Within 20px from edges will pan when dragging.
        // Misc. variables
        i: 0,
        duration: 750,
        root: null,
        viewerWidth: 0,
        viewerHeight: 0,
        tree: null,
        diagonal: null,
        treeData: {},
        svgGroup: null,
        zoomListener: null,

        // Initializing
        init: function (o) {
            this.element = o.element;
            this.index = 1000;
            this.spec = {nodes: [], links: []};
            this.setOptions(o);
            var $element = $('#' + this.element);
            //this.width = $element.innerWidth() - 10;
            //this.height = $element.innerHeight() - 10;

            // size of the diagram
            this.viewerWidth = $element.width();
            this.viewerHeight = $element.height();

            this.tree = d3.layout.tree()
                .size([this.viewerHeight, this.viewerWidth]);

            // define a d3 diagonal projection for use by the node paths later on.
            this.diagonal = d3.svg.diagonal()
                .projection(function (d) {
                    return [d.y, d.x];
                });

            // define the zoomListener which calls the zoom function on the "zoom" event constrained within the scaleExtents
            this.zoomListener = d3.behavior.zoom().scaleExtent([0.1, 3]).on("zoom", this.zoom);

            // define the baseSvg, attaching a class for styling and the zoomListener
            this.baseSvg = d3.select($element[0]).append("svg")
                .attr("width", this.viewerWidth)
                .attr("height", this.viewerHeight)
                .attr("class", "overlay")
                .call(this.zoomListener);

            // Append a group which holds all nodes and which the zoom Listener can act upon.
            this.svgGroup = this.baseSvg.append("g");


            this.clear();
        },

        clear: function () {
        },

        loadTreeData: function (struct) {
            var that = this;
            this.treeData = struct;
            // Call visit function to establish maxLabelLength
            this.visit(this.treeData, function (d) {
                this.totalNodes++;
                that.maxLabelLength = Math.max(d.name.length, that.maxLabelLength);
            }, function (d) {
                return d.children && d.children.length > 0 ? d.children : null;
            });

            // Sort the tree initially incase the JSON isn't in a sorted order.
            //this.sortTree();

            // Define the root
            this.root = this.treeData;
            this.root.x0 = this.viewerHeight / 2;
            this.root.y0 = 0;

            // Layout the tree initially and center on the root node.
            this.update(this.root);
            this.centerNode(this.root);

            var couplingParent1 = this.tree.nodes(this.root).filter(function (d) {
                return d['name'] === 'cluster';
            })[0];
            var couplingChild1 = this.tree.nodes(this.root).filter(function (d) {
                return d['name'] === 'JSONConverter';
            })[0];

            multiParents = [{
                parent: couplingParent1,
                child: couplingChild1
            }];
            /*
             multiParents.forEach(function(multiPair) {
             console.log(multiPair);
             that.svgGroup.append("path", "g")
             .attr("class", "additionalParentLink")
             .attr("d", function() {
             var oTarget = {
             x: multiPair.parent.x0,
             y: multiPair.parent.y0
             };
             var oSource = {
             x: multiPair.child.x0,
             y: multiPair.child.y0
             };
             return diagonal({
             source: oSource,
             target: oTarget
             });
             });
             });
             */
        },

        // A recursive helper function for performing some setup by walking through all nodes
        visit: function (parent, visitFn, childrenFn) {
            if (!parent) return;
            visitFn(parent);
            var children = childrenFn(parent);
            if (children) {
                var count = children.length;
                for (var i = 0; i < count; i++) {
                    this.visit(children[i], visitFn, childrenFn);
                }
            }
        },


        /*
         // sort the tree according to the node names

         function sortTree() {
         tree.sort(function(a, b) {
         return b.name.toLowerCase() < a.name.toLowerCase() ? 1 : -1;
         });
         }

         // TODO: Pan function, can be better implemented.

         function pan(domNode, direction) {
         var speed = panSpeed;
         if (panTimer) {
         clearTimeout(panTimer);
         translateCoords = d3.transform(svgGroup.attr("transform"));
         if (direction == 'left' || direction == 'right') {
         translateX = direction == 'left' ? translateCoords.translate[0] + speed : translateCoords.translate[0] - speed;
         translateY = translateCoords.translate[1];
         } else if (direction == 'up' || direction == 'down') {
         translateX = translateCoords.translate[0];
         translateY = direction == 'up' ? translateCoords.translate[1] + speed : translateCoords.translate[1] - speed;
         }
         scaleX = translateCoords.scale[0];
         scaleY = translateCoords.scale[1];
         scale = zoomListener.scale();
         svgGroup.transition().attr("transform", "translate(" + translateX + "," + translateY + ")scale(" + scale + ")");
         d3.select(domNode).select('g.node').attr("transform", "translate(" + translateX + "," + translateY + ")");
         zoomListener.scale(zoomListener.scale());
         zoomListener.translate([translateX, translateY]);
         panTimer = setTimeout(function() {
         pan(domNode, speed, direction);
         }, 50);
         }
         }
         */
        // Define the zoom function for the zoomable tree

        zoom: function () {
            grapher.svgGroup.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
        },


        /*
         function initiateDrag(d, domNode) {
         draggingNode = d;
         d3.select(domNode).select('.ghostCircle').attr('pointer-events', 'none');
         d3.selectAll('.ghostCircle').attr('class', 'ghostCircle show');
         d3.select(domNode).attr('class', 'node activeDrag');

         svgGroup.selectAll("g.node").sort(function(a, b) { // select the parent and sort the path's
         if (a.id != draggingNode.id) return 1; // a is not the hovered element, send "a" to the back
         else return -1; // a is the hovered element, bring "a" to the front
         });
         // if nodes has children, remove the links and nodes
         if (nodes.length > 1) {
         // remove link paths
         links = tree.links(nodes);
         nodePaths = svgGroup.selectAll("path.link")
         .data(links, function(d) {
         return d.target.id;
         }).remove();
         // remove child nodes
         nodesExit = svgGroup.selectAll("g.node")
         .data(nodes, function(d) {
         return d.id;
         }).filter(function(d, i) {
         if (d.id == draggingNode.id) {
         return false;
         }
         return true;
         }).remove();
         }

         // remove parent link
         parentLink = tree.links(tree.nodes(draggingNode.parent));
         svgGroup.selectAll('path.link').filter(function(d, i) {
         if (d.target.id == draggingNode.id) {
         return true;
         }
         return false;
         }).remove();

         dragStarted = null;
         }
         */
        /*
         // Define the drag listeners for drag/drop behaviour of nodes.
         dragListener = d3.behavior.drag()
         .on("dragstart", function(d) {
         if (d == root) {
         return;
         }
         dragStarted = true;
         nodes = tree.nodes(d);
         d3.event.sourceEvent.stopPropagation();
         // it's important that we suppress the mouseover event on the node being dragged. Otherwise it will absorb the mouseover event and the underlying node will not detect it d3.select(this).attr('pointer-events', 'none');
         })
         .on("drag", function(d) {
         if (d == root) {
         return;
         }
         if (dragStarted) {
         domNode = this;
         initiateDrag(d, domNode);
         }

         // get coords of mouseEvent relative to svg container to allow for panning
         relCoords = d3.mouse($('svg').get(0));
         if (relCoords[0] < panBoundary) {
         panTimer = true;
         pan(this, 'left');
         } else if (relCoords[0] > ($('svg').width() - panBoundary)) {

         panTimer = true;
         pan(this, 'right');
         } else if (relCoords[1] < panBoundary) {
         panTimer = true;
         pan(this, 'up');
         } else if (relCoords[1] > ($('svg').height() - panBoundary)) {
         panTimer = true;
         pan(this, 'down');
         } else {
         try {
         clearTimeout(panTimer);
         } catch (e) {

         }
         }

         d.x0 += d3.event.dy;
         d.y0 += d3.event.dx;
         var node = d3.select(this);
         node.attr("transform", "translate(" + d.y0 + "," + d.x0 + ")");
         updateTempConnector();
         }).on("dragend", function(d) {
         if (d == root) {
         return;
         }
         domNode = this;
         if (selectedNode) {
         // now remove the element from the parent, and insert it into the new elements children
         var index = draggingNode.parent.children.indexOf(draggingNode);
         if (index > -1) {
         draggingNode.parent.children.splice(index, 1);
         }
         if (typeof selectedNode.children !== 'undefined' || typeof selectedNode._children !== 'undefined') {
         if (typeof selectedNode.children !== 'undefined') {
         selectedNode.children.push(draggingNode);
         } else {
         selectedNode._children.push(draggingNode);
         }
         } else {
         selectedNode.children = [];
         selectedNode.children.push(draggingNode);
         }
         // Make sure that the node being added to is expanded so user can see added node is correctly moved
         expand(selectedNode);
         sortTree();
         endDrag();
         } else {
         endDrag();
         }
         });

         function endDrag() {
         selectedNode = null;
         d3.selectAll('.ghostCircle').attr('class', 'ghostCircle');
         d3.select(domNode).attr('class', 'node');
         // now restore the mouseover event or we won't be able to drag a 2nd time
         d3.select(domNode).select('.ghostCircle').attr('pointer-events', '');
         updateTempConnector();
         if (draggingNode !== null) {
         update(root);
         centerNode(draggingNode);
         draggingNode = null;
         }
         }
         */
        // Helper functions for collapsing and expanding nodes.
        collapse: function (d) {
            if (d.children) {
                d._children = d.children;
                d._children.forEach(collapse);
                d.children = null;
            }
        },
        expand: function (d) {
            if (d._children) {
                d.children = d._children;
                d.children.forEach(expand);
                d._children = null;
            }
        },

        overCircle: function (d) {
            this.selectedNode = d;
            this.updateTempConnector();
        },

        outCircle: function (d) {
            this.selectedNode = null;
            this.updateTempConnector();
        },

        // Function to update the temporary connector indicating dragging affiliation
        updateTempConnector: function () {
            var data = [];
            if (this.draggingNode !== null && this.selectedNode !== null) {
                // have to flip the source coordinates since we did this for the existing connectors on the original tree
                data = [{
                    source: {
                        x: this.selectedNode.y0,
                        y: this.selectedNode.x0
                    },
                    target: {
                        x: this.draggingNode.y0,
                        y: this.draggingNode.x0
                    }
                }];
            }
            var link = this.svgGroup.selectAll(".templink").data(data);

            link.enter().append("path")
                .attr("class", "templink")
                .attr("d", d3.svg.diagonal())
                .attr('pointer-events', 'none');

            link.attr("d", d3.svg.diagonal());

            link.exit().remove();
        },

        // Function to center node when clicked/dropped so node doesn't get lost when collapsing/moving with large amount of children.
        centerNode: function (source) {
            scale = this.zoomListener.scale();
            x = -source.y0;
            y = -source.x0;
            x = x * scale + this.viewerWidth / 2;
            y = y * scale + this.viewerHeight / 2;
            d3.select('g').transition()
                .duration(this.duration)
                .attr("transform", "translate(" + x + "," + y + ")scale(" + scale + ")");
            this.zoomListener.scale(scale);
            this.zoomListener.translate([x, y]);
        },

        // Toggle children function
        toggleChildren: function (d) {
            if (d.children) {
                d._children = d.children;
                d.children = null;
            } else if (d._children) {
                d.children = d._children;
                d._children = null;
            }
            return d;
        },

        // Toggle children on click.
        click: function (d) {
            if (d3.event.defaultPrevented) return; // click suppressed
            d = grapher.toggleChildren(d);
            grapher.update(d);
            grapher.centerNode(d);
        },

        update: function (source) {
            var that = this;
            // Compute the new height, function counts total children of root node and sets tree height accordingly.
            // This prevents the layout looking squashed when new nodes are made visible or looking sparse when nodes are removed
            // This makes the layout more consistent.
            var levelWidth = [1];
            var childCount = function (level, n) {
                if (n.children && n.children.length > 0) {
                    if (levelWidth.length <= level + 1) levelWidth.push(0);

                    levelWidth[level + 1] += n.children.length;
                    n.children.forEach(function (d) {
                        childCount(level + 1, d);
                    });
                }
            };
            childCount(0, this.root);
            var newHeight = d3.max(levelWidth) * 25; // 25 pixels per line  
            this.tree = this.tree.size([newHeight, this.viewerWidth]);

            // Compute the new tree layout.
            var nodes = this.tree.nodes(this.root).reverse(),
                links = this.tree.links(nodes);

            // Set widths between levels based on maxLabelLength.
            nodes.forEach(function (d) {
                //-   d.y = (d.depth * (that.maxLabelLength * 10)); //maxLabelLength * 10px
                // alternatively to keep a fixed scale one can set a fixed depth per level
                // Normalize for fixed-depth by commenting out below line
                d.y = (d.depth * 150); //500px per level.
            });

            // Update the nodes…
            node = this.svgGroup.selectAll("g.node")
                .data(nodes, function (d) {
                    return d.id || (d.id = ++that.i);
                });

            // Enter any new nodes at the parent's previous position.
            var nodeEnter = node.enter().append("g")
                //.call(dragListener)
                .attr("class", "node")
                .attr("transform", function (d) {
                    return "translate(" + source.y0 + "," + source.x0 + ")";
                })
                .on('click', this.click);

            nodeEnter.append("circle")
                .attr('class', 'nodeCircle')
                .attr("r", 0)
                .style("fill", function (d) {
                    return d._children ? "lightsteelblue" : "#fff";
                });

            nodeEnter.append("text")
                .attr("x", function (d) {
                    return d.children || d._children ? -10 : 10;
                })
                .attr("dy", ".35em")
                .attr('class', 'nodeText')
                .attr("text-anchor", function (d) {
                    return d.children || d._children ? "end" : "start";
                })
                .text(function (d) {
                    return d.name;
                })
                .style("fill-opacity", 0);

            // phantom node to give us mouseover in a radius around it
            nodeEnter.append("circle")
                .attr('class', 'ghostCircle')
                .attr("r", 30)
                .attr("opacity", 0.2) // change this to zero to hide the target area
                .style("fill", "red")
                .attr('pointer-events', 'mouseover')
                .on("mouseover", function (node) {
                    that.overCircle(node);
                })
                .on("mouseout", function (node) {
                    that.outCircle(node);
                });

            // Update the text to reflect whether node has children or not.
            node.select('text')
                .attr("x", function (d) {
                    return d.children || d._children ? -10 : 10;
                })
                .attr("text-anchor", function (d) {
                    return d.children || d._children ? "end" : "start";
                })
                .text(function (d) {
                    return d.name;
                });

            // Change the circle fill depending on whether it has children and is collapsed
            node.select("circle.nodeCircle")
                .attr("r", 4.5)
                .style("fill", function (d) {
                    return d._children ? "lightsteelblue" : "#fff";
                });

            // Transition nodes to their new position.
            var nodeUpdate = node.transition()
                .duration(this.duration)
                .attr("transform", function (d) {
                    return "translate(" + d.y + "," + d.x + ")";
                });

            // Fade the text in
            nodeUpdate.select("text")
                .style("fill-opacity", 1);

            // Transition exiting nodes to the parent's new position.
            var nodeExit = node.exit().transition()
                .duration(this.duration)
                .attr("transform", function (d) {
                    return "translate(" + source.y + "," + source.x + ")";
                })
                .remove();

            nodeExit.select("circle")
                .attr("r", 0);

            nodeExit.select("text")
                .style("fill-opacity", 0);

            // Update the links…
            var link = this.svgGroup.selectAll("path.link")
                .data(links, function (d) {
                    return d.target.id;
                });

            // Enter any new links at the parent's previous position.
            link.enter().insert("path", "g")
                .attr("class", "link")
                .attr("d", function (d) {
                    var o = {
                        x: source.x0,
                        y: source.y0
                    };
                    return that.diagonal({
                        source: o,
                        target: o
                    });
                });

            // Transition links to their new position.
            link.transition()
                .duration(this.duration)
                .attr("d", this.diagonal);

            // Transition exiting nodes to the parent's new position.
            link.exit().transition()
                .duration(this.duration)
                .attr("d", function (d) {
                    var o = {
                        x: source.x,
                        y: source.y
                    };
                    return that.diagonal({
                        source: o,
                        target: o
                    });
                })
                .remove();

            // Stash the old positions for transition.
            nodes.forEach(function (d) {
                d.x0 = d.x;
                d.y0 = d.y;
            });
        }
    });

    j.fn.D3Tree = function (o) {
        // initializing
        var args = arguments;
        var o = o || {'container': ''};
        return this.each(function () {
            // load the saved object
            var api = j.data(this, 'D3Tree');
            // create and save the object if it does not exist
            if (!api) {
                o.container = j(this);
                api = new D3Tree(o);
                j.data(this, 'D3Tree', api);
            }
            if (typeof api[o] == 'function') {
                if (args[0] == o) delete args[0];
                api[o].bind(api);
                var parameters = Array.prototype.slice.call(args, 1);
                return api[o].apply(api, parameters);
            }
            return api;
        });
    };
})(jQuery);