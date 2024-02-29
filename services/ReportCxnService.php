<?php



class ReportCxnService extends MService
{

    public function listCxns($data, $idLanguage = '')
    {
        $cxn = new fnbr\models\Construction();
        $filter = (object) ['ce' => $data->ce, 'cxn' => $data->cxn, 'idLanguage' => $idLanguage];
        $cxns = $cxn->listByFilter($filter)->asQuery()->getResult();
        $result = array();
        foreach ($cxns as $row) {
            if (strpos($row['name'], '#') === false) {
                $node = array();
                $node['id'] = 'c' . $row['idConstruction'];
                $node['text'] = $row['name'];
                $node['state'] = 'closed';
                $node['entry'] = $row['entry'];
                $result[] = $node;
            }
        }
        return $result;
    }

    public function listCEs($idCxn, $idLanguage)
    {
        $result = array();
        $vce = new fnbr\models\ViewConstructionElement();
        $ces = $vce->listByFrame($idCxn, $idLanguage)->asQuery()->chunkResult('idConstructionElement', 'name');
        foreach ($ces as $idConstructionElement => $name) {
            $node = array();
            $node['id'] = 'e' . $idConstructionElement;
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
                    return "<span class='ce_{$l}'>{$m}</span>";
                }
                foreach ($styles as $s) {
                    $p = strpos(utf8_encode($l), $s['ce']);
                    if ($p === 0) {
                        return "<span class='ce_{$s['ce']}'>{$m}</span>";
                    }
                }
                return $m;
            }, 
            $sentence
        );
        return utf8_encode($decorated);
    }

    public function getCEData($idCxn)
    {
        $constructionElement = new fnbr\models\ConstructionElement();
        $styles = $constructionElement->getStylesByCxn($idCxn);
        $ces = $constructionElement->listForReport($idCxn)->asQuery()->getResult();
        $core = [];
        foreach ($ces as $ce) {
            $ce['lower'] = strtolower($ce['name']);
            $ce['description'] = $this->decorate($ce['description'], $styles);
            $element[] = $ce;
        }
        return [
            'styles' => $styles,
            'element' => $element,
        ];
    }

    public function getRelations($construction)
    {
        $relations = [];
        $directRelations = $construction->listDirectRelations();
        foreach($directRelations as $entry => $row) {
            $relations[$entry] = '';
            $i = 0;
            foreach($row as $r) {
                $relations[$entry] .= ($i++ > 0 ? ', ' : '') . $r['name'];
            }
        }
        $inverseRelations = $construction->listInverseRelations();
        foreach($inverseRelations as $entry => $row) {
            $entry = $entry . '_inv';
            $relations[$entry] = '';
            $i = 0;
            foreach($row as $r) {
                $relations[$entry] .= ($i++ > 0 ? ', ' : '') . $r['name'];
            }
        }
//        $evokesRelations = $construction->listEvokesRelations();
//        foreach($evokesRelations as $entry => $row) {
//            $relations[$entry] = '';
//            $i = 0;
//            foreach($row as $r) {
//                $relations[$entry] .= ($i++ > 0 ? ', ' : '') . $r['name'];
//            }
//        }
        ksort($relations);
        return $relations;
    }

    public function listConstraintsEvokesCX($construction, $idLanguage)
    {
        $result = [];
        $service = Manager::getAppService('StructureConstraintInstance');
        $cns = $service->listConstraintsCX($construction->getIdConstruction());
        $evokesRelations = $construction->listEvokesRelations();
        foreach($evokesRelations as $entry => $row) {
            $result[$entry] = '';
            $i = 0;
            foreach($row as $r) {
                $result[$entry] .= ($i++ > 0 ? ', ' : '') . $r['name'];
            }
        }
        ksort($result);
        return $result;
    }

    public function listConstraintsEvokesCE($ceData, $idLanguage)
    {
        $service = Manager::getAppService('StructureConstraintInstance');
        $result = [];
        foreach($ceData['element'] as $ce) {
            $idConstructionElement = $ce['idConstructionElement'];
            $ce = new fnbr\models\ConstructionElement($idConstructionElement);
            $result[$idConstructionElement] = [];

            $directRelations = $ce->listDirectRelations();
            foreach($directRelations as $entry => $row) {
                $result[$idConstructionElement][$entry] = '';
                $i = 0;
                foreach($row as $r) {
                    $result[$idConstructionElement][$entry] .= ($i++ > 0 ? ', ' : '') . $r['name'];
                }
            }
            $evokesRelations = $ce->listEvokesRelations();
            foreach($evokesRelations as $entry => $row) {
                $result[$idConstructionElement][$entry] = '';
                $i = 0;
                foreach($row as $r) {
                    $result[$idConstructionElement][$entry] .= ($i++ > 0 ? ', ' : '') . $r['name'];
                }
            }

            $constraints = $ce->listConstraints();
            mdump($constraints);
            foreach ($constraints as $cn) {
                $result[$idConstructionElement][$cn['relationType']] = substr($cn['name'],4);
            }
        }
        mdump($result);
        return $result;
    }

    public function listConstraintsCN($idConstraint, $idLanguage)
    {
        $service = Manager::getAppService('StructureConstraintInstance');
        $result = $service->listConstraintsCN($idConstraint);
        return $result;
    }

    public function listConstraintsCNCN($idConstraint, $idLanguage)
    {
        $service = Manager::getAppService('StructureConstraintInstance');
        $result = $service->listConstraintsCNCN($idConstraint);
        return $result;
    }

    public function listConstraintsCX($idConstruction, $idLanguage)
    {
        $service = Manager::getAppService('StructureConstraintInstance');
        $result = $service->listConstraintsCX($idConstruction);
        return $result;
    }

    public function listEvokesCX($idConstruction, $idLanguage)
    {
        $service = Manager::getAppService('StructureConstraintInstance');
        $result = $service->listEvokesCX($idConstruction);
        return $result;
    }

    public function listInheritanceCX($idConstruction, $idLanguage)
    {
        $service = Manager::getAppService('StructureConstraintInstance');
        $result = $service->listInheritanceCX($idConstruction);
        return $result;
    }

    public function listConstraintsCE($idConstructionElement)
    {
        $service = Manager::getAppService('StructureConstraintInstance');
        $result = $service->listConstraintsCE($idConstructionElement);
        return $result;
    }

    public function listEvokesCE($idConstructionElement)
    {
        $service = Manager::getAppService('StructureConstraintInstance');
        $result = $service->listEvokesCE($idConstructionElement);
        return $result;
    }

    public function listInheritanceCE($idConstructionElement)
    {
        $service = Manager::getAppService('StructureConstraintInstance');
        $result = $service->listInheritanceCE($idConstructionElement);
        return $result;
    }


}
