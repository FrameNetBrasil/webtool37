<?php

class ReportLemmaService extends MService
{

    public function listLemmas($data, $idLanguage = '')
    {
        $lemma = new fnbr\models\Lemma();
        $filter = (object) ['lu' => $data->lu, 'fe' => $data->fe, 'lemma' => $data->lemma, 'idDomain' => $data->idDomain, 'idLanguage' => $idLanguage];
        $lemmas = $lemma->listByFilter($filter)->asQuery()->getResult(\FETCH_ASSOC);
        $result = array();
        foreach ($lemmas as $row) {
            if (strpos($row['name'], '#') === false) {
                $node = array();
                $node['id'] = 'f' . $row['idLemma'];
                $node['text'] = $row['name'];
                $node['state'] = 'closed';
                $node['entry'] = $row['entry'];
                $result[] = $node;
            }
        }
        return $result;
    }

    public function listLUs($idLemma, $idLanguage)
    {
        $result = array();
        $lu = new fnbr\models\ViewLU();
        $lus = $lu->listByLemma($idLemma, $idLanguage)->asQuery()->chunkResult('idLU', 'name');
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

    public function getFEData($idLemma)
    {
        $lemmaElement = new fnbr\models\LemmaElement();
        $styles = $lemmaElement->getStylesByLemma($idLemma);
        $fes = $lemmaElement->listForReport($idLemma)->asQuery()->getResult();
        $core = [];
        $coreun = [];
        $noncore = [];
        $feByEntry = [];
        foreach ($fes as $fe) {
            $feByEntry[$fe['entry']] = $fe;
        }
        foreach ($fes as $fe) {
            $lemmaElement->getById($fe['idLemmaElement']);
            $relations = $this->getRelationsFE($lemmaElement);
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

    public function getFECoreSet($lemma)
    {
        $feCoreSet = $lemma->listFECoreSet();
        $s = [];
        foreach($feCoreSet as $i => $cs) {
            $s[$i] = "{" . implode(',', $cs) . "}";
        }
        $result = implode(', ', $s);
        return $result;
    }

    public function getRelations($lemma)
    {
        $relations = [];
        $directRelations = $lemma->listDirectRelations();
        foreach($directRelations as $entry => $row) {
            $relations[$entry] = [];
            $i = 0;
            foreach($row as $r) {
                $relations[$entry][$r['idLemma']] = $r['name'];
            }
        }
        $inverseRelations = $lemma->listInverseRelations(); 
        foreach($inverseRelations as $entry => $row) {
            $entry = $entry . '_inv';
            $relations[$entry] = [];
            $i = 0;
            foreach($row as $r) {
                $relations[$entry][$r['idLemma']] = $r['name'];
            }
        }
        ksort($relations);
        return $relations;
    }

    public function getRelationsFE($lemmaElement)
    {
        $relations = [];
        $coreSet = $lemmaElement->listCoreSet()->asQuery()->getResult();
        $excludes = $lemmaElement->listExcludes()->asQuery()->getResult();
        $requires = $lemmaElement->listRequires()->asQuery()->getResult();
        $st = $lemmaElement->listFE2SemanticType()->asQuery()->getResult();
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

    public function getLUs($lemma, $idLanguage)
    {
        $lu = new fnbr\models\ViewLU();
        $lus = $lu->listByLemma($lemma->getIdLemma(), $idLanguage)->asQuery()->chunkResult('idLU', 'name');
        return $lus;
    }

}
