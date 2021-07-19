let grapher = {
    nodes: [],
    links: [],
    graphViz: null,
    init: () => {
        grapher.graphViz = d3.select("#output_graph_content")
            .graphviz()
            .tweenPaths(false)
            .tweenShapes(false);
        grapher.nodes = [];
        grapher.links = [];
    },
    nodeSpec: {
        "lu": {
            color: "#428bca",
            fontcolor: "white"
        }
    },
    relationSpec: {
        "rel_qualia_formal": {
            color: "red",
            linkOptions: "arrowsize = 0.0  style = \"solid\" "
        },
        "rel_qualia_constitutive": {
            color: "darkgreen",
            linkOptions: "arrowsize = 0.0  style = \"solid\" "
        },
        "rel_qualia_agentive": {
            color: "blue",
            linkOptions: "arrowsize = 0.0  style = \"solid\" "
        },
        "rel_qualia_telic": {
            color: "darkgold",
            linkOptions: "arrowsize = 0.0  style = \"solid\" "
        },
    },
    convert: (rowLinks) => {
        // rowLink format = {"source":{"id":"","type":"","name":""},"type":"","target":{"id":"","type":"","name":""}}
        let linkIndex = '';
        for (rowLink of rowLinks) {
            console.log(rowLink);
            if (typeof grapher.nodes[rowLink.source.id] == 'undefined') {
                grapher.nodes[rowLink.source.id] = {
                    id: rowLink.source.id,
                    type: rowLink.source.type,
                    name: rowLink.source.name,
                }
            }
            if (typeof grapher.nodes[rowLink.target.id] == 'undefined') {
                grapher.nodes[rowLink.target.id] = {
                    id: rowLink.target.id,
                    type: rowLink.target.type,
                    name: rowLink.target.name,
                }
            }
            if (rowLink.type !== 'rel_none') {
                linkIndex = rowLink.source.id + '_' + rowLink.target.id + '_' + rowLink.type;
                if (typeof grapher.links[linkIndex] == 'undefined') {
                    grapher.links[linkIndex] = {
                        idSource: rowLink.source.id,
                        idTarget: rowLink.target.id,
                        relation: rowLink.type
                    }
                }
            }
        }
    },
    render: () => {
        let dotLines = grapher.createDot();
        grapher.renderDot(dotLines);
    },
    createDot: () => {
        let nodes = grapher.nodes;
        let links = grapher.links;
        let dotGraph = {};
        let dotLines = [
            "digraph  {",
            "   node [height=0.08 shape=\"box\" style=\"rounded\" ]",
            "   rankdir = LR",
            "   ranksep =0.50",
            "   forcelabels = true",
            "   K = 0.5",
            "   nodesep = 0.1",
            "   fontsize = 8",
        ];
        dotLines.push("");
        for (let id in nodes) {
            let node = nodes[id];
            dotGraph[node.id] = node;
        }
        for (let n in dotGraph) {
            node = dotGraph[n];
            let spec = "";
            let color = grapher.nodeSpec[node.type].color;
            let fontcolor = grapher.nodeSpec[node.type].fontcolor;
            spec = spec + `fillcolor = "${color}" `;
            spec = spec + `fontcolor = "${fontcolor}" `;
            //spec = spec + `fontname = "helvetica" `;
            spec = spec + `fontsize = 8 `;
            spec = spec + `style="rounded,filled"`;
            node.spec = spec;
            dotLines.push("    " + node.id + " [" + spec + " label = \"" + node.name + "\"]" + "\n");
        }
        let spec;
        for (let idLink in links) {
            let link = links[idLink];
            //console.log(link);
            spec = link.idSource + " -> " + link.idTarget + " [" + grapher.relationSpec[link.relation].linkOptions + " color = \"" + grapher.relationSpec[link.relation].color + "\"" + "]\n";
            dotLines.push(spec);
        }
        dotLines.push("}");
        console.log(dotLines.join(""));
        return dotLines;
    },
    renderDot: (dotLines) => {
        let scale = 1;
        let numLinks = grapher.links.length;
        console.log('numLinks ', numLinks);
        if (numLinks < 10) {
            scale = (numLinks > 2) ? numLinks / 10 : 0.25;
        }
        console.log('scale', scale);
        grapher.graphViz
            .fade(false)
            .fit(true)
            .scale(scale)
            .renderDot(dotLines.join(""))
            .on("end", grapher.onEnd);
    },
    onEnd: () => {
        nodes = d3.selectAll('.node');
        nodes
            .on("click", function () {
                let idEntity = d3.select(this).datum().key;
                console.log(idEntity);
                grapher.add(idEntity)
            });
    }

}