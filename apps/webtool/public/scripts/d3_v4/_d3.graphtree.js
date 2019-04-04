//http://bl.ocks.org/GerHobbelt/3683278
var D3GraphTree;
(function(j){
D3GraphTree = Class.extend({
	defaults : {
	},
        element: '',
        width: 0,
        height: 0,
        graph: null,

    type: {
        attr: {symbol: "circle", size: 80},
        CE: {symbol: "circle", size: 80},
        ce: {symbol: "circle", size: 80},
        class: {symbol: "circle", size: 80},
        common: {symbol: "circle", size: 80},
        const: {symbol: "circle", size: 40},
        CX: {symbol: "circle", size: 200},
        cxn: {symbol: "square", size: 100},
        fe: {symbol: "circle", size: 80},
        FE: {symbol: "circle", size: 80},
        FR: {symbol: "square", size: 100},
        frame: {symbol: "square", size: 100},
        gf: {symbol: "square", size: 100},
        lemma: {symbol: "circle", size: 80},
        lexeme: {symbol: "circle", size: 80},
        lu: {symbol: "circle", size: 80},
        meaning: {symbol: "circle", size: 40},
        MEANING: {symbol: "circle", size: 40},
        ONTOLOGY: {symbol: "triangle-up", size: 80},
        pattern: {symbol: "square", size: 100},
        rel: {symbol: "triangle-up", size: 80},
        ROLE: {symbol: "circle", size: 40},
        SCHEMA: {symbol: "square", size: 200},
        st: {symbol:"triangle-up", size:100},
        top: {symbol: "triangle-up", size: 100},
        valence: {symbol: "circle", size: 80},
        word: {symbol: "circle", size: 80}
    },
    container : null,
	test : 'defaultvalue',
        node: null,
        entities: [],
        entitiesn: [],
        highlightNodes: [],
        names: [],
        cxn: [],
        schema: [],
        ont: [],
        struct: {},
        spec: {nodes:[], links:[]},
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
        'rel_cxn-const',
        'rel_evokes',
        'rel_constraint',
        'rel_constraint_before',
        'rel_inheritance',
        'rel_elementof',
        'rel_inhibitory'
    ],

        vis: null,
        nodes: [],
        links: [],
        force: null,
        drag: null,
        root: null,
        node: null,
        path: null,
        text: null,
	
	// Initializing
	init: function(o)
	{
            console.log('a');
            this.graph = this;
            this.element = o.element;
            this.index = 1000;
            this.spec = {nodes:[], links:[]};
            this.setOptions(o);
            var $element = $('#' + this.element);
            console.log($element);
            console.log($element.innerWidth());
            console.log($element.innerHeight());
            this.width = $element.innerWidth() - 10;
            this.height = $element.innerHeight() - 10;
            this.vis = d3.select($element[0]).append("svg")
                .attr("width", this.width)
                .attr("height", this.height);

            // Per-type markers, as they don't inherit styles.
            this.vis.append("defs").selectAll("marker")
                .data(this.relations)
                .enter().append("marker")
                .attr("id", function(d) { return d; })
                .attr("viewBox", "0 -5 10 10")
                .attr("refX", 18)
                .attr("refY", 0)
                .attr("markerWidth", 6)
                .attr("markerHeight", 6)
                .attr("orient", "auto")
                .append("path")
                .attr("class", function(d) { return d; })
                .attr("d", "M0,-3L10,0L0,3");            
            
            //this.update();
            this.clear();
	},

        clear: function() {
            this.clearLink();
            this.clearNode();
            //$('#' + this.element).html('');
        },

        loadNodesStruct: function(struct) {
            console.log('b');
            var that = this;
            $.each(struct.nodes, function (i, node){
                if (node.typeSystem != '') {
                    node.run = [];
                    node.children = [];
                    node.visited = false;
                    //console.log(node);
                    that.addNode(node);
                    if (node.type == 'ROOT') {
                        //console.log(node);
                        that.root = node;
                        that.root.fixed = true;
                        that.root.px = that.root.py = 0;
                      }
                } else {
                    console.log(node);
                }
            });
            $.each(struct.links, function (i, link){
                l = {
                    sourceId: link.source,
                    targetId: link.target,
                    type: (link.label == "") ? 'rel_common' : link.label
                };
                that.addLinkById(l); 
                target = that.findNode(link.target);
                source = that.findNode(link.source);
                //if ((target.typeSystem == 'frame') && (source.typeSystem == 'fe') ) {
                //    parent = source;
                //    child = target;
                //} else {
                    parent = target;
                    child = source;
                //}


                parent.children.push(child);
            });
            console.log('b1');
            this.force = d3.layout.force()
                .nodes(this.nodes)
                .links(this.links)
                .size([this.width, this.height])
            //this.force
                //.on("tick", this.onTick(this))
                .gravity(.01)
                .linkDistance(function(d) {
                    return d.target._children ? 100 : 50;
                })
                .charge(function(d) {
                    return -100;//return d._children ? -d.size / 100 : d.children ? -100 : -30;
                })
                .start()
                ;
            this.drag = this.force.drag()
                .on("dragstart", this.dragstart);
            console.log('b2');
            this.update();        
        },

        highlight: function(word) {
            var node = this.findNodeByType(word);
            if (node) {
                    this.highlightNode(node);
            }
        },

        loadNode: function(sourceWord) {
            var source = null;
            console.log(this.struct);
            $.each(this.struct.nodes, function (i, node){
                if (node.typeSystem != '') {
                    if (node.type == sourceWord) {
                        source = node;
                    }
                }
            });
            if (source) {
                var clone = {};
                this.cloneNode(source, clone);
                clone.run = [];
                runNumber = clone.id;
                clone.run.push(runNumber);
                clone.visited = false;
                this.spec.nodes.push(clone);
                console.log('loading ' + sourceWord) ;
                this.loadNodeSpec(clone, 1, runNumber);
                console.log('spec of ' + sourceWord) ;
                console.log(this.spec);
                this.loadNodesSpread(this.spec);
            }
        },
        getNodes: function() {
            return this.nodes;
        },
    
        getLinks: function() {
            return this.links;
        },
        
        flatten: function(root) {
            var nodes = [], i = 0, depth = 0, level_widths = [0], max_width, max_depth = 1, kx, ky;
            var oNodes = {};

            function recurse(node, parent, depth) {
                if (node.visited) {
                    return node.size;
                }
                node.visited = 1;
                console.log(node);

                node.parent = parent;
                node.depth = (node.depth ===  undefined) ? depth : node.depth;
                if (node.depth < depth) {
                    level_widths[node.depth] -= 1;
                    node.depth = depth;
                }
                var w = level_widths[node.depth] || 0;
                level_widths[node.depth] = w + 1;
                node.x = level_widths[node.depth];
                max_depth = Math.max(max_depth, depth + 1);
                oNodes[node.name + node.id] = node;
                if (node.children.length > 0) {
                    node.children.forEach(function (n) {
                        node.size += recurse(n, node, depth + 1);
                    });
                }
                return node.size;
            }

            console.log('before recurse');
            this.root.size = recurse(root, null, 0);
            console.log('after recurse');

            // now correct/balance the x positions:
            console.log(level_widths);
            max_width = 1;
            for (i = level_widths.length; --i > 0; ) {
                max_width = Math.max(max_width, level_widths[i]);
            }
            //console.log('max_width = '  + max_width);
            kx = (this.width - 20) / max_width;
            ky = (this.height - 20) / max_depth;
            //console.log('kx = '  + kx);
            //for (i = nodes.length; --i >= 0; ) {
            for (i in oNodes) {
                var node = oNodes[i];
                //if (!node.px) {
                    node.y = node.depth * ky;//node.y *= ky;
                    node.y += 10 + ky / 2;
                    node.x *= kx;
                    node.x += 10 + kx / 2;
                //}
                console.log(node.name + ' - ' + node.id + ' - ' + node.depth + ' - ' + node.x + ' - ' + node.y);
                //node.fixed = true;
                nodes.push(node);
            }

            return nodes;
        },   

        circle_radius: function (d) {
            return d.children ? 4.5 : Math.sqrt(d.size) / 10;
        },        

        update: function() {
            var that = this;
            var nodes = this.flatten(this.root);
            //console.log(nodes);
            this.vis.selectAll("g").remove();
        
            // make sure we set .px/.py as well as node.fixed will use those .px/.py to 'stick' the node to:
            if (!this.root.px) {
                // root have not be set / dragged / moved: set initial root position
                this.root.px = this.root.x = this.width / 2;
                this.root.py = this.root.y = this.circle_radius(this.root) + 2;
            }
            console.log('update');
            /*
            var n = 100;
            this.force.start();
            for (var i = n * n; i > 0; --i) this.force.tick();
            this.force.stop();
            */
  
            var path = this.vis.append("g").selectAll("path")
                .data(this.force.links())
                .enter().insert("line", ".node")
                .attr("x1", function(d) { 
                    return d.source.x; 
                })
                .attr("y1", function(d) { 
                    return d.source.y; 
                })
                .attr("x2", function(d) { 
                    return d.target.x; 
                })
                .attr("y2", function(d) { 
                    return d.target.y; 
                })
                .attr("class", function (d) {
                    return "link " + d.type;
                })
                .attr("marker-end", function (d) {
                    return (d.type == 'rel_elementof' ? "" : "url(#" + d.type + ")");
                })
                .on("mouseover", function (d) {
                    d3.select(this).attr("class", "link " + d.type + ' linkOver');
                })
                .on("mouseout", function (d) {
                    d3.select(this).attr("class", "link " + d.type);
                })
                .on("dblclick", this.clickLink);

            var node = this.vis.append("g").selectAll("circle.node")
                .data(nodes)//, function(d) {console.log(d.id); return d.id; })
                //.data(this.force.nodes())
                //.enter().append('path')
                //.attr("d", d3.svg.symbol()
                //    .size(function(d) { return d.size;})
                //    .type(function(d) {return d.symbol;})
                //)
                .enter().append("circle")
                //.attr("class", "node")
                .attr("cx", function(d) { return d.x; })
                .attr("cy", function(d) { return d.y; })
                .attr("r", function(d) { return that.circle_radius(d); })
                .attr("class", function (d) {
                    var typeSystem = d.typeSystem ? d.typeSystem : 'common';
                    var cssClass = ($.inArray(d.id, that.highlightNodes) != -1) ? " nodeSelected" : ' entity_' + typeSystem;
                    return cssClass;
                })
                .on("dblclick", this.dblclick)
                .call(this.drag);

            var text = this.vis.append("g").selectAll("text")
                .data(this.force.nodes())
                .enter().append("text")
                .attr("x", function(d) { return d.x + 8; })
                .attr("y", function(d) { return d.y; })
                .text(function (d) {
                    return d.name;
                });

            this.force.on("tick", function () {
                //console.log('tick');
                // Apply the constraints:
                //
                
                that.force.nodes().forEach(function(d) {
                    if (!d.fixed) {
                        var r = that.circle_radius(d) + 4, dx, dy, ly = 50;

                        // #1: constraint all nodes to the visible screen:
                        //d.x = Math.min(width - r, Math.max(r, d.x));
                        //d.y = Math.min(height - r, Math.max(r, d.y));

                        // #1.0: hierarchy: same level nodes have to remain with a 1 LY band vertically:
                        if (d.children || d._children) {
                            //var py = 0;
                            //if (d.parent) {
                            //    py = d.parent.y;
                            //}
                            var py = 0;
                            d.py = d.y = py + d.depth * ly + r;
                        }
                        
                        // #1a: constraint all nodes to the visible screen: links
                        dx = Math.min(0, that.width - r - d.x) + Math.max(0, r - d.x);
                        dy = Math.min(0, that.height - r - d.y) + Math.max(0, r - d.y);
                        d.x += 2 * Math.max(-ly, Math.min(ly, dx));
                        d.y += 2 * Math.max(-ly, Math.min(ly, dy));
                        // #1b: constraint all nodes to the visible screen: charges ('repulse')
                        dx = Math.min(0, that.width - r - d.px) + Math.max(0, r - d.px);
                        dy = Math.min(0, that.height - r - d.py) + Math.max(0, r - d.py);
                        d.px += 2 * Math.max(-ly, Math.min(ly, dx));
                        d.py += 2 * Math.max(-ly, Math.min(ly, dy));

                        // #2: hierarchy means childs must be BELOW parents in Y direction:
                        
                        //if (d.parent) {
                        //    d.y = Math.max(d.y, d.parent.y + ly);
                        //    d.py = Math.max(d.py, d.parent.py + ly);
                        //}
                        //
                    }
                })    
                

                path.attr("x1", function(d) { return d.source.x; })
                    .attr("y1", function(d) { return d.source.y; })
                    .attr("x2", function(d) { return d.target.x; })
                    .attr("y2", function(d) { return d.target.y; });

                node.attr("cx", function(d) { return d.x; })
                    .attr("cy", function(d) { return d.y; });

                text.attr("x", function(d) { return d.x + 8; })
                    .attr("y", function(d) { return d.y; });
            });  
                
            this.force.start();                
        },
        
        addNode: function (node) {
                //console.log(node);
                var typeSystem = node.typeSystem ? node.typeSystem : 'common';
                node.size = this.type[typeSystem].size; 
                node.symbol = this.type[typeSystem].symbol; 
                this.nodes.push(node);
                //this.update();
        },

        removeNode: function (id) {
                var i = 0;
                var n = this.findNode(id);
                while (i < this.links.length) {
                    if ((this.links[i]['source'] === n) || (links[i]['target'] == n)) {
                        this.links.splice(i, 1);
                    } else {
                        i++;
                    }
                }
                var index = this.findNodeIndex(id);
                if (index !== undefined) {
                    this.nodes.splice(index, 1);
                    this.update();
                }
        },

        addLink: function (link) {
                var sourceNode = this.findNode(link.source.id);
                if (sourceNode === undefined) {
                    this.addNode(link.source);
                } else {
                    link.source = sourceNode;
                }
                var targetNode = this.findNode(link.target.id);
                if (targetNode === undefined) {
                    this.addNode(link.target);
                } else {
                    link.target = targetNode;
                }
                var existsLink = this.findLink(link);
                if (!existsLink) {
                    this.links.push(link);
                }
                //console.log(links);
                //this.update();
        },
    
        addLinkById: function (l) {
                var link = {};
                var sourceNode = this.findNode(l.sourceId);
                link.source = sourceNode;
                var targetNode = this.findNode(l.targetId);
                link.target = targetNode;
                link.type = l.type;
                var existsLink = this.findLink(link);
                if (!existsLink) {
                    this.links.push(link);
                }
                //console.log(links);
                //this.update();
        },

        refreshLink: function (types) {
                var i = 0;
                while (i < this.links.length) {
                    if (types[links[i]['type']] === undefined) {
                        this.links.splice(i,1);
                    }
                    else i++;
                }
                this.update();
        },
    
        clearLink: function () {
                var i = 0;
                while (i < this.links.length) {
                    this.links.splice(i,1);
                }
                //this.update();
        },

        clearNode: function () {
                var i = 0;
                while (i < this.nodes.length) {
                    this.nodes[i].visited = false;
                    this.nodes.splice(i,1);
                }
                //this.update();
        },

        highlightNode: function (node) {
                this.highlightNodes.push(node.id);
                this.update();
        },
    
        resetHighLight: function () {
                this.highlightNodes = [];
                this.update();
        },

        spreadMatch: function(source, a, runNumber) {
                if (a < 0.7) {
                    return;
                }
                if ($.inArray(source.id, sp.highlightNodes) != -1) {
                    if ($.inArray(source.run, runNumber)) {
                        if (source.a > a) {
                            return;
                        }
                    } else {
                        a = source.a * a;
                    }   
                }
                source.a = a;
                //console.log(source.type + ' - ' + source.a);
                this.highlightNode(source);
                for (var i = 0; i < this.links.length; i++) {
                    var l = this.links[i];
                    if (l.source.id === source.id) {
                        if (l.type != 'rel_type-of') {
                            this.spreadMatch(l.target, a * 0.95, runNumber);
                        }
                    }
                }
        },    
        loadNodeSpec: function(clone, a, runNumber) {
                if (clone.visited) {
                    return;
                }
                clone.visited = true;
                var that = this;
                $.each(this.struct.links, function (i, link){
                    //var linkOk = ((link.label == 'rel_subclass') ||
                    //    (link.label == 'rel_frame-fe') ||
                    //   (link.label == 'rel_fe-frame'));
                    var linkOk = true;
                    if (linkOk) {
                        if (link.source == clone.idBase) {
                            var f = -1;
                            //console.log(link);
                            for(j in that.spec.nodes) {
                                //console.log(sp.spec.nodes[j]);
                                if (that.spec.nodes[j].idBase == link.target) {
                                    f = j;
                                }
                            }
                            if (f == -1) {
                                var cloneTarget = {};
                                that.cloneNode(that.struct.nodes[link.target], cloneTarget);
                                //console.log(cloneTarget);
                                that.spec.nodes.push(cloneTarget);
                                //console.log('adding ' + cloneTarget.type);
                            } else {
                                cloneTarget = that.spec.nodes[f];
                            }    
                            l = {
                                source: clone.id,
                                target: cloneTarget.id,
                                label: (link.label == "") ? 'rel_common' : link.label
                            };
                            that.spec.links.push(l);
                            that.loadNodeSpec(cloneTarget, 1, runNumber);
                        }    
                    }
                });
        },    
    
        cloneNode: function(node, clone) {
                for(i in node) {
                    clone[i] = node[i];
                }
                clone.idBase = node.id;
                clone.id = this.index++;
                clone.name = node.name + '_' + clone.id;
            },

            findNode: function (id) {
                for (var i = 0; i < this.nodes.length; i++) {
                    if (this.nodes[i].id === id) {
                        return this.nodes[i]
                    }
                }
            },

            findNodeByType: function (type) {
                for (var i = 0; i < this.nodes.length; i++) {
                    if (this.nodes[i].type === type) {
                        return this.nodes[i]
                    }
                }
            },

            findNodeIndex: function (id) {
                for (var i = 0; i < this.nodes.length; i++) {
                    if (this.nodes[i].id === id) {
                        return i
                    }
                }
        },

        findLink: function (link) {
                for (var i = 0; i < this.links.length; i++) {
                    var l = this.links[i];
                    if ((l.source.id === link.source.id) && (l.target.id === link.target.id) && (l.type === link.type)) {
                        return true;
                    }
                }
                return false;
        },

    
        dblclick: function() {
                this.onDblClick(d3.select(this).data()[0]);
        },
    
        clickLink: function() {
                this.clickLink(d3.select(this).data()[0]);
        },

        dragstart: function(d) {
                d3.select(this).classed("fixed", d.fixed = true);
        }

});

j.fn.D3Graph = function(o){
	// initializing
	var args = arguments;
	var o = o || {'container':''};
	return this.each(function(){
		// load the saved object
		var api = j.data(this, 'D3Graph');
		// create and save the object if it does not exist
		if (!api) {
			o.container = j(this);
			api = new D3Graph(o);
			j.data(this, 'D3Graph', api);
		}
                if (typeof api[o] == 'function') {
                        if (args[0] == o) delete args[0];
                        api[o].bind(api);
                        var parameters = Array.prototype.slice.call(args, 1);
                        return api[o].apply(api,parameters);
               }
		return api;
	});
};
})(jQuery);


