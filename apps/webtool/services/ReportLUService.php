<?php

class ReportLUService extends MService
{

    public function getFERealizations($lu)
    {
        $valence = new fnbr\models\Valence();
        $rows = $valence->FERealizations($lu->getIdLU())->getResult();
        $fes = [];
        $realizations = [];
        $realizationAS = [];
        $idAS = -1;
        $idVP = $idVPFE = null;
        $pattern = [];
        $feEntries = [];
        $vp = [];
        $vpfe = [];
        $maxCountFE = 0;
        foreach($rows as $row) {
            if (!isset($fes[$row['feEntry']])) {
                $fes[$row['feEntry']] = [
                    'entry' => $row['feEntry'],
                    'name' => $row['feName'],
                    'type' => $row['feTypeEntry'],
                    'as' => []
                ];
            }
            $fes[$row['feEntry']]['as'][$row['idAnnotationSet']] = $row['idAnnotationSet'];
            $fe = $row['feName'];
            $feEntry = $row['feEntry'];
            $gf = $row['gfName'] ?: '?';
            $pt = $row['ptName'] ?: '?';
            $it = $row['itEntry'] ?: '?';
            $startChar = $row['startChar'] ?: '0';
            if ($it == 'int_normal') {
                $idRealization = 'id' . md5($feEntry . $gf . $pt);
                $realizations[$feEntry][$gf][$pt] = [$idRealization];
            } else {
                $idRealization = 'id' . md5($feEntry . $row['itName'] . '--');
                $realizations[$feEntry][$row['itName']]['--'] = [$idRealization];
            }
            $realizationAS[$idRealization][] = $row['idAnnotationSet'];
            if ($row['idAnnotationSet'] != $idAS) {
                if ($idAS >= 0) {
                    $vpfe[$idVPFE]['feEntries'] = $feEntries;
                    $vpfe[$idVPFE]['count'] = $vpfe[$idVPFE]['count'] + 1;
                    if (count($pattern) > $maxCountFE) {
                        $maxCountFE = count($pattern);
                    }
                    $vp[$idVPFE][$idVP][] = $idAS;
                    if (count($vp[$idVPFE][$idVP]) == 1) {
                        $patterns[$idVPFE][$idVP] = $pattern;
                    }
                }
                $idVP = 'id';
                $idVPFE = 'id';
                $pattern = [];
                $feEntries = [];
            }
            if ($it == 'int_normal') {
                $pattern[$startChar][$feEntry][$gf][$pt] = $row['idAnnotationSet'];
            } else {
                $pattern['0'][$fe][$row['itName']]['--'][] = $row['idAnnotationSet'];
            }
            $idAS = $row['idAnnotationSet'];
            $feEntries[$feEntry] = $fe;
            $idVPFE = 'id'. md5($idVPFE. $fe);
            $idVP = 'id'. md5($idVP. $fe . $gf . $pt . $it);
        }
        $vpfe[$idVPFE]['feEntries'] = $feEntries;
        $vpfe[$idVPFE]['count'] = $vpfe[$idVPFE]['count'] + 1;
        if (count($pattern) > $maxCountFE) {
            $maxCountFE = count($pattern);
        }
        $vp[$idVPFE][$idVP][] = $idAS;
        if (count($vp[$idVPFE][$idVP]) == 1) {
            $patterns[$idVPFE][$idVP] = $pattern;
        }
        $patternFEAS = [];
        $patternAS = [];
        foreach($vp as $idVPFE => $p) {
            foreach($p as $idVP => $as) {
                $patternAS[$idVP] = $as;
                foreach($as as $a) {
                    $patternFEAS[$idVPFE][] = $a;
                }
            }
        }
        $feAS = [];
        foreach($fes as $feEntry => $fe) {
            foreach($fe['as'] as $as) {
                $feAS[$feEntry][] = $as;
            }
        }
        //mdump($realizations);
        $result = [
            'realizations' => $realizations,
            'realizationAS' => $realizationAS,
            'fes' => $fes,
            'vp' => $vp,
            'vpfe' => $vpfe,
            'maxCountFE' => $maxCountFE,
            'patterns' => $patterns,
            'feAS' => $feAS,
            'patternFEAS' => $patternFEAS,
            'patternAS' => $patternAS
        ];
        return $result;
    }

    public function getSentences() {
        $as = new fnbr\models\ViewAnnotationSet();
        $sentences = $as->listSentencesByAS($this->data->idAS)->asQuery()->getResult();
        $annotation = $as->listFECEByAS($this->data->idAS);
        $result = [];
        foreach ($sentences as $sentence) {
            $node = array();
            $node['idAnnotationSet'] = $sentence['idAnnotationSet'];
            $node['idSentence'] = $sentence['idSentence'];
            if ($annotation[$sentence['idSentence']]) {
                $node['text'] = $this->decorateSentence($sentence['text'], $annotation[$sentence['idSentence']]);
            } else {
                $node['text'] = $sentence['text'];
            }
            $node['status'] = $sentence['annotationStatus'];
            $node['rgbBg'] = $sentence['rgbBg'];
            $result[] = $node;
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
        $result = '';
        foreach($layer as $layerNum => $layerLabels) {
            $i = 0;
            $ni = "";
            $decorated = "";
            $invisible = 'background-color:#FFFFF;color:#FFFFFF;';
            foreach($layerLabels as $label) {
                $style = 'background-color:#' . $label['rgbBg'] . ';color:#' . $label['rgbFg'] . ';';
                $class = 'fe_' . ($label['feEntry'] ?: 'target');
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
            $result .= ($layerNum > 0 ? '<br/>' : '') . utf8_encode($decorated);
        }
        return $result;
    }

}
