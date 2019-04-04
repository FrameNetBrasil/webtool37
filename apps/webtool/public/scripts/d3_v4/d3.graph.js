var D3Graph;
(function (j) {
    D3Graph = Class.extend({
        defaults: {},
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
            constraint: {symbol: "triangle-up", size: 80},
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
            meaning: {symbol: "circle", size: 80},
            MEANING: {symbol: "circle", size: 40},
            ONTOLOGY: {symbol: "triangle-up", size: 80},
            pattern: {symbol: "square", size: 100},
            rel: {symbol: "triangle-up", size: 80},
            ROLE: {symbol: "circle", size: 40},
            root: {symbol: "triangle-up", size: 100},
            same: {symbol: "circle", size: 80},
            SCHEMA: {symbol: "square", size: 200},
            st: {symbol:"triangle-up", size:100},
            top: {symbol: "triangle-up", size: 100},
            valence: {symbol: "circle", size: 80},
            word: {symbol: "circle", size: 80}
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
            'rel_agentive_qualia',
            'rel_argval',
            'rel_argument',
            'rel_causative_of',
            'rel_common',
            'rel_const-cxn',
            'rel_constitutive_qualia',
            'rel_constraint',
            'rel_constraint_before',
            'rel_constraint_frame',
            'rel_coreset',
            'rel_cxn-const',
            'rel_cxn-meaning',
            'rel_elementof',
            'rel_evokes',
            'rel_excludes',
            'rel_fe-frame',
            'rel_fe-lu',
            'rel_formal_qualia',
            'rel_frame-cxn',
            'rel_frame-fe',
            'rel_hassemtype',
            'rel_identity',
            'rel_inchoative_of',
            'rel_inheritance',
            'rel_inheritance_cxn',
            'rel_inhibitory',
            'rel_lu-fe',
            'rel_meaning-frame',
            'rel_metaphor',
            'rel_perspective_on',
            'rel_precedes',
            'rel_requires',
            'rel_see_also',
            'rel_subclass',
            'rel_subclassof',
            'rel_subframe',
            'rel_telic_qualia',
            'rel_type-of',
            'rel_using',
            'rel_value'
        ],

        vis: null,
        nodes: [],
        links: [],
        force: null,
        drag: null,

        // Initializing
        init: function (o) {
            this.element = o.element;
            this.index = 1000;
            this.spec = {nodes: [], links: []};
            this.setOptions(o);
            var $element = $('#' + this.element);
            console.log($element);
            console.log($element.innerWidth());
            console.log($element.outerHeight());

            this.width = $element.innerWidth() - 10;
            this.height = $element.outerHeight() - 10;
            this.vis = d3.select($element[0]).append("svg")
                .attr("width", this.width)
                .attr("height", this.height);

            // Per-type markers, as they don't inherit styles.
            this.vis.append("defs").selectAll("marker")
                .data(this.relations)
                .enter().append("marker")
                .attr("id", function (d) {
                    return d;
                })
                .attr("viewBox", "0 -5 10 10")
                .attr("refX", 18)
                .attr("refY", 0)
                .attr("markerWidth", 6)
                .attr("markerHeight", 6)
                .attr("orient", "auto")
                .append("path")
                .attr("class", function (d) {
                    return d;
                })
                .attr("d", "M0,-3L10,0L0,3");

            this.graph = this;
            //this.update();
            this.clear();
        },

        start: function () {
            this.force = d3.layout.force()
                .nodes(this.nodes)
                .links(this.links)
                .gravity(.1)
                .size([this.width, this.height])
                .linkDistance(100)
                .charge(-100);

            this.drag = this.force.drag()
                .on("dragstart", this.dragstart);
            this.update();
        },

        clear: function () {
            this.clearLink();
            this.clearNode();
            //$('#' + this.element).html('');
        },

        clearGraph: function () {
            this.clear();
            this.start();
        },

        stop: function() {
            if (this.force) {
                this.force.stop();
            }
        },

        loadNodes: function (struct) {
            var that = this;
            console.log(struct);
            if (struct.nodes) {
                $.each(struct.nodes, function (i, node) {
                    if (node.typeSystem != '') {
                        node.run = [];
                        that.addNode(node);
                    } else {
                        console.log(node);
                    }
                });
            }
            if (struct.links) {
                $.each(struct.links, function (i, link) {
                    l = {
                        sourceId: link.source,
                        targetId: link.target,
                        type: (link.label == "") ? 'rel_common' : link.label,
                    };
                    that.addLinkById(l);
                });
            }
        },

        fixCurrentNodes: function() {
            for (var i = 0; i < this.nodes.length; i++) {
                this.nodes[i].fixed = true;
            }
        },

        loadNodesStruct: function (struct) {
            var that = this;
            $.each(struct.nodes, function (i, node) {
                //if ((node.typeSystem != '') && (node.typeSystem != 'ONTOLOGY')) {
                if (node.typeSystem != '') {
                    node.run = [];
                    //console.log(node);
                    var existNode = that.findNode(node.id);
                    if (!existNode) {
                        that.addNode(node);
                        if (node.type == 'h') {
                            console.log(node);
                            that.highlightNode(node);
                        }
                    } else {
                        existNode.status = node.status;
                        existNode.name = node.name;
                    }
                } else {
                    console.log(node);
                }
            });
            that.clearLink();
            $.each(struct.links, function (i, link) {
                s = that.findNode(link.source);
                t = that.findNode(link.target);
                if (s && t) {
                    l = {
                        sourceId: link.source,
                        targetId: link.target,
                        type: (link.label == "") ? 'rel_common' : link.label,
                        optional: link.optional,
                        status: link.status
                    };
                    var existLink = that.findLink(l);
                    if (!existLink) {
                        that.addLinkById(l);
                    } else {
                        l.status = link.status;
                    }
                }
            });
        },

        highlight: function (word) {
            var node = this.findNodeByType(word);
            if (node) {
                this.highlightNode(node);
            }
        },

        getNodes: function () {
            return this.nodes;
        },

        getLinks: function () {
            return this.links;
        },

        collide: function (alpha) {
            var quadtree = d3.geom.quadtree(this.nodes);
            return function(d) {
                nx1 = d.x - 10,
                nx2 = d.x + 10,
                ny1 = d.y - 10,
                ny2 = d.y + 10;
                quadtree.visit(function(quad, x1, y1, x2, y2) {
                    if (quad.point && (quad.point !== d)) {
                        var x = d.x - quad.point.x,
                            y = d.y - quad.point.y,
                            l = Math.sqrt(x * x + y * y);
                        if (l < 10) {
                            l = (l - 10) / l * alpha;
                            d.x -= x *= l;
                            d.y -= y *= l;
                            quad.point.x += x;
                            quad.point.y += y;
                        }
                    }
                    return x1 > nx2 || x2 < nx1 || y1 > ny2 || y2 < ny1;
                });
            };
        },

        update: function () {
            var that = this;

            //this.force.start();
            //for (var i = 150000; i > 0; --i) this.force.tick();
            //this.force.stop();

            //console.log(this.force.nodes());

            this.vis.selectAll("g").remove();

            var path = this.vis.append("g").selectAll("path")
                .data(this.force.links())
                .enter().append("line")
                .attr("class", function (d) {
                    return ((d.status == 'inactive') ? ' linkInactive' : "link " + d.type)  + (d.optional ? ' optional' : '') ;
                })
                .attr("marker-end", function (d) {
                    return (d.type == 'rel_elementof' ? "" : "url(#" + d.type + ")");
                })
                .on("mouseover", function (d) {
                    d3.select(this).attr("class", ((d.status == 'inactive') ? ' linkInactive' : "link " + d.type)  + (d.optional ? ' optional' : ''));
                })
                .on("mouseout", function (d) {
                    d3.select(this).attr("class", ((d.status == 'inactive') ? ' linkInactive' : "link " + d.type)  + (d.optional ? ' optional' : ''));
                })
                .on("dblclick", this.onClickLink);

            var node = this.vis.append("g").selectAll("path")
                .data(this.force.nodes())
                .enter().append('path')
                .attr("d", d3.svg.symbol()
                    .size(function (d) {
                        return d.size;
                    })
                    .type(function (d) {
                        return d.symbol;
                    })
                )
                .attr("class", function (d) {
                    var typeSystem = d.typeSystem ? d.typeSystem : 'common';
                    if ((d.status == 'fired') || (d.status =='terminal') || (d.status =='none')) {
                        var cssClass = (($.inArray(d.id, that.highlightNodes) != -1) ? " nodeSelected" : ' entity_' + typeSystem) ;
                    } else {
                        var cssClass = d.status;
                    }
                    return cssClass;
                })
                .on("click", this.onClick)
                .on("dblclick", this.onDblClick)
                .call(this.drag);

            var text = this.vis.append("g").selectAll("text")
                .data(this.force.nodes())
                .enter().append("text")
                .attr("x", 8)
                .attr("y", ".31em")
                .text(function (d) {
                    return d.name;
                })
                .attr("class", function (d) {
                    var typeSystem = d.typeSystem ? d.typeSystem : 'common';
                    if ((d.status == 'fired') || (d.status =='terminal')) {
                        var cssClass = ' entity_' + typeSystem;
                    } else {
                        var cssClass = d.status;
                    }
                    return cssClass;
                });
/*
            node
                .attr("transform", function(d) {
                    if (d.y < 0) {
                        d.y = (Math.random() * 30) + 10;
                    }
                    if (d.y > that.height) {
                        d.y = that.height - (d.y - that.height) - 15 - (Math.random() * 20);
                    }
                    return "translate(" + d.x + "," + d.y + ")";
                });
            text
                .attr("transform", function(d) {
                    if (d.y < 0) {
                        d.y = (Math.random() * 30) + 10;
                    }
                    if (d.y > that.height) {
                        d.y = that.height - (d.y - that.height) - 15 - (Math.random() * 20);
                    }
                    return "translate(" + d.x + "," + d.y + ")";
                });

            path.attr("x1", function (d) {
                    return d.source.x;
                })
                .attr("y1", function (d) {
                    return d.source.y;
                })
                .attr("x2", function (d) {
                    return d.target.x;
                })
                .attr("y2", function (d) {
                    return d.target.y;
                });
*/
            this.force.on("tick", function () {
                var transform = function (d) {
                    d.x = Math.max(0, Math.min(that.width - 5, d.x));
                    if (d.y < 0) {
                        d.y = (Math.random() * 30) + 10;
                    }
                    if (d.y > that.height) {
                        d.y = that.height - (d.y - that.height) - 15 - (Math.random() * 20);
                    }
                    return "translate(" + d.x + "," + d.y + ")";
                }

                path.attr("x1", function (d) {
                        return d.source.x;
                    })
                    .attr("y1", function (d) {
                        return d.source.y;
                    })
                    .attr("x2", function (d) {
                        return d.target.x;
                    })
                    .attr("y2", function (d) {
                        return d.target.y;
                    });
                node.attr("transform", transform);
                text.attr("transform", transform);
                //node.each(that.collide(0.5));

            });
            this.force.start();
        },

        addNode: function (node) {
            //console.log(node);
            //console.log(typeSystem);
            var typeSystem = node.typeSystem ? node.typeSystem : (node.type ? node.type :'common');
            node.size = this.type[typeSystem].size;
            node.symbol = this.type[typeSystem].symbol;
            node.typeSystem = typeSystem;
            node.status = node.status ? node.status : 'none';
            //node.x = (this.width * Math.random());
            //node.y = (this.height / 2);
            node.x = 0;
            node.y = 0;
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
                //this.update();
            }
        },

        addLink: function (link) {
            var sourceNode = this.findNode(link.source.id);
            if (sourceNode) {
                link.source = sourceNode;
            } else {
                this.addNode(link.source);
            }
            var targetNode = this.findNode(link.target.id);
            if (targetNode) {
                link.target = targetNode;
            } else {
                this.addNode(link.target);
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
            link.optional = l.optional;
            link.status = l.status;
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
                    this.links.splice(i, 1);
                }
                else i++;
            }
            //this.update();
        },

        clearLink: function () {
            /*
            var i = 0;
            while (i < this.links.length) {
                this.links.splice(i, 1);
            }
            */
            while (this.links.length) {
                this.links.splice(0, 1);
            }
            //this.update();
        },

        clearNode: function () {
            /*
            var i = 0;
            while (i < this.nodes.length) {
                this.nodes[i].visited = false;
                this.nodes.splice(i, 1);
            }
            */
            while (this.nodes.length) {
                this.nodes.splice(0, 1);
            }
            //this.update();
        },

        highlightNode: function (node) {
            this.highlightNodes.push(node.id);
            //this.update();
        },

        resetHighLight: function () {
            this.highlightNodes = [];
            //this.update();
        },

        findNode: function (id) {
            for (var i = 0; i < this.nodes.length; i++) {
                if (this.nodes[i].id === id) {
                    return this.nodes[i]
                }
            }
            return false;
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
                if ((l.source.id === link.sourceId) && (l.target.id === link.targetId) && (l.type === link.type)) {
                    return l;
                }
            }
            return false;
        },

        /*
        click: function () {
            console.log(this);
            if (d3.event.shiftKey) {
                console.log("Mouse+Shift pressed");
                that.onClick(d3.select(this).data()[0]);
            }
        },

        dblclick: function () {
            that.onDblClick(d3.select(this).data()[0]);
        },

        clickLink: function () {
            that.onClickLink(d3.select(this).data()[0]);
        },
        */

        dragstart: function (d) {
            d3.select(this).classed("fixed", d.fixed = true);
        }

    });

    j.fn.D3Graph = function (o) {
        // initializing
        var args = arguments;
        var o = o || {'container': ''};
        return this.each(function () {
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
                return api[o].apply(api, parameters);
            }
            return api;
        });
    };
})(jQuery);