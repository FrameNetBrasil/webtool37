<?php

class GrapherGraphVizService extends MService
{
    public $structure;
    public $typeNode;
    public $typeEdge;
    public $typeStatus;
    public $fontSize;

    public function __construct()
    {
        $this->fontSize = 8;
        $this->typeNode = [
            'word' => [
                'color' => 'red',
                'bgcolor' => 'red',
                'style' => 'filled',
                'shape' => 'box'
            ],
            'lexeme' => [
                'color' => 'dodgerblue',
                'bgcolor' => 'dodgerblue',
                'style' => 'filled',
                'shape' => 'box',
                'fontColor' => 'white'
            ],
            'le' => [
                'color' => 'blue',
                'bgcolor' => 'blue',
                'style' => 'filled',
                'shape' => 'box'
            ],
            'lemma' => [
                'color' => 'mediumblue',
                'bgcolor' => 'mediumblue',
                'style' => 'filled',
                'shape' => 'box',
                'fontColor' => 'white'
            ],
            'lu' => [
                'color' => 'navy',
                'bgcolor' => 'navy',
                'style' => 'filled',
                'shape' => 'box',
                'fontColor' => 'white'
            ],
            'pos' => [
                'color' => 'white',
                'bgcolor' => 'limegreen',
                'style' => 'filled',
                'shape' => 'box'
            ],
            'ce' => [
                'color' => 'blue',
                'bgcolor' => 'blue',
                'style' => 'filled',
                'shape' => 'box'
            ],
            'cxn' => [
                'color' => 'white',
                'bgcolor' => 'forestgreen',
                'fontcolor' => 'white',
                'style' => 'filled',
                'shape' => 'box',
                'fontColor' => 'white'
            ],
            'fe' => [
                'color' => 'blue',
                'bgcolor' => 'blue',
                'style' => 'filled',
                'shape' => 'circle'
            ],
            'frame' => [
                'color' => 'white',
                'bgcolor' => 'red',
                'fontcolor' => 'white',
                'style' => 'filled,rounded',
                'shape' => 'box',
                'fontColor' => 'white'
            ],
            'relay' => [
                'color' => 'forestgreen',
                'bgcolor' => 'forestgreen',
                'style' => 'filled',
                'shape' => 'diamond'
            ],
            'relation' => [
                'color' => 'navy',
                'bgcolor' => 'navy',
                'style' => 'filled',
                'shape' => 'triangle'
            ],
            'inhibits' => [
                'color' => 'yellow',
                'bgcolor' => 'yellow',
                'style' => 'filled',
                'shape' => 'triangle'
            ],
            'constraint' => [
                'color' => 'white',
                'bgcolor' => 'white',
                'style' => 'filled',
                'shape' => 'triangle'
            ],
            'constraintbefore' => [
                'color' => 'goldenrod',
                'bgcolor' => 'goldenrod',
                'style' => 'filled',
                'shape' => 'triangle'
            ],
            'constraintmeets' => [
                'color' => 'goldenrod',
                'bgcolor' => 'goldenrod',
                'style' => 'filled',
                'shape' => 'triangle'
            ],
            'constraintdifferent' => [
                'color' => 'goldenrod',
                'bgcolor' => 'goldenrod',
                'style' => 'filled',
                'shape' => 'triangle'
            ],
            'constraintrelation' => [
                'color' => 'goldenrod',
                'bgcolor' => 'goldenrod',
                'style' => 'filled',
                'shape' => 'triangle'
            ],
        ];
        $this->typeEdge = [
            'rel_argument1' => [
                'color' => 'orange',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'empty'
            ],
            'rel_argument2' => [
                'color' => 'orangered',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'empty'
            ],
            'rel_argument' => [
                'color' => 'violet',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'empty'
            ],
            'rel_value' => [
                'color' => 'purple',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'empty'
            ],
            'rel_relay' => [
                'color' => 'purple',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'empty'
            ],
            'rel_evokes' => [
                'color' => 'olivedrab',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'empty'
            ],
            'rel_elementof' => [
                'color' => 'royalblue',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'empty'
            ],
            'rel_constraint' => [
                'color' => 'goldenrod',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'empty'
            ],
            'rel_constraint_before' => [
                'color' => 'goldenrod',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'empty'
            ],
            'rel_inheritance' => [
                'color' => 'red',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_inheritance_cxn' => [
                'color' => 'red',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_lexicon' => [
                'color' => 'black',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_pos' => [
                'color' => 'limegreen',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_subframe' => [
                'color' => 'blue',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_perspective_on' => [
                'color' => 'lightpink',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_precedes' => [
                'color' => 'black',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_inchoative_of' => [
                'color' => 'gold4',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_causative_of' => [
                'color' => 'gold',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_using' => [
                'color' => 'darkgreen',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
        ];
        $this->typeStatus = [
            'inactive' => [
                'color' => 'whitesmoke',
                'fontColor' => 'black'
            ],
            'predictive' => [
                'color' => 'slategrey',
                'fontColor' => 'black'
            ],
            'constrained' => [
                'color' => 'firebrick',
                'fontColor' => 'black'
            ],
            'exhausted' => [
                'color' => 'white',
                'fontColor' => 'black'
            ],
            'waiting' => [
                'color' => 'lightgrey',
                'fontColor' => 'black'
            ],
            'inhibited' => [
                'color' => 'yellow',
                'fontColor' => 'black'
            ],
        ];
    }

    public function generateDot()
    {
        $graph = $this->createGraph();
        $dot = $graph->parse();
        return $dot;
    }

    public function generatePNG()
    {
        $graph = $this->createGraph();
        $file = $graph->fetch('png');
        return $file;
    }

    public function generateSVG()
    {
        $graph = $this->createGraph();
        $file = $graph->fetch('svg');
        return $file;
    }

    public function createFromFile($fileName) {
        $rows = file($fileName);
        $list = array();
        $nodes = array();
        $edges = array();
        $graph = array();
        $bgColor = 0;
//  #node(N): id : type : level
//  #node(E): node1 : node2: type : label
        foreach ($rows as $row) {
            if (substr($row, 0, 1) != '#') {
                $row = str_replace("\n", "", $row);
                $line = explode(':', $row);
                if ($line[0] == 'G') {
                    $graph[$line[1]] = $line[2];
                }
                if ($line[0] == 'N') {
                    // line = $id, $type, $level, $label
                    $nodes[$line[1]] = [
                        'typeSystem' => $line[2],
                        'name' => $line[4],
                        'id' => $line[1],
                        'status' => 'active',
                        'index' => $line[1],
                        'group' => 'group',
                    ];
                }
                if ($line[0] == 'E') {
                    // line = idSub, idSuper, relation
                    $label = $line[3];//$line[6] ? $line[4] . ' [' . $line[6] . ']' : $line[4];
                    $edges[] = [
                        'source' => $line[1],
                        'target' => $line[2],
                        'label' => $label,
                        'optional' => '0',
                        'head' => '0',
                        'status' => 'active',
                    ];

                }
            }
        }
        $this->structure = (object)['graph' => $graph, 'nodes' => $nodes, 'links' => $edges];
        $graph = $this->createGraph();
        $graphviz = new Graphp\GraphViz\GraphViz();
        $imageFile = $graphviz->createImageFile($graph);
        return $imageFile;
    }

    private function createGraph()
    {
        $graph = new Fhaculty\Graph\Graph();
        $graph->setAttribute('graphviz.graph.rankdir', $this->structure->graph['rankdir']);
        $graph->setAttribute('graphviz.graph.ranksep', '0.50');
        $graph->setAttribute('graphviz.graph.layout', 'dot');
        $graph->setAttribute('graphviz.graph.forcelabels', 'true');
        $graph->setAttribute('graphviz.graph.K', '0.5');
        $graph->setAttribute('graphviz.graph.nodesep', '0.1');
        $graph->setAttribute('graphviz.graph.fontsize', $this->fontSize);

/*
        $directed = true;
        $graph = new GraphViz($directed, ['colorscheme' => 'svg'], 'G', false, true);
        $n['rank'] = 'same';
        $graph->addSubGraph('g1', '', $n, 'group1');
        $graph->addSubGraph('g2', '', $n, 'group2');
        $graph->addSubGraph('g3', '', $n, 'group3');
        $graph->addSubGraph('g4', '', $n, 'group4');
        $graph->addSubGraph('g5', '', $n, 'group5');
        $graph->addSubGraph('g6', '', $n, 'group6');
        $graph->addSubGraph('g7', '', $n, 'group7');
        $graph->addSubGraph('g8', '', $n, 'group8');
        $graph->addSubGraph('g9', '', $n, 'group9');
        if (count($this->structure->links)) {
            $this->addEdges($graph, $this->structure->links);
        }
*/
        if (count($this->structure->nodes)) {
            $this->addNodes($graph, $this->structure->nodes);
        }
        if (count($this->structure->links)) {
            $this->addEdges($graph, $this->structure->links);
        }
        return $graph;
    }

    private function addNodes($graph, $nodes)
    {
        foreach ($nodes as $node) {
            $nodeType = $node['typeSystem'];
            if (($nodeType == 'frame') || ($nodeType == 'cxn') || ($nodeType == 'lexeme') || ($nodeType == 'lemma') || ($nodeType == 'lu') || ($nodeType == 'pos')) {
                //$label = $node['name'] . ' [' . $node['id'] . ']' . ' [' . $node['activation'] . ']';
                $label = $node['name'] . ' [' . $node['id'] . ']';
                $xlabel = '';
            } elseif (($nodeType == 'relay') || ($nodeType == 'le')) {
                $label = '';
                $xlabel = ' [' . $node['id'] . ']';
            } else {
                $label = '';
                $xlabel = $node['name'] . ' [' . $node['id'] . ']';
            }
            $style = $this->typeNode[$nodeType]['style'];
            $status = $node['status'];
            if ($status == 'active') {
                $color = $this->typeNode[$nodeType]['bgcolor'];
                $fontColor = $this->typeNode[$nodeType]['fontColor'] ?: 'black';
            } else {
                $color = $this->typeStatus[$status]['color'];
                $fontColor = $this->typeStatus[$status]['fontColor'] ?: 'black';
            }
            $shape = $this->typeNode[$nodeType]['shape'];
            //$size = (($shape == 'triangle') || ($shape == 'diamond')) ? '0.2' : '0.1';
            $size = (($shape == 'triangle')) ? '0.15' : '0.1';
            $vertex = $graph->createVertex( $node['id']);
            $vertex->setAttribute('id', $node['id']);

            if ($label != '') {
                $vertex->setAttribute('graphviz.xlabel', $xlabel);
                $vertex->setAttribute('graphviz.label', $label);
                $vertex->setAttribute('graphviz.tooltip', '');
                $vertex->setAttribute('graphviz.fontname', 'helvetica');
                $vertex->setAttribute('graphviz.shape', $shape);
                $vertex->setAttribute('graphviz.height', $size);
                $vertex->setAttribute('graphviz.width', $size);
                $vertex->setAttribute('graphviz.style', $style);
                $vertex->setAttribute('graphviz.fillcolor', $color);
                $vertex->setAttribute('graphviz.fontcolor', $fontColor);
                $vertex->setAttribute('graphviz.fontsize', $this->fontSize);
            } else {
                $vertex->setAttribute('graphviz.xlabel', $xlabel);
                $vertex->setAttribute('graphviz.label', $label);
                $vertex->setAttribute('graphviz.tooltip', '');
                $vertex->setAttribute('graphviz.fontname', 'helvetica');
                $vertex->setAttribute('graphviz.shape', $shape);
                $vertex->setAttribute('graphviz.height', $size);
                $vertex->setAttribute('graphviz.width', $size);
                $vertex->setAttribute('graphviz.style', $style);
                $vertex->setAttribute('graphviz.fillcolor', $color);
                $vertex->setAttribute('graphviz.fontcolor', $fontColor);
                $vertex->setAttribute('graphviz.fontsize', $this->fontSize);
            }
        }
    }

    private function addEdges($graph, $links)
    {
        foreach ($links as $link) {
            $t = $this->typeEdge[$link['label']];
            $label = '';//' ' . $e[1];
            $optional = $link['optional'];
            $head = $link['head'];
            $color = ($link['status'] == 'active') ? $t['color'] : 'gray';
            //var_dump($t);
            $a = $graph->getVertex($link['source']);
            $b = $graph->getVertex($link['target']);
            $edge = $a->createEdgeTo($b);
            $edge->setAttribute('graphviz.color', $color);
            $edge->setAttribute('graphviz.label', $label);
            $edge->setAttribute('graphviz.minlen', '1');
            $edge->setAttribute('graphviz.fontname', 'helvetica');
            $edge->setAttribute('graphviz.fontsize', $this->fontSize);
            $edge->setAttribute('graphviz.arrowsize', '0.5');
            $edge->setAttribute('graphviz.arrowhead', ($head == '1' ? 'normal' : $t['arrowType']));
            $edge->setAttribute('graphviz.penwidth', $t['penwidth']);
            $edge->setAttribute('graphviz.style', ($optional == '1' ? 'dashed' : $t['style']));
        }
    }


}