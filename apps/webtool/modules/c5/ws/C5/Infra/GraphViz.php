<?php
namespace C5\Infra;

class GraphViz extends \Mesh\Infra\GraphViz
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

    public function __construct()
    {
        parent::__construct();
        $this->typeNode = [
            'Cxn' => [
                'labelType' => 'l',
                'color' => 'white',
                'bgcolor' => 'forestgreen',
                'fontcolor' => 'white',
                'style' => 'filled',
                'shape' => 'box',
                'fontColor' => 'white'
            ],
            'CE' => [
                'labelType' => 'l',
                'fontcolor' => 'white',
                'style' => 'filled,rounded',
                'fontColor' => 'white',
                'color' => 'blue',
                'bgcolor' => 'blue',
                'shape' => 'box'
            ],
            'Frame' => [
                'labelType' => 'l',
                'color' => 'white',
                'bgcolor' => 'red',
                'fontcolor' => 'white',
                'style' => 'filled,rounded',
                'shape' => 'box',
                'fontColor' => 'white'
            ],
            'Concept' => [
                'labelType' => 'l',
                'color' => 'white',
                'bgcolor' => 'purple',
                'fontcolor' => 'white',
                'style' => 'filled,rounded',
                'shape' => 'box',
                'fontColor' => 'white'
            ],
            'UDRelation' => [
                'labelType' => 'l',
                'color' => 'white',
                'bgcolor' => 'orange',
                'fontcolor' => 'white',
                'style' => 'filled',
                'shape' => 'box',
                'fontColor' => 'white'
            ],
            'Constraint' => [
                'labelType' => 'l',
                'color' => 'white',
                'bgcolor' => 'chocolate',
                'fontcolor' => 'white',
                'style' => 'filled',
                'shape' => 'box',
                'fontColor' => 'white'
            ],

            /*
                        'word' => [
                            'labelType' => 'l',
                            'color' => 'red',
                            'bgcolor' => 'red',
                            'style' => 'filled',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        'lexeme' => [
                            'labelType' => 'l',
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
                            'labelType' => 'l',
                            'color' => 'mediumblue',
                            'bgcolor' => 'mediumblue',
                            'style' => 'filled',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        'lu' => [
                            'labelType' => 'l',
                            'color' => 'navy',
                            'bgcolor' => 'navy',
                            'style' => 'filled',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        'pos' => [
                            'labelType' => 'l',
                            'color' => 'white',
                            'bgcolor' => 'limegreen',
                            'style' => 'filled',
                            'shape' => 'box'
                        ],
                        'de' => [
                            'color' => 'blue',
                            'bgcolor' => 'blue',
                            'style' => 'filled',
                            'shape' => 'box'
                        ],
                        'udrelation' => [
                            'labelType' => 'l',
                            'color' => 'white',
                            'bgcolor' => 'limegreen',
                            'style' => 'filled',
                            'shape' => 'box'
                        ],
                        'ud' => [
                            'labelType' => 'l',
                            'color' => 'white',
                            'bgcolor' => 'forestgreen',
                            'fontcolor' => 'white',
                            'style' => 'filled',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        'xe' => [
                            'color' => 'blue',
                            'bgcolor' => 'blue',
                            'style' => 'filled',
                            'shape' => 'circle'
                        ],
                        'iface' => [
                            'labelType' => 'l',
                            'color' => 'white',
                            'bgcolor' => 'forestgreen',
                            'fontcolor' => 'white',
                            'style' => 'filled',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        've' => [
                            'color' => 'blue',
                            'bgcolor' => 'blue',
                            'style' => 'filled',
                            'shape' => 'box'
                        ],
                        'valence' => [
                            'labelType' => 'l',
                            'color' => 'white',
                            'bgcolor' => 'red',
                            'fontcolor' => 'white',
                            'style' => 'filled,rounded',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        'fe' => [
                            'color' => 'blue',
                            'bgcolor' => 'blue',
                            'style' => 'filled',
                            'shape' => 'box'
                        ],
                        'relay' => [
                            'labelType' => 'i',
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'diamond'
                        ],
                        'relation' => [
                            'color' => 'white',
                            'bgcolor' => 'navy',
                            'labelType' => 'l',
                            'fontcolor' => 'white',
                            'style' => 'filled,rounded',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        'inhibitory' => [
                            'color' => 'white',
                            'bgcolor' => 'brown',
                            'labelType' => 'l',
                            'fontcolor' => 'white',
                            'style' => 'filled,rounded',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        're' => [
                            'color' => 'blue',
                            'bgcolor' => 'blue',
                            'style' => 'filled',
                            'shape' => 'box'
                        ],
                        'inhibits' => [
                            'color' => 'yellow',
                            'bgcolor' => 'yellow',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraint' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintbefore' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintafter' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintmeets' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintdifferent' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintsame' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintvalence' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintandmeets' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintand' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'hexagon'
                        ],
                        'constraintxor' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'hexagon'
                        ],
                        'constraintfollows' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintdominance' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintdomino' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constrainthead' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintrelation' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constrainthasword' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintargument1' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintargument2' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'constraintnear' => [
                            'color' => 'forestgreen',
                            'bgcolor' => 'forestgreen',
                            'style' => 'filled',
                            'shape' => 'triangle'
                        ],
                        'deprel' => [
                            'color' => 'white',
                            'bgcolor' => 'navy',
                            'labelType' => 'l',
                            'fontcolor' => 'white',
                            'style' => 'filled,rounded',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        'root' => [
                            'color' => 'white',
                            'bgcolor' => 'navy',
                            'labelType' => 'l',
                            'fontcolor' => 'white',
                            'style' => 'filled,rounded',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        'feature' => [
                            'color' => 'white',
                            'bgcolor' => 'navy',
                            'labelType' => 'l',
                            'fontcolor' => 'white',
                            'style' => 'filled,rounded',
                            'shape' => 'box',
                            'fontColor' => 'white'
                        ],
                        'pool' => [
                            'color' => 'blue',
                            'bgcolor' => 'blue',
                            'style' => 'filled',
                            'shape' => 'box'
                        ],
            */
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
                'style' => 'dashed',
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
                'color' => 'red3',
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
            'rel_ud' => [
                'color' => 'limegreen',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_isa' => [
                'color' => 'red',
                'style' => 'solid',
                'penwidth' => '1',
                'arrowType' => 'normal'
            ],
            'rel_projection' => [
                'color' => 'red',
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
                'color' => 'yellow',
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
                'color' => 'brown',
                'fontColor' => 'white'
            ],
        ];
        $this->graphAttributes = [
            'rankdir' => 'LR',
            'ranksep' => '0.50', //'0.3', //'0.75',
            //'rank' => 'source',
            'layout' => 'dot',
            //'height' => '0.3',
            'forcelabels' => 'true',
            //'size' => '13.4,12',
            'K' => '0.5',
            'nodesep' => '0.1',
            'fontsize' => $this->fontSize
        ];
    }

    public function getInfo($node) {
        $info = "id: " . $node['id'] .
            "\nname: " . $node['name'] .
            //"\nword: " . $node['wordIndex']  .
            //"\nh: " . $node['h'] .
            //"\nd: " . $node['d'] .
            //"\nidHead: " . $node['idHead'] .
            "\nw: " . $node['w'] .
            "\nstatus: " . $node['status'].
            "\nisQuery: " . ($node['isQuery'] ? 'true' : 'false') .
            //"\nslots: " . $node['strSlots'].
            "\ntype: " . $node['type'] .
            "\nclass: " . $node['class'] .
            "\na: " . $node['activation'];
            //"\nwords: " . $this->words

        return $info;
    }

}

