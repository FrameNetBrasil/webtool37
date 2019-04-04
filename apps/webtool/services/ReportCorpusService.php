<?php

class ReportCorpusService extends MService
{

    public function getDataReportCorpus()
    {
        $idCorpus = $this->data->id;
        $corpus = new fnbr\models\Corpus($idCorpus);
        $query = $corpus->listAnnotationReport($this->data->sort, $this->data->order);
        $result = $query->getResult();
        return $result;
    }

    public function getDataReportDocument()
    {
        $idDocument = $this->data->id;
        $document = new fnbr\models\Document($idDocument);
        $options = [
            'fe' => $this->data->fe,
            'gf' => $this->data->gf,
            'pt' => $this->data->pt,
            'ni' => $this->data->ni,
        ];
        $query = $document->listAnnotationReport($options, $this->data->sort, $this->data->order);
        $result = $query->getResult();
        return $result;
    }

    public function getAnnotationSets()
    {
        $result = [];
        $sentence = new fnbr\models\Sentence($this->data->idSentence);
        $viewAS = new fnbr\models\ViewAnnotationSet();
        $as = new fnbr\models\AnnotationSet();
        $annotationSets = $as->getAnnotationSets($this->data->idSentence);
        foreach ($annotationSets as $annotationSet) {
            if ($annotationSet['type'] == 'lu') {
                list($frame, $lu) = explode('.', $annotationSet['name']);
                $annotation = $viewAS->listFECEByAS($annotationSet['idAnnotationSet']);
                $node = array();
                $node['frame'] = $frame;
                $node['lu'] = $lu;
                $node['idAnnotationSet'] = $annotationSet['idAnnotationSet'];
                $node['idSentence'] = $this->data->idSentence;
                if ($annotation[$this->data->idSentence]) {
                    $decorated = $this->decorateSentence($sentence->getText(), $annotation[$this->data->idSentence]);
                    $node['text'] = $decorated['text'];
                    $node['fes'] = $decorated['fes'];
                } else {
                    $node['text'] = $sentence->getText();
                    $node['fes'] = [];
                }
                $node['status'] = $annotation['annotationStatus'] ?: "";
                $node['rgbBg'] = $annotation['rgbBg'] ?: "";
                $result[] = $node;
            }
        }
        return json_encode($result);
    }

    public function decorateSentence($sentence, $labels)
    {
        $sentence = utf8_decode($sentence);
        mdump($sentence);
        mdump($labels);
        $layer = [];
        $tempStartChar = -2;
        foreach($labels as $i => $label) {
            $startChar = $label['startChar'];
            if ($startChar >= 0) {
                if ($startChar > $tempStartChar) {
                    $layer[0][$i] = $label;
                } else {
                    if (isset($layer[1][$startChar])) {
                        if (isset($layer[2][$startChar])) {
                            if (isset($layer[3][$startChar])) {
                                if (isset($layer[4][$startChar])) {
                                } else {
                                    $layer[4][$startChar] = $label;
                                }
                            } else {
                                $layer[3][$startChar] = $label;
                            }
                        } else {
                            $layer[2][$startChar] = $label;
                        }
                    } else {
                        $layer[1][$startChar] = $label;
                    }
                }
                $tempStartChar = $label['startChar'];
            } else {
                $layer[0][$i] = $label;
            }
        }
        $fes = [];
        $text = '';
        foreach($layer as $layerNum => $layerLabels) {
            $i = 0;
            $ni = "";
            $decorated = "";
            $invisible = 'background-color:#FFFFF;color:#FFFFFF;';
            foreach($layerLabels as $label) {
                $style = 'background-color:#' . $label['rgbBg'] . ';color:#' . $label['rgbFg'] . ';';
                $entry = ($label['feEntry'] ?: 'target');
                $class = 'fe_' . $entry;
                $fes[] = [
                    'entry' => $entry,
                    'name' => $label['feName'] ?: "Target",
                    'bg' => $label['rgbBg'],
                    'color' => $label['rgbFg']
                ];
                if ($label['startChar'] >= 0) {
                    if ($layerNum == 0) {
                        $decorated .= substr($sentence, $i, $label['startChar'] - $i);
                    } else {
                        $decorated .= "<span style='{$invisible}'>" . substr($sentence, $i, $label['startChar'] - $i) . "</span>";
                    }
                    //$decorated .= "<span style='{$style}'>" . substr($sentence, $label['startChar'], $label['endChar'] - $label['startChar'] + 1) . "</span>";
                    $decorated .= "<span class=\"{$class}\">" . substr($sentence, $label['startChar'], $label['endChar'] - $label['startChar'] + 1) . "</span>";
                    $i = $label['endChar'] + 1;
                } else { // null instantiation
                    $ni .= "<span class=\"{$class}\">" . $label['instantiationType'] . "</span> ";
                    mdump($ni);
                }
            }
            if ($layerNum == 0) {
                $decorated .= substr($sentence, $i) . $ni;
            } else {
                $decorated .= "<span style='{$invisible}'>" . substr($sentence, $i) . "</span>";
            }
            $text .= ($layerNum > 0 ? '<br/>' : '') . utf8_encode($decorated);
        }
        $result = [
            'fes' => $fes,
            'text' => $text
        ];
        return $result;
    }

}
