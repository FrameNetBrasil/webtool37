<?php

class ReportFrameService extends MService
{

    public function listFrames($data, $idLanguage = '')
    {
        $frame = new fnbr\models\ViewFrame();
        $filter = (object) ['lu' => $data->lu, 'fe' => $data->fe, 'frame' => $data->frame, 'idDomain' => $data->idDomain, 'idLanguage' => $idLanguage];
        $frames = $frame->listByFilter($filter)->asQuery()->getResult(\FETCH_ASSOC);
        $result = array();
        foreach ($frames as $row) {
            if (strpos($row['name'], '#') === false) {
                $node = array();
                $node['id'] = 'f' . $row['idFrame'];
                $node['text'] = $row['name'];
                $node['state'] = 'closed';
                $node['entry'] = $row['entry'];
                $result[] = $node;
            }
        }
        return $result;
    }

    public function listLUs($idFrame, $idLanguage)
    {
        $result = array();
        $lu = new fnbr\models\ViewLU();
        $lus = $lu->listByFrame($idFrame, $idLanguage)->asQuery()->chunkResult('idLU', 'name');
        foreach ($lus as $idLU => $name) {
            $node = array();
            $node['id'] = 'l' . $idLU;
            $node['text'] = $name;
            $node['state'] = 'open';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function decorate($description, $styles)
    {
        $decorated = "";
        $sentence = utf8_decode($description);
        $decorated = preg_replace_callback(
            "/\#([^\s\.\,\;\?\!]*)/i", 
            function ($matches) use ($styles) {
                $m = substr($matches[0], 1);
                $l = strtolower($m);
                $s = $styles[utf8_encode($l)];
                if ($s) {
                    return "<span class='fe_{$l}'>{$m}</span>";
                }
                foreach ($styles as $s) {
                    $p = strpos(utf8_encode($l), $s['fe']);
                    if ($p === 0) {
                        return "<span class='fe_{$s['fe']}'>{$m}</span>";
                    }
                }
                return $m;
            }, 
            $sentence
        );
        return utf8_encode($decorated);
    }

    public function getFEData($idFrame)
    {
        $frameElement = new fnbr\models\FrameElement();
        $styles = $frameElement->getStylesByFrame($idFrame);
        $fes = $frameElement->listForReport($idFrame)->asQuery()->getResult();
        $core = [];
        $coreun = [];
        $noncore = [];
        $feByEntry = [];
        foreach ($fes as $fe) {
            $feByEntry[$fe['entry']] = $fe;
        }
        foreach ($fes as $fe) {
            $frameElement->getById($fe['idFrameElement']);
            $relations = $this->getRelationsFE($frameElement);
            $fe['relations'] = [];
            foreach($relations as $rel => $aRelation) {
                foreach($aRelation as $relation) {
                    $fe['relations'][] = [$rel, $feByEntry[$relation]['name'] ?: $relation];
                }
            }
            $fe['lower'] = strtolower($fe['name']);
            $fe['description'] = $this->decorate($fe['description'], $styles);
            if ($fe['coreType'] == 'cty_core') {
                $core[] = $fe;
            } else if ($fe['coreType'] == 'cty_core-unexpressed') {
                $coreun[] = $fe;
            } else {
                $noncore[] = $fe;
            }
        }
        return [
            'styles' => $styles,
            'core' => $core,
            'core_unexpressed' => $coreun,
            'noncore' => $noncore
        ];
    }

    public function getFECoreSet($frame)
    {
        $feCoreSet = $frame->listFECoreSet();
        $s = [];
        foreach($feCoreSet as $i => $cs) {
            $s[$i] = "{" . implode(',', $cs) . "}";
        }
        $result = implode(', ', $s);
        return $result;
    }

    public function getRelations($frame)
    {
        $relations = [];
        $directRelations = $frame->listDirectRelations();
        foreach($directRelations as $entry => $row) {
            $relations[$entry] = [];
            $i = 0;
            foreach($row as $r) {
                $relations[$entry][$r['idFrame']] = $r['name'];
            }
        }
        $inverseRelations = $frame->listInverseRelations(); 
        foreach($inverseRelations as $entry => $row) {
            $entry = $entry . '_inv';
            $relations[$entry] = [];
            $i = 0;
            foreach($row as $r) {
                $relations[$entry][$r['idFrame']] = $r['name'];
            }
        }
        ksort($relations);
        return $relations;
    }

    public function getRelationsFE($frameElement)
    {
        $relations = [];
        $coreSet = $frameElement->listCoreSet()->asQuery()->getResult();
        $excludes = $frameElement->listExcludes()->asQuery()->getResult();
        $requires = $frameElement->listRequires()->asQuery()->getResult();
        $st = $frameElement->listFE2SemanticType()->asQuery()->getResult();
        foreach($requires as $row) {
            $relations['requires'][] = $row['entry'];
        }
        foreach($excludes as $row) {
            $relations['excludes'][] = $row['entry'];
        }
        foreach($st as $row) {
            $relations['semantic_type'][] = $row['name'];
        }

        return $relations;
    }

    public function getLUs($frame, $idLanguage)
    {
        $lu = new fnbr\models\ViewLU();
        $lus = $lu->listByFrame($frame->getIdFrame(), $idLanguage)->asQuery()->chunkResult('idLU', 'name');
        return $lus;
    }

}
