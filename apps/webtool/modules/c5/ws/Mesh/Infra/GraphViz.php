<?php
namespace Mesh\Infra;

require_once 'Image/GraphViz.php';

class GraphViz
{

    /* nodes [
            'index' => $i,
            'id' => $node->getId(),
            'name' => utf8_encode($node->getLabel()),
            'position' => $node->index,
            'activation' => round($node->getO(), 5),
            'type' => $node->getType(),
            'status' => $node->status,
            'group' => $node->group
       ];

        links [
            'source' => $cola[$node->getId()],
            'target' => $cola[$this->getSite($target)->idNode()],
            'label' => $site->label() ?: 'rel_common',
            'status' => (($site->active() || $site->predictive()) ? 'active' : 'inactive'),
            'optional' => $site->optional()
        ];

        groups[nameGroup][link]

     */

    public $structure;
    public $typeNode;
    public $typeEdge;
    public $typeStatus;
    public $graphAttributes;
    public $fontSize;
    public $words;

    public function __construct()
    {
        $this->fontSize = 8;
        $this->graphAttributes = [];
    }

    public function setStructure($structure) {
        $this->structure = $structure;
        $this->words = $this->structure->words;
    }

    public function generateDot()
    {
        $graph = $this->createGraph();
        $dot = $graph->parse();
        return $dot;
    }

    public function createGraph()
    {
        $directed = true;
        $graph = new \Image_GraphViz($directed, ['colorscheme' => 'svg'], 'G', false, true);
        if (isset($this->structure->regions)) {
            $regions = $this->structure->regions;
            foreach ($regions as $region) {
                $graph->addCluster('cluster_r' . $region, 'Region ' . $region);
            }
        }
        if (count($this->structure->nodes)) {
            $this->addNodes($graph, $this->structure->nodes);
        }
        if (count($this->structure->links)) {
            $this->addEdges($graph, $this->structure->links);
        }

        $graph->addAttributes($this->graphAttributes);
        return $graph;
    }

    public function getInfo($node) {
        return '';
    }

    private function addNodes(&$graph, $nodes)
    {
        foreach ($nodes as $node) {
            $nodeType = $node['type'];
            $labelType = $this->typeNode[$nodeType]['labelType'] ?: 'x';
            $a = substr($node['activation'],0,6);
            $words = '';
            $slots = $node['slots'];
            for($i = 1; $i <= count($this->words); $i++) {
                if ($slots->get($i)) {
                    $words .= $this->words[$i] . ' ';
                }
            }
            $info = $this->getInfo($node);

            if ($labelType == 'l') {
                $label = $node['name'] . ' [' . $a . ']';
                $xlabel = $info;
            } else if ($labelType == 'i') {
                $label = '';
                $xlabel = $info;
            } else if ($labelType == 'x') {
                $label = '';
                $xlabel = $info;
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
            $tooltip = $xlabel;
            $xlabel = '';

            $shape = $this->typeNode[$nodeType]['shape'];
            if ($node['logic'] == 'N') {
                $shape = 'component';
                $label = $xlabel = '';
            }
            //$size = (($shape == 'triangle') || ($shape == 'diamond')) ? '0.2' : '0.1';
            $size = (($shape == 'triangle')) ? '0.15' : '0.1';
            if ($label != '') {
                $graph->addNode(
                    $node['id'], [
                        'xlabel' => $xlabel,
                        'label' => $label,
                        'tooltip' => $tooltip,
                        'fontname' => 'helvetica',
                        'shape' => $shape,
                        'height' => $size,
                        'width' => $size,
                        'style' => $style,
                        'fillcolor' => $color,
                        'fontcolor' => $fontColor,
                        'fontsize' => $this->fontSize
                    ]
                    , 'cluster_r' . $node['region']
                );
            } else {
                $graph->addNode(
                    $node['id'], [
                        'xlabel' => $xlabel,
                        'label' => $label,
                        'tooltip' => $tooltip,
                        'fontname' => 'helvetica',
                        'shape' => $shape,
                        'height' => $size,
                        'width' => $size,
                        'style' => $style,
                        'fontcolor' => $fontColor,
                        'fillcolor' => $color,
                        'fontsize' => $this->fontSize
                    ]
                    , 'cluster_r' . $node['region']
                );
            }
        }
    }

    private function addEdges(&$graph, $links)
    {
        foreach ($links as $link) {
            $t = $this->typeEdge[$link['label']];
            $label = '';//' ' . $e[1];
            $optional = $link['optional'];
            $head = $link['head'];
            $color = ($link['status'] == 'active') ? $t['color'] : 'gray';
            //var_dump($t);
            $graph->addEdge(
                [
                    $link['source'] => $link['target'],
                ], [
                    'color' => $color,
                    'label' => $label,
                    'xlabel' => $label,
                    'tooltip' => $label,
                    'minlen' => '1',
                    'fontname' => 'helvetica',
                    'fontsize' => $this->fontSize,
                    'arrowsize' => '0.5',
                    'arrowhead' => ($head == '1' ? 'normal' : $t['arrowType']),
                    'penwidth' => $t['penwidth'],
                    'style' => ($optional == '1' ? 'dashed' : $t['style'])
                ]
            );
        }
    }

}

