<script type="text/javascript">

var grapher = {
    isMaster: {{$data->isMaster|noescape}},
    currentyEntity: null,
    relationEntry: {{$data->relationEntry|noescape}},
    relationData: [],
    relations: [],
    onDblClick: function(node) {
        console.log(node);
        this.show(node.id, true);
    },
    clickLink: function(link) {
        this.showLink(link);
    }
};

var i = 0;
for (relation in grapher.relationEntry) {
    grapher.relationData[i] = grapher.relationEntry[relation];
    grapher.relations[i] = grapher.relationEntry[relation]['id'];
    i++;
}

grapher.graph = function (element, relations) {

    var $element = $('#' + element);
    var w = $element.innerWidth() - 10;
    var h = $element.innerHeight() - 10;

    var type = {
        cxn: {symbol: d3.symbolCircle, size:260},
        frame: {symbol: d3.symbolSquare, size:260},
        fe: {symbol: d3.symbolCircle, size:80},
        ce: {symbol: d3.symbolCircle, size:80},
        st: {symbol: d3.symbolTriangle, size:100},
        cp: {symbol: d3.symbolTriangle, size:100}
    };
    
    var svg = this.svg = d3.select($element[0]).append("svg")
            .attr("width", w)
            .attr("height", h);

    var nodes = [],
        links = [];

    var layout = cola.d3adaptor(d3)
        .size([w, h])
        .nodes(nodes)
        .links(links)
        .jaccardLinkLengths(40, 0.7)
        .start(10,15,20);
/*
    var force = d3.layout.force()
        .nodes(nodes)
        .links(links)
        //.gravity(.05)
        .size([w, h])
        .linkDistance(100)
        .charge(-300)
        .start();
*/
/*
    var drag = layout.drag()
        .on("dragstart", dragstart);
*/
    // Add and remove elements on the graph object
    this.addNode = function (node) {
        console.log(node);
        node.size = type[node.type].size; 
        node.symbol = type[node.type].symbol; 
        nodes.push(node);
        update();
    }

    this.removeNode = function (id) {
        var i = 0;
        var n = findNode(id);
        while (i < links.length) {
            if ((links[i]['source'] === n) || (links[i]['target'] == n)) {
                links.splice(i, 1);
            } else {
                i++;
            }
        }
        var index = findNodeIndex(id);
        if (index !== undefined) {
            nodes.splice(index, 1);
            update();
        }
    }

    this.addLink = function (link) {
        var sourceNode = findNode(link.source.id);
        if (sourceNode === undefined) {
            this.addNode(link.source);
        } else {
            link.source = sourceNode;
        }
        var targetNode = findNode(link.target.id);
        if (targetNode === undefined) {
            this.addNode(link.target);
        } else {
            link.target = targetNode;
        }
        var existsLink = findLink(link);
        if (!existsLink) {
            links.push(link);
        }
        console.log(links);
        update();
    }
    
    this.refreshLink = function (types) {
        var i = 0;
        while (i < links.length) {
            if (types[links[i]['type']] === undefined) {
                links.splice(i,1);
            }
            else i++;
        }
        update();
    }
    
    this.clearLink = function () {
        var i = 0;
        while (i < links.length) {
            links.splice(i,1);
        }
        update();
    }

    this.clearNode = function () {
        var i = 0;
        while (i < nodes.length) {
            nodes.splice(i,1);
        }
        update();
    }

    var findNode = function (id) {
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].id === id) {
                return nodes[i]
            }
        }
    }

    var findNodeIndex = function (id) {
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].id === id) {
                return i
            }
        }
    }

    var findLink = function (link) {
        for (var i = 0; i < links.length; i++) {
            var l = links[i];
            if ((l.source.id === link.source.id) && (l.target.id === link.target.id) && (l.type === link.type)) {
                return true;
            }
        }
        return false;
    }
    
    // Per-type markers, as they don't inherit styles.
    svg.append("defs").selectAll("marker")
        .data(relations)
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
        .attr("d", "M0,-3L8,0L0,3");
    
    function dblclick() {
        grapher.onDblClick(d3.select(this).data()[0]);
    }
    
    function clickLink() {
        grapher.clickLink(d3.select(this).data()[0]);
    }

    function dragstart(d) {
        d3.select(this).classed("fixed", d.fixed = true);
    }   

    var update = function () {
        svg.selectAll("g").remove();
        
        var link = svg.append("g")
            .selectAll("line")
            .data(links)
            .enter().append("line")
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
            .on("dblclick", clickLink);

        var node = svg.append("g")
            .selectAll("path")
            .data(nodes)
            .enter().append('path')
            .attr("d", d3.symbol()
                 .size(function(d) {return d.size;})
                 .type(function(d) {return d.symbol;})
            )
            .attr("class", function (d) {
                 var cssClass = ((d.id == grapher.currentEntity) ? " nodeSelected" : " nodeNormal") + ' entity_' + d.type;
                 return cssClass;
            })
            .on("click", function (d) { d.fixed = true })
            .on("dblclick", dblclick)
            .call(layout.drag);

        var text = svg.append("g")
            .selectAll("text")
            .data(nodes)
            .enter().append("text")
            .attr("x", 8)
            .attr("y", ".31em")
            .text(function (d) {
                return d.name;
            });


        layout.on("tick", () => {
            link
                .attr("x1", function(d) { return d.source.x; })
                .attr("y1", function(d) { return d.source.y; })
                .attr("x2", function(d) { return d.target.x; })
                .attr("y2", function(d) { return d.target.y; });

            node
                .attr("transform", function(d) {
                    return "translate(" + d.x + "," + d.y + ")";
                });

            text
                .attr("transform", function(d) {
                    return "translate(" + d.x + "," + d.y + ")";
                });
        });

        layout.start(10,15,20);

    }

    // Make it all go
    update();
}

</script>

