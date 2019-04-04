<?php

use fnbr\models\Base;

class StructureCxnService extends MService
{
    /*
     * CRUD
     */

    public function listCxns($data, $idLanguage = '')
    {
        $cxn = new fnbr\models\ViewConstruction();
        $filter = (object)['idDomain' => $data->idDomain, 'ce' => $data->ce, 'cxn' => $data->cxn, 'active' => $data->active, 'idLanguage' => $idLanguage];
        $cxns = $cxn->listByFilter($filter)->asQuery()->getResult(\FETCH_ASSOC);
        $result = array();
        foreach ($cxns as $row) {
            $node = array();
            $node['id'] = 'c' . $row['idConstruction'];
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listCxnLanguage($data, $idLanguageFilter = '')
    {
        $listLanguages = Base::languagesDescription();
        $cxn = new fnbr\models\ViewConstruction();
        $filter = (object)['idDomain' => $data->idDomain, 'ce' => $data->ce, 'cxn' => $data->cxn, 'active' => $data->active, 'idLanguage' => $idLanguage];
        $languages = $cxn->listByLanguageFilter($filter)->asQuery()->treeResult('idLanguage', 'idConstruction,name,entry');
        $result = [];
        if ($idLanguageFilter == '') {
            foreach ($languages as $idLanguage => $language) {
                $nodes = [];
                foreach ($language as $row) {
                    $node = [];
                    $node['id'] = 'c' . $row['idConstruction'];
                    $node['text'] = $row['name'];
                    $node['state'] = 'closed';
                    $node['entry'] = $row['entry'];
                    $nodes[] = $node;
                }
                $lang = $listLanguages[$idLanguage];
                $flag = 'fnbrFlag' . $lang[0]['description'];
                $langNode = [
                    'id' => 'l' . $idLanguage,
                    'state' => 'closed',
                    'text' => $lang[0]['description'],
                    'iconCls' => "icon-blank {$flag}",
                    'children' => $nodes
                ];
                $result[] = $langNode;
            }
        } else {
            foreach ($languages[$idLanguageFilter] as $row) {
                $node = [];
                $node['id'] = 'c' . $row['idConstruction'];
                $node['text'] = $row['name'];
                $node['state'] = 'closed';
                $node['entry'] = $row['entry'];
                $result[] = $node;
            }
            $result = json_encode($result);
        }
        return $result;
    }

    public function listCxnLanguageEntity($data, $idLanguageFilter = '')
    {
        $listLanguages = Base::languagesDescription();
        $cxn = new fnbr\models\ViewConstruction();
        $filter = (object)['idDomain' => $data->idDomain, 'ce' => $data->ce, 'cxn' => $data->cxn, 'active' => $data->active, 'idLanguage' => $idLanguage];
        $languages = $cxn->listByLanguageFilter($filter)->asQuery()->treeResult('idLanguage', 'idConstruction,name,entry,idEntity');
        $result = [];
        if ($idLanguageFilter == '') {
            foreach ($languages as $idLanguage => $language) {
                $nodes = [];
                foreach ($language as $row) {
                    $node = [];
                    $node['id'] = 'c' . $row['idEntity'];
                    $node['text'] = $row['name'];
                    $node['state'] = 'closed';
                    $node['entry'] = $row['entry'];
                    $nodes[] = $node;
                }
                $lang = $listLanguages[$idLanguage];
                $flag = 'fnbrFlag' . $lang[0]['description'];
                $langNode = [
                    'id' => 'l' . $idLanguage,
                    'state' => 'closed',
                    'text' => $lang[0]['description'],
                    'iconCls' => "icon-blank {$flag}",
                    'children' => $nodes
                ];
                $result[] = $langNode;
            }
        } else {
            foreach ($languages[$idLanguageFilter] as $row) {
                $node = [];
                $node['id'] = 'c' . $row['idEntity'];
                $node['text'] = $row['name'];
                $node['state'] = 'closed';
                $node['entry'] = $row['entry'];
                $result[] = $node;
            }
            $result = json_encode($result);
        }
        return $result;
    }

    public function listCEs($idCxn, $idLanguage)
    {
        $result = array();
        $cxn = new fnbr\models\Construction($idCxn);
        $ces = $cxn->listCE()->asQuery()->getResult();
        foreach ($ces as $ce) {
            $node = array();
            $node['id'] = 'e' . $ce['idConstructionElement'];
            $style = 'background-color:#' . $ce['rgbBg'] . ';color:#' . $ce['rgbFg'] . ';';
            $node['text'] = "<span style='{$style}'>" . $ce['name'] . "</span>";
            $node['state'] = 'closed';//'open';
            $node['entry'] = $ce['entry'];
            //$node['iconCls'] = 'icon-blank fa-icon fa fa-circle';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function deleteCxn($idCxn)
    {
        mdump('deleteCxn ' . $idCxn);
        $cxn = new fnbr\models\Construction($idCxn);
        $transaction = $cxn->beginTransaction();
        try {
            $cxnElement = new fnbr\models\ConstructionElement();
            $filter = (object)['idConstruction' => $idCxn];
            $ces = $cxnElement->listByFilter($filter)->asQuery()->getResult();
            foreach ($ces as $ce) {
                $cxnElement->getById($ce['idConstructionElement']);
                $cxnElement->delete();
            }
            $cxn->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception($e->getMessage());
        }
    }

    public function deleteCxnElement($idCE)
    {
        mdump('deleteCE ' . $idCE);
        $ce = new fnbr\models\ConstructionElement($idCE);
        $transaction = $ce->beginTransaction();
        try {
            $ce->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception($e->getMessage());
        }
    }


    /*
     * Annotation
     */

    public function listSubCorpus($idLU)
    {
        $sc = new fnbr\models\ViewSubCorpusLU();
        $scs = $sc->listByLU($idLU)->asQuery()->getResult();
        foreach ($scs as $sc) {
            $node = array();
            $node['id'] = 's' . $sc['idSubCorpus'];
            $node['text'] = $sc['name'] . ' [' . $sc['quant'] . ']';
            $node['state'] = 'open';
            $result[] = $node;
        }
        $this->data->result = $result;
        return json_encode($result);
    }

    public function getSubCorpusTitle($idSubCorpus, $idLanguage, $isCxn)
    {
        $sc = $isCxn ? new fnbr\models\ViewSubCorpusCxn() : new fnbr\models\ViewSubCorpusLU();
        $title = $sc->getTitle($idSubCorpus, $idLanguage);
        return $title;
    }

    public function getDocumentTitle($idDocument, $idLanguage)
    {
        $doc = new fnbr\models\Document();
        $filter = (object)['idDocument' => $idDocument];
        $result = $doc->listByFilter($filter)->asQuery()->getResult();
        return 'Document:' . $result[1];
    }

    public function decorateSentence($sentence, $labels)
    {
        $decorated = "";
        $ni = "";
        $i = 0;
        $sentence = utf8_decode($sentence);
        foreach ($labels as $label) {
            $style = 'background-color:#' . $label[3] . ';color:#' . $label[2] . ';';
            if ($label[0] >= 0) {
                $decorated .= substr($sentence, $i, $label[0] - $i);
                $decorated .= "<span style='{$style}'>" . substr($sentence, $label[0], $label[1] - $label[0] + 1) . "</span>";
                $i = $label[1] + 1;
            } else { // null instantiation
                $ni .= "<span style='{$style}'>" . $label[4] . "</span> " . $decorated;
            }
        }
        $decorated = utf8_encode($ni . $decorated . substr($sentence, $i));
        return $decorated;
    }

    public function listAnnotationSet($idSubCorpus)
    {
        $as = new fnbr\models\ViewAnnotationSet();
        $sentences = $as->listBySubCorpus($idSubCorpus)->asQuery()->getResult();
        $annotation = $as->listFECEBySubCorpus($idSubCorpus);
        $result = array();
        foreach ($sentences as $sentence) {
            $node = array();
            $node['idAnnotationSet'] = $sentence[0];
            $node['idSentence'] = $sentence[1];
            if ($annotation[$sentence[1]]) {
                $node['text'] = $this->decorateSentence($sentence[2], $annotation[$sentence[1]]);
            } else {
                $node['text'] = $sentence[2];
            }
            $node['status'] = $sentence[3];
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function getLayers($params, $idLanguage)
    {
        $idSentence = $params->idSentence;
        $idAnnotationSet = $params->idAnnotationSet;

        $layers = array(
            "words" => NULL,
            "frozenColumns" => NULL,
            "columns" => NULL,
            "labels" => NULL,
            "layers" => NULL,
            "labelTypes" => NULL,
            "nis" => NULL,
        );

        $as = new fnbr\models\AnnotationSet($idAnnotationSet);

        // get words/chars
        $wordsChars = $as->getWordsChars($idSentence);
        $words = $wordsChars->words;
        $chars = $wordsChars->chars;

        $result = array();
        foreach ($words as $i => $word) {
//            $field = 'wf' . $word[0];
            $fieldData = $i; //$field . '_data';
            $result[$fieldData]->word = $word[1];
            $result[$fieldData]->startChar = $word[2];
            $result[$fieldData]->endChar = $word[3];
        }
        $layers['words'] = json_encode($result);

        $result = array();
        foreach ($chars as $i => $char) {
            $fieldData = 'wf' . $i; //$field . '_data';
            $result[$fieldData]->order = $char[0];
            $result[$fieldData]->char = $char[1];
            $result[$fieldData]->word = $char[2];
        }
        $layers['chars'] = json_encode($result);

        // get hiddenColumns/frozenColumns/Columns using $words
        $frozenColumns[] = array(
            "field" => "layer",
            "width" => '60',
            "title" => "S_" . $idSentence,
            "formatter" => "annotation.cellLayerFormatter",
            "styler" => "annotation.cellStyler"
        );
        $columns[] = array("field" => "idAnnotationSet", "hidden" => 'true', "formatter" => "", "styler" => "");
        $columns[] = array("field" => "idLayerType", "hidden" => 'true', "formatter" => "", "styler" => "");
        $columns[] = array("field" => "idLayer", "hidden" => 'true', "formatter" => "", "styler" => "");
        $columns[] = array(
            "hidden" => 'false',
            "field" => "ni",
            "width" => "90",
            "resizable" => "true",
            "title" => "NI",
            "formatter" => "annotation.cellNIFormatter",
            "styler" => ""
        );

        foreach ($chars as $i => $char) {
            $width = 15;
            $columns[] = array(
                "hidden" => 'false',
                "field" => 'wf' . $i,
                "width" => 13,
                "resizable" => "false",
                "title" => $char[1],
                "formatter" => "annotation.cellFormatter",
                "styler" => "annotation.cellStyler"
            );
        }
        $layers['columns'] = $columns;
        $layers['frozenColumns'] = $frozenColumns;

        // get Layers
        $result = array();
        $asLayers = $as->getLayers($idSentence);
        foreach ($asLayers as $row) {
            $result[$row[0]] = ['idAnnotationSet' => $row[2], 'nameLayer' => $row[1], 'currentLabel' => '0', 'currentLabelPos' => 0];
        }
        $layers['layers'] = json_encode($result);

        // get AnnotationSets
        $result = array();
        $annotationSets = $as->getAnnotationSets($idSentence);
        foreach ($annotationSets as $row) {
            $result[$row[0]] = ['idAnnotationSet' => $row[0], 'name' => $row[1], 'show' => true];
        }
        $layers['annotationSets'] = json_encode($result);

        // get Labels
        $result = array();
        $asLabels = $as->getLabels($idSentence);
        foreach ($asLabels as $row) {
            $result[$row[0]][$row[1]] = ['idLabelType' => $row[2]];
        }
        $layers['labels'] = json_encode($result);

        // get LabelTypes
        $result = array();
        // GL-GF
        $queryLabelType = $as->getLabelTypesGLGF($idSentence)->asQuery();
        $rows = $queryLabelType->getResult();
        list($idLayer, $idLabelType, $labelType, $idColor, $coreType) = $queryLabelType->getColumnNumbers('idLayer,idLabelType,labelType,idColor,coreType');
        foreach ($rows as $row) {
            $result[$row[$idLayer]][$row[$idLabelType]] = array('label' => $row[$labelType], 'idColor' => $row[$idColor], 'coreType' => $row[$coreType]);
        }
        // GL
        $queryLabelType = $as->getLabelTypesGL($idSentence)->asQuery();
        $rows = $queryLabelType->getResult();
        list($idLayer, $idLabelType, $labelType, $idColor, $coreType) = $queryLabelType->getColumnNumbers('idLayer,idLabelType,labelType,idColor,coreType');
        foreach ($rows as $row) {
            $result[$row[$idLayer]][$row[$idLabelType]] = array('label' => $row[$labelType], 'idColor' => $row[$idColor], 'coreType' => $row[$coreType]);
        }
        // FE
        $queryLabelType = $as->getLabelTypesFE($idSentence);
        $rows = $queryLabelType->getResult();
        list($idLayer, $idLabelType, $labelType, $idColor, $coreType) = $queryLabelType->getColumnNumbers('idLayer,idLabelType,labelType,idColor,coreType');
        foreach ($rows as $row) {
            $result[$row[$idLayer]][$row[$idLabelType]] = array('label' => $row[$labelType], 'idColor' => $row[$idColor], 'coreType' => $row[$coreType]);
        }
        // CE
        $queryLabelType = $as->getLabelTypesCE($idSentence);
        $rows = $queryLabelType->getResult();
        list($idLayer, $idLabelType, $labelType, $idColor, $coreType) = $queryLabelType->getColumnNumbers('idLayer,idLabelType,labelType,idColor,coreType');
        foreach ($rows as $row) {
            $result[$row[$idLayer]][$row[$idLabelType]] = array('label' => $row[$labelType], 'idColor' => $row[$idColor], 'coreType' => $row[$coreType]);
        }
        $layers['labelTypes'] = json_encode($result);

        // get NIs
        //$niFields = array();
        $result = array();
        $queryNI = $as->getNI($idSentence, $idLanguage);
        $rows = $queryNI->getResult();
        list($idLayer, $idLabel, $feName, $idInstantiationType, $instantiationType, $idColor, $idLabelType) = $queryNI->getColumnNumbers('idLayer,idLabel,feName,idInstantiationType,instantiationType,idColor,idLabelType');
        foreach ($rows as $row) {
            $result[$row[$idLayer]][$row[$idLabelType]] = array(
                'fe' => $row[$feName],
                'idInstantiationType' => $row[$idInstantiationType],
                'label' => $row[$instantiationType],
                //'idSentenceWord' => $row[$idSentenceWord],
                'idColor' => $row[$idColor]
            );
            //$niFields[$row[$idLayer]] = 'wf' . $row[$idSentenceWord];
        }
        $layers['nis'] = (count($result) > 0) ? json_encode($result) : "{}";
        //$layers['niFields'] = json_encode($niFields);

        return $layers;
    }

    public function getLayersData($params, $idLanguage)
    {
        $idSentence = $params->idSentence;
        $idAnnotationSet = $params->idAnnotationSet;

        $as = new fnbr\models\AnnotationSet($idAnnotationSet);
        $result = array();
        $queryLayersData = $as->getLayersData($idSentence);
        $unorderedRows = $queryLayersData->getResult();
        list($idAnnotationSet, $idLayerType, $idLayer, $nameLayer, $startChar, $endChar, $idLabelType, $idLabel) = $queryLayersData->getColumnNumbers('idAnnotationSet,idLayerType,idLayer,layer,startChar,endChar,idLabelType,idLabel');

        // get the annotationsets
        $aSet = array();
        foreach ($unorderedRows as $row) {
            $aSet[$row[$idAnnotationSet]][] = $row;
        }
        // reorder rows to put Target on top of each annotatioset
        $rows = array();
        $idHeaderLayer = -1;
        foreach ($aSet as $asRows) {
            $hasTarget = false;
            foreach ($asRows as $row) {
                if ($row[$nameLayer] == 'Target') {
                    $row[$idLayerType] = 0;
                    $rows[] = $row;
                    $hasTarget = true;
                }
            }
            if ($hasTarget) {
                foreach ($asRows as $row) {
                    if ($row[$nameLayer] != 'Target') {
                        $rows[] = $row;
                    }
                }
            } else {
                $headerLayer = $asRows[0];
                $headerLayer[$nameLayer] = 'x';
                $headerLayer[$startChar] = -1;
                $headerLayer[$idLayerType] = 0;
                $headerLayer[$idLayer] = $idHeaderLayer--;
                $rows[] = $headerLayer;
                foreach ($asRows as $row) {
                    $rows[] = $row;
                }
            }
        }

        $layersToShow = Manager::getSession()->mfnLayers;
        if ($layersToShow == '') {
            $user = Manager::getLogin()->getUser();
            $layersToShow = Manager::getSession()->mfnLayers = $user->getConfigData('fnbrLayers');
        }
        $wordsChars = $as->getWordsChars($idSentence);
        $chars = $wordsChars->chars;

        $line = [];
        $idLayerRef = -1;
        foreach ($rows as $row) {
            $idLT = $row[$idLayerType];
            if ($idLT != 0) {
                if (!in_array($idLT, $layersToShow)) {
                    //  mdump('*'.$idLayerType);
                    continue;
                }
            }
            if ($row[$idLayer] != $idLayerRef) {
                $line[$row[$idLayer]] = new \stdclass();
                $line[$row[$idLayer]]->idAnnotationSet = $row[$idAnnotationSet];
                $line[$row[$idLayer]]->idLayerType = $row[$idLayerType];
                $line[$row[$idLayer]]->idLayer = $row[$idLayer];
                $line[$row[$idLayer]]->layer = ($row[$idLayerType] == 0) ? 'AS_' . $row[$idAnnotationSet] : $row[$nameLayer];
                $line[$row[$idLayer]]->ni = '';
                $line[$row[$idLayer]]->show = true;
                $idLayerRef = $row[$idLayer];
            }
            if ($row[$startChar] > -1) {
                $posChar = $row[$startChar];
                $i = 0;
                while ($posChar <= $row[$endChar]) {
                    $field = 'wf' . $posChar;
                    if ($row[$nameLayer] == 'Target') {
                        $line[$row[$idLayer]]->$field = $chars[$posChar][1];
                    } else {
                        $line[$row[$idLayer]]->$field = $row[$idLabelType];
                    }
                    $posChar += 1;
                }
            }
        }
        // last, create data
        $data = array();
        foreach ($line as $layer) {
            $data[] = $layer;
        }
        return json_encode($data);
        //return $data;
    }

    public function putLayers($layers)
    {
        $annotationSet = new fnbr\models\AnnotationSet();
        $annotationSet->putLayers($layers);
    }

    public function addFELayer($idAnnotationSet)
    {
        $annotationSet = new fnbr\models\AnnotationSet($idAnnotationSet);
        $annotationSet->addFELayer();
        $this->render();
    }

    public function delFELayer($idAnnotationSet)
    {
        $annotationSet = new fnbr\models\AnnotationSet($idAnnotationSet);
        $annotationSet->delFELayer();
        $this->render();
    }

    public function listCnx($cnx = '', $idLanguage = '')
    {
        $construction = new fnbr\models\Construction();
        $filter = (object)['cnx' => $cnx, 'idLanguage' => $idLanguage];
        $constructions = $construction->listByFilter($filter)->asQuery()->chunkResult('idConstruction', 'name');
        $result = array();
        foreach ($constructions as $idCnx => $name) {
            $node = array();
            $node['id'] = 'c' . $idCnx;
            $node['text'] = $name;
            $node['state'] = 'closed';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function listSubCorpusCnx($idCnx)
    {
        $sc = new fnbr\models\SubCorpus();
        $scs = $sc->listByCnx($idCnx)->asQuery()->getResult();
        foreach ($scs as $sc) {
            $node = array();
            $node['id'] = 's' . $sc[0];
            $node['text'] = $sc[1] . ' [' . $sc[2] . ']';
            $node['state'] = 'open';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function headerMenu($wordform)
    {
        $wf = new fnbr\models\WordForm();
        $lus = $wf->listLUByWordForm($wordform);
        return json_encode($lus);
    }

    public function addManualSubcorpus($data)
    {
        $sc = new fnbr\models\SubCorpus();
        if ($data->idLU != '') {
            $sc->addManualSubcorpusLU($data);
        } else {
            $sc->addManualSubcorpusCnx($data);
        }
    }

    public function cnxGridData()
    {
        $cnx = new fnbr\models\Construction();
        $criteria = $cnx->listAll();
        $data = $cnx->gridDataAsJSON($criteria);
        return $data;
    }

    public function listCorpus($corpus = '', $idLanguage = '')
    {
        $corpus = new fnbr\models\Corpus();
        $filter = (object)['corpus' => $corpus, 'idLanguage' => $idLanguage];
        $corpora = $corpus->listByFilter($filter)->asQuery()->chunkResult('idCorpus', 'name');
        $result = array();
        foreach ($corpora as $idCorpus => $name) {
            $node = array();
            $node['id'] = 'c' . $idCorpus;
            $node['text'] = $name;
            $node['state'] = 'closed';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function listCorpusDocument($idCorpus)
    {
        $doc = new fnbr\models\Document();
        $docs = $doc->listByCorpus($idCorpus)->asQuery()->getResult();
        foreach ($docs as $doc) {
            if ($doc[0]) {
                $node = array();
                $node['id'] = 'd' . $doc[0];
                $node['text'] = $doc[1] . ' [' . $doc[2] . ']';
                $node['state'] = 'open';
                $result[] = $node;
            }
        }
        return json_encode($result);
    }

    /*
     * Constraints
     */

    public function listOptionsNumber()
    {
        $ti = new fnbr\models\TypeInstance();
        $result = $ti->listUDNumber()->chunkResult('idEntity', 'info');
        return $result;
    }

    public function listOptionsSTLU()
    {
        $st = new fnbr\models\SemanticType();
        $result = $st->listSTLUforConstraint()->chunkResult('idEntity', 'name');
        mdump($result);
        return $result;
    }

    public function listOptionsUDRelation()
    {
        $ud = new fnbr\models\UDRelation();
        $result = $ud->listForLookupEntity('*')->chunkResult('idEntity', 'info');
        mdump($result);
        return $result;
    }

    public function listCEsConstraintsEvokesCX($idCxn, $idLanguage)
    {
        $result = [];
        $ces = json_decode($this->listCEs($idCxn, $idLanguage));
        foreach ($ces as $ce) {
            $result[] = $ce;
        }
        $cxs = json_decode($this->listConstraintsCX($idCxn, $idLanguage));
        foreach ($cxs as $cx) {
            $result[] = $cx;
        }
        $evokes = json_decode($this->listEvokesCX($idCxn, $idLanguage));
        foreach ($evokes as $evoke) {
            $result[] = $evoke;
        }
        return json_encode($result);
    }

    public function listConstraintsEvokesCE($idConstructionElement, $idLanguage)
    {
        $result = [];
        $cns = json_decode($this->listConstraintsCE($idConstructionElement));
        foreach ($cns as $cn) {
            $result[] = $cn;
        }
        $evokes = json_decode($this->listEvokesCE($idConstructionElement));
        foreach ($evokes as $evoke) {
            $result[] = $evoke;
        }
        return json_encode($result);
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

    public function treeCX($idConstruction, $idLanguage = '')
    {
        $children = [];
        $ces = $this->listCEs($idConstruction, $idLanguage);
        foreach ($ces as $ce) {
            $children[] = $ce;
        }
        $cxs = $this->listConstraintsCX($idConstruction, $idLanguage);
        foreach ($cxs as $cx) {
            $children[] = $cx;
        }
        mdump($children);
        $cxn = new fnbr\models\ViewConstruction();
        $filter = (object)['idConstruction' => $idConstruction, 'idLanguage' => $idLanguage];
        $cxns = $cxn->listByFilter($filter)->asQuery()->getResult(\FETCH_ASSOC);
        $result = array();
        foreach ($cxns as $row) {
            $node = array();
            $node['id'] = 'c' . $row['idConstruction'];
            $node['text'] = $row['name'];
            $node['state'] = 'open';
            $node['entry'] = $row['entry'];
            $node['children'] = $children;
            $result[] = $node;
        }
        return $result;

    }

    public function addConstraintsCX($data)
    {
        try {
            $transaction = Manager::getDatabase(Manager::getConf('fnbr.db'))->beginTransaction();
            if (($data->idEntityA1 != '') && ($data->idEntityA2 != '') && ($data->relation != '')) {
                $constraint = Base::createEntity('CN', 'con');
                Base::createConstraintInstance($constraint->getIdEntity(), $data->relation, $data->idEntityA1, $data->idEntityA2);
                $constraint2 = Base::createEntity('CN', 'con');
                $cxn = new fnbr\models\Construction($data->idConstruction);
                Base::createConstraintInstance($constraint2->getIdEntity(), 'con_constraint', $cxn->getIdEntity(), $constraint->getIdEntity());
            }
            if (($data->idEntityC1 != '') && ($data->idEntityC2 != '') && ($data->constraint != '')) {
                $constraint = Base::createEntity('CN', 'con');
                Base::createConstraintInstance($constraint->getIdEntity(), $data->constraint, $data->idEntityC1, $data->idEntityC2);
                $constraint2 = Base::createEntity('CN', 'con');
                $cxn = new fnbr\models\Construction($data->idConstruction);
                Base::createConstraintInstance($constraint2->getIdEntity(), 'con_constraint', $cxn->getIdEntity(), $constraint->getIdEntity());
            }
            if ($data->idEntityCE != '') {
                $constraint = Base::createEntity('CN', 'con');
                $frame = new fnbr\models\Frame($data->idFrame);
                Base::createConstraintInstance($constraint->getIdEntity(), $data->relationCEFrame, $data->idEntityCE, $frame->getIdEntity());
                $constraint2 = Base::createEntity('CN', 'con');
                $cxn = new fnbr\models\Construction($data->idConstruction);
                Base::createConstraintInstance($constraint2->getIdEntity(), 'con_constraint', $cxn->getIdEntity(), $constraint->getIdEntity());
            }
            if ($data->idConcept != '') {
                $cxn = new fnbr\models\Construction($data->idConstruction);
                $concept = new fnbr\models\Concept($data->idConcept);
                $conceptType = new fnbr\models\TypeInstance($data->idConceptType);
                Base::createEntityRelation($cxn->getIdEntity(), 'rel_evokes', $concept->getIdEntity(), $conceptType->getIdEntity());
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception($e->getMessage());
        }
    }

    public function addConstraintsCE($data)
    {
        try {
            $transaction = Manager::getDatabase(Manager::getConf('fnbr.db'))->beginTransaction();
            if ($data->idConstruction != '') {
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                $cxn = new fnbr\models\Construction($data->idConstruction);
                // constraint 'cxn' CE-CXN
                $constraintCxn = Base::createEntity('CN', 'con');
                Base::createConstraintInstance($constraintCxn->getIdEntity(), 'con_cxn', $ce->getIdEntity(), $cxn->getIdEntity());
                // constraints 'ele' Constraint-CE
                $ces = $cxn->listCE()->asQuery()->getResult();
                foreach ($ces as $ce) {
                    $constraintEle = Base::createEntity('CN', 'con');
                    Base::createConstraintInstance($constraintEle->getIdEntity(), 'con_element', $constraintCxn->getIdEntity(), $ce['idEntity']);
                }
            }
            if ($data->idFrame != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                $frame = new fnbr\models\Frame($data->idFrame);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_frame', $ce->getIdEntity(), $frame->getIdEntity());
            }
            if ($data->idFrameFamily != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                $frame = new fnbr\models\Frame($data->idFrameFamily);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_framefamily', $ce->getIdEntity(), $frame->getIdEntity());
            }
            /*
            if ($data->idWordform != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                $wf = new fnbr\models\Wordform($data->idWordform);
                Base::createEntityRelation($constraint->getIdEntity(), 'con_wordform', $ce->getIdEntity(), $wf->getIdEntity());
            }
            */
            if ($data->idLexeme != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                $lexeme = new fnbr\models\Lexeme($data->idLexeme);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_lexeme', $ce->getIdEntity(), $lexeme->getIdEntity());
            }
            if ($data->idLemma != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                $lemma = new fnbr\models\Lemma($data->idLemma);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_lemma', $ce->getIdEntity(), $lemma->getIdEntity());
            }
            if ($data->idLU != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                $lu = new fnbr\models\LU($data->idLU);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_lu', $ce->getIdEntity(), $lu->getIdEntity());
            }
            if ($data->idConstructionBefore != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                $ceBefore = new fnbr\models\ConstructionElement($data->idConstructionBefore);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_before', $ce->getIdEntity(), $ceBefore->getIdEntity());
            }
            if ($data->idConstructionMeets != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                $ceMeets = new fnbr\models\ConstructionElement($data->idConstructionMeets);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_meets', $ce->getIdEntity(), $ceMeets->getIdEntity());
            }
            if ($data->idNumber != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_udfeature', $ce->getIdEntity(), $data->idNumber);
            }
            if ($data->idUDRelation != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_udrelation', $ce->getIdEntity(), $data->idUDRelation);
            }
            if ($data->idSemanticTypeLU != '') {
                $constraint = Base::createEntity('CN', 'con');
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_stlu', $ce->getIdEntity(), $data->idSemanticTypeLU);
            }
            if ($data->idConcept != '') {
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                $concept = new fnbr\models\Concept($data->idConcept);
                $conceptType = new fnbr\models\TypeInstance($data->idConceptType);
                Base::createEntityRelation($ce->getIdEntity(), 'rel_evokes', $concept->getIdEntity(), $conceptType->getIdEntity());
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception($e->getMessage());
        }
    }

    public function addConstraintsCN($data)
    {
        mdump($data);
        try {
            $transaction = Manager::getDatabase(Manager::getConf('fnbr.db'))->beginTransaction();
            if ($data->idConstructionElement != '') {
                $constraint = Base::createEntity('CN', 'con');
                $cn = new fnbr\models\ConstraintType($data->idConstraint);
                $ce = new fnbr\models\ConstructionElement($data->idConstructionElement);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_element', $cn->getId(), $ce->getIdEntity());
            }
            if ($data->idConstruction != '') {
                $cn = new fnbr\models\ConstraintType($data->idConstraint);
                $cxn = new fnbr\models\Construction($data->idConstruction);
                if ($cxn->getIdEntity() != '') {
                    // constraint 'cxn' Constraint(ele)-CXN
                    $constraintCxn = Base::createEntity('CN', 'con');
                    Base::createConstraintInstance($constraintCxn->getIdEntity(), 'con_cxn', $cn->getId(), $cxn->getIdEntity());
                    // constraints 'ele' Constraint(CXN)-CE
                    $ces = $cxn->listCE()->asQuery()->getResult();
                    foreach ($ces as $ce) {
                        $constraintEle = Base::createEntity('CN', 'con');
                        Base::createConstraintInstance($constraintEle->getIdEntity(), 'con_element', $constraintCxn->getIdEntity(), $ce['idEntity']);
                    }
                } else { // constraint is a cxn that is another constraint
                    $cn2 = new fnbr\models\ConstraintType($data->idConstruction);
                    // constraint 'cxn' Constraint(ele)-Constraint(CXN)
                    $constraintCxn = Base::createEntity('CN', 'con');
                    Base::createConstraintInstance($constraintCxn->getIdEntity(), 'con_cxn', $cn->getId(), $cn2->getIdEntity());
                }
            }
            if ($data->idFrame != '') {
                $constraint = Base::createEntity('CN', 'con');
                $cn = new fnbr\models\ConstraintType($data->idConstraint);
                $frame = new fnbr\models\Frame($data->idFrame);
                //Base::createEntityRelation($constraint->getIdEntity(), 'con_frame', $cn->getId(), $frame->getIdEntity());
                Base::createConstraintInstance($constraint->getIdEntity(), 'rel_evokes', $cn->getId(), $frame->getIdEntity());
            }
            if ($data->idFrameFamily != '') {
                $constraint = Base::createEntity('CN', 'con');
                $cn = new fnbr\models\ConstraintType($data->idConstraint);
                $frame = new fnbr\models\Frame($data->idFrameFamily);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_framefamily', $cn->getId(), $frame->getIdEntity());
            }
            if ($data->idLexemeCN != '') {
                $constraint = Base::createEntity('CN', 'con');
                $cn = new fnbr\models\ConstraintType($data->idConstraint);
                $lexeme = new fnbr\models\Lexeme($data->idLexemeCN);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_lexeme', $cn->getIdEntity(), $lexeme->getIdEntity());
            }
            if ($data->idLemmaCN != '') {
                $constraint = Base::createEntity('CN', 'con');
                $cn = new fnbr\models\ConstraintType($data->idConstraint);
                $lemma = new fnbr\models\Lemma($data->idLemmaCN);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_lemma', $cn->getIdEntity(), $lemma->getIdEntity());
            }
            if ($data->idLUCN != '') {
                $constraint = Base::createEntity('CN', 'con');
                $cn = new fnbr\models\ConstraintType($data->idConstraint);
                $lu = new fnbr\models\LU($data->idLUCN);
                Base::createConstraintInstance($constraint->getIdEntity(), 'con_lu', $cn->getIdEntity(), $lu->getIdEntity());
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception($e->getMessage());
        }
    }


}
