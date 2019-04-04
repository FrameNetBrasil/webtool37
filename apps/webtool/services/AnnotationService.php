<?php

class AnnotationService extends MService
{

    public function getColor()
    {
        $color = new fnbr\models\Color();
        $colors = $color->listAll()->asQuery()->getResult();
        $result = new \stdclass();
        foreach ($colors as $c) {
            $node = new \stdclass();
            $node->rgbFg = '#' . $c['rgbFg'];
            $node->rgbBg = '#' . $c['rgbBg'];
            $idColor = $c['idColor'];
            $result->$idColor = $node;
        }
        return MUtil::php2js($result);//json_encode($result);
    }

    public function getLayerType()
    {
        $lt = new fnbr\models\LayerType();
        $lts = $lt->listAll()->asQuery()->getResult();
        $result = new \stdclass();
        foreach ($lts as $row) {
            $node = new \stdclass();
            $node->entry = $row['entry'];
            $node->name = $row['name'];
            $idLT = $row['idLayerType'];
            $result->$idLT = $node;
        }
        return MUtil::php2js($result);//json_encode($result);
    }

    public function getInstantiationType()
    {
        $type = new fnbr\models\Type();
        $instances = $type->getInstantiationType()->asQuery()->getResult();
        $array = array();
        $obj = new \stdclass();
        foreach ($instances as $instance) {
            if ($instance['instantiationType'] != 'APos') {
                $value = $instance['idInstantiationType'];
                $obj->$value = $instance['instantiationType'];
                $node = new \stdclass();
                if ($instance['instantiationType'] == 'Normal') {
                    $node->idInstantiationType = 0;
                    $node->instantiationType = '-';
                } else {
                    $node->idInstantiationType = $instance['idInstantiationType'];
                    $node->instantiationType = $instance['instantiationType'];
                }
                $array[] = $node;
            }
        }
        $result = [
            'array' => MUtil::php2js($array),//json_encode($array),
            'obj' => MUtil::php2js($obj)//json_encode($obj)
        ];
        return $result;
    }

    private function constraintLU()
    {
        $idLU = NULL;
        $userLevel = fnbr\models\Base::getCurrentUserLevel();
        if (($userLevel == 'BEGINNER') || ($userLevel == 'JUNIOR')) {
            $user = fnbr\models\Base::getCurrentUser();
            $lus = $user->getConfigData('fnbrConstraintsLU');
            if (is_array($lus) && count($lus)) {
                $idLU = $lus;
            } else {
                $idLU = -1;
            }
        }
        return $idLU;
    }

    public function listFrames($lu = '', $idLanguage = '')
    {
        $idLU = $this->constraintLU();
        if ($idLU == -1) {
            return json_encode([[]]);
        }
        $frame = new fnbr\models\ViewFrame();
        $filter = (object)['lu' => $lu, 'idLanguage' => $idLanguage, 'idLU' => $idLU];
        $frames = $frame->listByFilter($filter)->asQuery()->chunkResult('idFrame', 'name');
        $result = array();
        foreach ($frames as $idFrame => $name) {
            $node = array();
            $node['id'] = 'f' . $idFrame;
            $node['text'] = $name;
            $node['state'] = 'closed';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function listLUs($idFrame, $idLanguage)
    {
        $idLU = $this->constraintLU();
        if ($idLU == -1) {
            return json_encode([[]]);
        }
        $lu = new fnbr\models\ViewLU();
        $lus = $lu->listByFrame($idFrame, $idLanguage, $idLU)->asQuery()->chunkResult('idLU', 'name');
        $result = array();
        foreach ($lus as $idLU => $name) {
            $node = array();
            $node['id'] = 'l' . $idLU;
            $node['text'] = $name;
            $node['state'] = 'closed';
            $result[] = $node;
        }
        return json_encode($result);
    }

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
        return json_encode($result);
    }

    public function getSubCorpusTitle($idSubCorpus, $idLanguage, $isCxn)
    {
        $sc = $isCxn ? new fnbr\models\ViewSubCorpusCxn() : new fnbr\models\ViewSubCorpusLU();
        $title = $sc->getTitle($idSubCorpus, $idLanguage);
        return $title;
    }

    public function getSubCorpusStatus($idSubCorpus, $isCxn)
    {
        $sc = $isCxn ? new fnbr\models\ViewSubCorpusCxn() : new fnbr\models\ViewSubCorpusLU();
        $status = new \stdclass;
        $total = 0;
        $totalUnann = 0;
        $stats = $sc->getStats($idSubCorpus);
        foreach ($stats as $st) {
            $entry = $st['entry'];
            $status->stat->$entry = (object)['name' => $st['name'], 'quant' => $st['quant']];
            $total += $st['quant'];
            if ($entry == 'ast_unann') {
                ++$totalUnann;
            }
        }
        $status->stat->total = (object)['name' => _M('Total'), 'quant' => $total];
        if ($totalUnann == 0) {
            $status->status->code = 1;
            $status->status->msg = _M('Complete');
        } else {
            $status->status->code = 0;
            $status->status->msg = _M('Incomplete');
        }
        return $status;
    }

    public function getDocumentTitle($idDocument, $idLanguage)
    {
        $doc = new fnbr\models\Document();
        $filter = (object)['idDocument' => $idDocument];
        $result = $doc->listByFilter($filter)->asQuery()->getResult();
        return 'Document:' . $result['name'];
    }

    public function decorateSentence($sentence, $labels)
    {
        $decorated = "";
        $ni = "";
        $i = 0;
        //$sentence = utf8_decode($sentence);
        foreach ($labels as $label) {
            $style = 'background-color:#' . $label['rgbBg'] . ';color:#' . $label['rgbFg'] . ';';
            if ($label['startChar'] >= 0) {
                $decorated .= mb_substr($sentence, $i, $label['startChar'] - $i);
                $decorated .= "<span style='{$style}'>" . mb_substr($sentence, $label['startChar'], $label['endChar'] - $label['startChar'] + 1) . "</span>";
                $i = $label['endChar'] + 1;
            } else { // null instantiation
                $ni .= "<span style='{$style}'>" . $label['instantiationType'] . "</span> " . $decorated;
            }
        }
        //$decorated = utf8_encode($ni . $decorated . substr($sentence, $i));
        $decorated = $ni . $decorated . mb_substr($sentence, $i);
        return $decorated;
    }

    public function listAnnotationSet($idSubCorpus, $sortable = NULL)
    {
        $as = new fnbr\models\ViewAnnotationSet();
        $sentences = $as->listBySubCorpus($idSubCorpus, $sortable)->asQuery()->getResult();
        $annotation = $as->listFECEBySubCorpus($idSubCorpus);
        $result = array();
        foreach ($sentences as $sentence) {
            $node = array();
            $node['idAnnotationSet'] = $sentence['idAnnotationSet'];
            $node['idSentence'] = $sentence['idSentence'];
            if ($annotation[$sentence['idSentence']]) {
                $node['text'] = $this->decorateSentence($sentence['text'], $annotation[$sentence['idSentence']]);
            } else {
                $targets = $as->listTargetBySentence($sentence['idSentence']);
                $node['text'] = $this->decorateSentence($sentence['text'], $targets);
                //$node['text'] = $sentence['text'];
            }
            $node['status'] = $sentence['annotationStatus'];
            $node['rgbBg'] = $sentence['rgbBg'];
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function getLayers($params, $idLanguage)
    {
        $idSentence = $params->idSentence;
        $idAnnotationSet = $params->idAnnotationSet;
        $annotationType = $params->type;
        $language = \fnbr\models\Base::languages()[$idLanguage];

        $layers = [
            "words" => NULL,
            "frozenColumns" => NULL,
            "columns" => NULL,
            "labels" => NULL,
            "layers" => NULL,
            "labelTypes" => NULL,
            "nis" => NULL,
        ];

        if ($annotationType == 'c') { // corpus mode
            $as = new fnbr\models\AnnotationSet();
        } else {
            $as = $idAnnotationSet ? new fnbr\models\AnnotationSet($idAnnotationSet) : new fnbr\models\AnnotationSet();
        }

        // get words/chars
        $wordsChars = $as->getWordsChars($idSentence);
        $words = $wordsChars->words;
        $chars = $wordsChars->chars;

        $result = [];
        foreach ($words as $i => $word) {
            $fieldData = $i;
            $result[$fieldData] = (object)[
                'word' => $word['word'],
                'startChar' => $word['startChar'],
                'endChar' => $word['endChar']
            ];
        }
        $layers['words'] = MUtil::php2js($result);;//json_encode($result);

        $result = [];
        foreach ($chars as $i => $char) {
            $fieldData = 'wf' . $i;
            $result[$fieldData] = (object)[
                'order' => $char['offset'],
                'char' => $char['char'],
                'word' => $char['order']
            ];
        }
        $layers['chars'] = MUtil::php2js($result);//json_encode($result);

        if ($annotationType == 'c') { // corpus mode
            $header = "[{$idSentence}] ";
        } else {
            // annotationSet Status
            $asStatus = $as->getFullAnnotationStatus();
            $header = "[{$idSentence}] " . "<span class='fa fa-square' style='width:16px;color:#" . $asStatus->rgbBg . "'></span><span>" . $asStatus->annotationStatus . "</span>";
        }

        // get hiddenColumns/frozenColumns/Columns using $words
        $frozenColumns[] = array(
            "field" => "layer",
            "width" => '60',
            "title" => $header,
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
            $width = 13;
            if ($language == 'jp') {
                if ($char['char'] == ' ') {
                    continue;
                }
                $width = 18;
            }
            if ($language == 'hi') {
                $width = 18;
            }
            if ($language == 'te') {
                $width = 18;
            }
            if ($language == 'kn') {
                $width = 18;
            }
            $columns[] = array(
                "hidden" => 'false',
                "field" => 'wf' . $i,
                "width" => $width,
                "resizable" => "false",
                "title" => $char['char'],
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
            $result[$row['idLayer']] = [
                'idAnnotationSet' => $row['idAnnotationSet'],
                'nameLayer' => $row['name'],
                'currentLabel' => '0',
                'currentLabelPos' => 0
            ];
        }

        // CE-FE is a "artificial" layer; it needs to be inserts manually
        $queryLabelType = $as->getLabelTypesCEFE($idSentence);
        $rowsCEFE = $queryLabelType->getResult();
        foreach ($rowsCEFE as $row) {
            $result[$row['idLayer']] = [
                'idAnnotationSet' => $row['idAnnotationSet'],
                'nameLayer' => $row['idLayer'],
                'currentLabel' => '0',
                'currentLabelPos' => 0
            ];
        }
        $layers['layers'] = MUtil::php2js($result);//json_encode($result);

        // get AnnotationSets
        $result = array();
        $annotationSets = $as->getAnnotationSets($idSentence);
        foreach ($annotationSets as $row) {
            $result[$row['idAnnotationSet']] = [
                'idAnnotationSet' => $row['idAnnotationSet'],
                'name' => $row['name'],
                'type' => $row['type'],
                'show' => true
            ];
        }
        $layers['annotationSets'] = MUtil::php2js($result);;//json_encode($result);

        /*
        // get Labels
        $result = array();
        $asLabels = $as->getLabels($idSentence);
        foreach ($asLabels as $row) {
            $result[$row['idLayer']][$row['idLabel']] = ['idLabelType' => $row['idLabelType']];
        }
        $layers['labels'] = json_encode($result);
        */

        // get LabelTypes
        $result = [];
        $layerLabels = [];
        $layerLabelsTemp = [];

        // GL-GF
        $queryLabelType = $as->getLabelTypesGLGF($idSentence)->asQuery();
        $rows = $queryLabelType->getResult();
        foreach ($rows as $row) {
            //    $result[$row['idLayer']][$row['idLabelType']] = [
            if (!isset($layerLabelsTemp[$row['idLayer']][$row['idLabelType']])) {
                $layerLabels[$row['idLayer']][] = $row['idLabelType'];
                $layerLabelsTemp[$row['idLayer']][$row['idLabelType']] = 1;
            }
            $result[$row['idLabelType']] = [
                'label' => $row['labelType'],
                'idColor' => $row['idColor'],
                'coreType' => $row['coreType']
            ];
        }
        // GL
        $queryLabelType = $as->getLabelTypesGL($idSentence)->asQuery();
        $rows = $queryLabelType->getResult();
        foreach ($rows as $row) {
            //    $result[$row['idLayer']][$row['idLabelType']] = [
            if (!isset($layerLabelsTemp[$row['idLayer']][$row['idLabelType']])) {
                $layerLabels[$row['idLayer']][] = $row['idLabelType'];
                $layerLabelsTemp[$row['idLayer']][$row['idLabelType']] = 1;
            }
            $result[$row['idLabelType']] = [
                'label' => $row['labelType'],
                'idColor' => $row['idColor'],
                'coreType' => $row['coreType']
            ];
        }
        // FE
        $queryLabelType = $as->getLabelTypesFE($idSentence);
        $rows = $queryLabelType->getResult();
        //mdump($rows);
        foreach ($rows as $row) {
            //    $result[$row['idLayer']][$row['idLabelType']] = [
            if (!isset($layerLabelsTemp[$row['idLayer']][$row['idLabelType']])) {
                $layerLabels[$row['idLayer']][] = $row['idLabelType'];
                $layerLabelsTemp[$row['idLayer']][$row['idLabelType']] = 1;
            }
            $result[$row['idLabelType']] = [
                'label' => $row['labelType'],
                'idColor' => $row['idColor'],
                'coreType' => $row['coreType']
            ];
        }
        // CE
        $queryLabelType = $as->getLabelTypesCE($idSentence);
        $rows = $queryLabelType->getResult();
        foreach ($rows as $row) {
            //    $result[$row['idLayer']][$row['idLabelType']] = [
            if (!isset($layerLabelsTemp[$row['idLayer']][$row['idLabelType']])) {
                $layerLabels[$row['idLayer']][] = $row['idLabelType'];
                $layerLabelsTemp[$row['idLayer']][$row['idLabelType']] = 1;
            }
            $result[$row['idLabelType']] = [
                'label' => $row['labelType'],
                'idColor' => $row['idColor'],
                'coreType' => $row['coreType']
            ];
        }

        // CE-FE - $rowsCEFE is obtained via query for layer above
        foreach ($rowsCEFE as $row) {
            //    $result[$row['idLayer']][$row['idLabelType']] = [
            if (!isset($layerLabelsTemp[$row['idLayer']][$row['idLabelType']])) {
                $layerLabels[$row['idLayer']][] = $row['idLabelType'];
                $layerLabelsTemp[$row['idLayer']][$row['idLabelType']] = 1;
            }
            $result[$row['idLabelType']] = [
                'label' => $row['labelType'],
                'idColor' => $row['idColor'],
                'coreType' => $row['coreType']
            ];
        }

        // UDTree
        $UDTreeLayer = [];
        $UDTreeLayer['none'] = '';
        /*
        $queryUDTree = $as->getUDTree($idSentence);
        $rows = $queryUDTree->getResult();
        foreach ($rows as $row) {
            if (!isset($UDTree[$row['idLayer']])) {
                $UDTree[$row['idLayer']][$row['idLabel']] = $row['idLabelParent'];
            }
        }
        */


//mdump($result);
        $layers['labelTypes'] = MUtil::php2js($result);//json_encode($result);
        $layers['layerLabels'] = MUtil::php2js($layerLabels);//json_encode($result);
        $layers['UDTreeLayer'] = MUtil::php2js($UDTreeLayer);

        // get NIs
        //$niFields = array();
        $result = array();
        $queryNI = $as->getNI($idSentence, $idLanguage);
        $rows = $queryNI->getResult();
        foreach ($rows as $row) {
            $result[$row['idLayer']][$row['idLabelType']] = [
                'fe' => $row['feName'],
                'idInstantiationType' => $row['idInstantiationType'],
                'label' => $row['instantiationType'],
                'idColor' => $row['idColor']
            ];
        }
        $layers['nis'] = (count($result) > 0) ? MUtil::php2js($result) : "{}";
        $layers['data'] = 'null';
        return $layers;
    }

    public function getLayersData($params, $idLanguage)
    {
        $idSentence = $params->idSentence;
        $idAnnotationSet = $params->idAnnotationSet;

        $as = new fnbr\models\AnnotationSet($idAnnotationSet);
        if (($idAnnotationSet == '') || ($idAnnotationSet == '0')) {
            $idLU = $idCxn = NULL;
        } else {
            $idLU = $as->getSubCorpus()->getIdLU();
            $idCxn = $as->getSubCorpus()->getIdCxn();
        }
        $isCxn = ($idLU == NULL) && ($idCxn != NULL);

        $result = array();
        $queryLayersData = $as->getLayersData($idSentence);
        $unorderedRows = $queryLayersData->getResult();

        // get the annotationsets - first ordered by target then the other which has no target (cxn)
        $layersOrderedByTarget = $as->getLayersOrderByTarget($idSentence)->getResult();
        $aSet = [];
        $aTarget = [];
        foreach ($layersOrderedByTarget as $layersOrdered) {
            $aTarget[$layersOrdered['idAnnotationSet']] = 1;
            foreach ($unorderedRows as $row) {
                if ($layersOrdered['idAnnotationSet'] == $row['idAnnotationSet']) {
                    $aSet[$row['idAnnotationSet']][] = $row;
                }
            }

        }
        foreach ($unorderedRows as $row) {
            if ($aTarget[$row['idAnnotationSet']] == '') {
                $aSet[$row['idAnnotationSet']][] = $row;
            }
        }

        // reorder rows to put Target on top of each annotatioset
        $rows = array();
        $idHeaderLayer = -1;

        foreach ($aSet as $asRows) {
            $hasTarget = false;
            foreach ($asRows as $row) {
                if ($row['layerTypeEntry'] == 'lty_target') {
                    $row['idLayerType'] = 0;
                    $rows[] = $row;
                    $hasTarget = true;
                }
            }
            if ($hasTarget) {
                foreach ($asRows as $row) {
                    if ($row['layerTypeEntry'] != 'lty_target') {
                        $rows[] = $row;
                    }
                }
            } else {
                $headerLayer = $asRows[0];
                $headerLayer['layer'] = 'x';
                $headerLayer['startChar'] = -1;
                $headerLayer['idLayerType'] = 0;
                $headerLayer['layerTypeEntry'] = 'lty_as';
                $headerLayer['idLayer'] = $idHeaderLayer--;
                $rows[] = $headerLayer;
                foreach ($asRows as $row) {
                    $rows[] = $row;
                }
            }
        }
        //mdump($rows);
        // CE-FE
        $ltCEFE = new fnbr\models\LayerType();
        $ltCEFE->getByEntry('lty_cefe');
        $queryLabelType = $as->getLayerNameCnxFrame($idSentence);
        $cefe = $queryLabelType->chunkResultMany('idLayer', ['idFrame', 'name','idAnnotationSet'], 'A');

        $level = Manager::getSession()->fnbrLevel;
        if ($level == 'BEGINNER') {
            $layersToShow = Manager::getConf('fnbr.beginnerLayers');
        } else {
            $layersToShow = Manager::getSession()->fnbrLayers;
            if ($layersToShow == '') {
                $user = Manager::getLogin()->getUser();
                $layersToShow = Manager::getSession()->fnbrLayers = $user->getConfigData('fnbrLayers');
            }
        }

        $wordsChars = $as->getWordsChars($idSentence);
        $chars = $wordsChars->chars;
        $line = [];

        if (count($rows) == 0) {
            // annotationSet Status
            $asStatus = $as->getFullAnnotationStatus();
            //
            $line[-1] = new \stdclass();
            $line[-1]->idAnnotationSet = -1;
            $line[-1]->idLayerType = -1;
            $line[-1]->layerTypeEntry = '';
            $line[-1]->idLayer = 0;
            $line[-1]->layer = '';//"[{$idSentence}] " . "<span class='fa fa-square' style='width:16px;color:#" . $asStatus->rgbBg . "'></span><span>" . $asStatus->annotationStatus . "</span>";
            $line[-1]->ni = 'NI';
            $line[-1]->show = true;
            for ($posChar = 0; $posChar < count($chars); $posChar++) {
                $field = 'wf' . $posChar;
                $line[-1]->$field = '';//$chars[$posChar]['char'];
            }
        }

        //

        $idLayerRef = 0;
        $lastLayerTypeEntry = '';
        // each row is a Label - the loop aggregates labels in Layers
        foreach ($rows as $row) {
            $idLT = $row['idLayerType'];
            if ($idLT != 0) {
                if (!in_array($idLT, $layersToShow)) {
                    //  mdump('*'.$idLT);
                    continue;
                }
            }
            $idLayer = $row['idLayer'];
            if ($idLayer != $idLayerRef) {
                $line[$idLayer] = new \stdclass();
                $line[$idLayer]->idAnnotationSet = $row['idAnnotationSet'];
                $line[$idLayer]->idLayerType = $row['idLayerType'];
                $line[$idLayer]->layerTypeEntry = $row['layerTypeEntry'];
                $line[$idLayer]->idLayer = $idLayer;
                if ($row['idLayerType'] == 0) {
                    $line[$idLayer]->layer = 'AS_' . $row['idAnnotationSet'];
                } else {
                    $line[$idLayer]->layer = $row['layer'];
                }
                $line[$idLayer]->ni = '';
                $line[$idLayer]->show = true;
                $idLayerRef = $idLayer;
                $lastLayerTypeEntry = $row['layerTypeEntry'];
                // if lastLayer=CE, try to add the layers for CE-FE
                if ($lastLayerTypeEntry == 'lty_ce') {
                    foreach ($cefe as $idLayerCEFE => $frame) {
                        if ($frame[2] == $row['idAnnotationSet']) {
                            $line[$idLayerCEFE] = new \stdclass();
                            $line[$idLayerCEFE]->idAnnotationSet = $row['idAnnotationSet'];
                            $line[$idLayerCEFE]->idLayerType = "{$ltCEFE->getId()}";
                            $line[$idLayerCEFE]->layerTypeEntry = $idLayerCEFE;
                            $line[$idLayerCEFE]->idLayer = $idLayerCEFE;
                            $line[$idLayerCEFE]->layer = $frame[1] . '.FE';
                            $line[$idLayerCEFE]->ni = '';
                            $line[$idLayerCEFE]->show = true;
                            $cefeData = $as->getCEFEData($idSentence, $idLayerCEFE)->getResult();
                            foreach ($cefeData as $labelCEFE) {
                                if ($labelCEFE['startChar'] > -1) {
                                    $posChar = $labelCEFE['startChar'];
                                    while ($posChar <= $labelCEFE['endChar']) {
                                        $field = 'wf' . $posChar;
                                        $line[$idLayerCEFE]->$field = $labelCEFE['idLabelType'];
                                        $posChar += 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ($row['startChar'] > -1) {
                $posChar = $row['startChar'];
                $i = 0;
                while ($posChar <= $row['endChar']) {
                    $field = 'wf' . $posChar;
                    if ($row['layer'] == 'Target') {
                        $line[$idLayer]->$field = $chars[$posChar]['char'];
                    } else {
                        $line[$idLayer]->$field = $row['idLabelType'];
                    }
                    $posChar += 1;
                }
            }
        }

// last, create data
        $data = array();
        foreach ($line as $idLine => $layer) {
            $data[] = $layer;
        }
        //mdump($data);
        return json_encode($data);
        //return $data;
    }

    public function putLayers($layers)
    {
        $annotationSet = new fnbr\models\AnnotationSet();
        $transaction = $annotationSet->beginTransaction();
        try {
            $idAS = [];
            $hasFE = $annotationSet->putLayers($layers);
            foreach ($layers as $layer) {
                $idAnnotationSet = $layer->idAnnotationSet;
                $idAS[$idAnnotationSet] = $idAnnotationSet;
            }
            $typeInstance = new fnbr\models\TypeInstance();
            $annotationStatus = $typeInstance->listAnnotationStatus('', 'entry, idTypeInstance')->asQuery()->chunkResult('entry', 'idTypeInstance');
            $idAnnotationStatus = $annotationStatus[fnbr\models\Base::getAnnotationStatus()];
            foreach ($idAS as $idAnnotationSet) {
                if ($hasFE[$idAnnotationSet]) {
                    $annotationSet->getById($idAnnotationSet);
                    $annotationSet->setIdAnnotationStatus($idAnnotationStatus);
                    $annotationSet->save();
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception('Save failed: ' . $e->getMessage());
        }
    }

    public function addFELayer($idAnnotationSet)
    {
        $annotationSet = new fnbr\models\AnnotationSet($idAnnotationSet);
        $idLayer = $annotationSet->addFELayer();
        $result[$idLayer] = [
            'idAnnotationSet' => $idAnnotationSet,
            'nameLayer' => 'FE',
            'currentLabel' => '0',
            'currentLabelPos' => 0
        ];
        return $result;
    }

    public function getFELabels($idAnnotationSet, $idSentence)
    {
        $annotationSet = new fnbr\models\AnnotationSet($idAnnotationSet);
        $queryLabelType = $annotationSet->getLabelTypesFE($idSentence, true);
        $rows = $queryLabelType->getResult();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['idLayer']][$row['idLabelType']] = [
                'label' => $row['labelType'],
                'idColor' => $row['idColor'],
                'coreType' => $row['coreType']
            ];
        }
        return $result;
    }

    public function delFELayer($idAnnotationSet)
    {
        $annotationSet = new fnbr\models\AnnotationSet($idAnnotationSet);
        $annotationSet->delFELayer();
        $this->render();
    }

    public function validation($as, $validation, $feedback = '')
    {
        $annotationSet = new fnbr\models\AnnotationSet();
        foreach ($as as $idAnnotationSet => $o) {
            $annotationSet->getById($idAnnotationSet);
            $annotationSet->setIdAnnotationStatus(fnbr\models\Base::getAnnotationStatus(true, $validation));
            $annotationSet->save();
            if ($validation == '0') { // ast_disapp 
                $this->notifySupervised($annotationSet, $feedback);
            }
        }
    }

    public function notifySupervised($annotationSet, $feedback = '')
    {
        $idLU = $annotationSet->getIdLU();
        $user = fnbr\models\Base::getCurrentUser();
        $userSupervised = $user->getUserSupervisedByIdLU($idLU);
        if ($userSupervised) {
            $emailService = Manager::getAppService('email');
            $email = $userSupervised->getPerson()->getEmail();
            $to[$email] = $email;
            $subject = 'FNBr - AnnotationSet Disapproved';
            $body = "<p>From supervisor: " . $user->getLogin() . ' - ' . $user->getPerson()->getName() . "</p>";
            $subCorpus = $annotationSet->getSubCorpus();
            $body .= "<p>SubCorpus [" . $subCorpus->getTitle() . '] - Sentence [' . $annotationSet->getIdSentence() . ']  disapproved. Please, correct it.</p>';
            $body .= "<p>Message: " . $feedback . "</p>";
            $emailService->sendSystemEmail($to, $subject, $body);
        } else {
            throw new \exception("No supervised user.");
        }
    }

    public function notifySupervisor($as)
    {
        $body = '';
        $annotationSet = new fnbr\models\AnnotationSet();
        foreach ($as as $idAnnotationSet => $o) {
            $annotationSet->getById($idAnnotationSet);
            $status = $this->getSubCorpusStatus($annotationSet->getIdSubCorpus());
            if ($status->status->code == 1) {
                $subCorpus = $annotationSet->getSubCorpus();
                $body .= "<p>SubCorpus [" . $subCorpus->getTitle() . '] completed.</p>';
            }
        }
        if ($body != '') {
            $emailService = Manager::getAppService('email');
            $user = fnbr\models\Base::getCurrentUser();
            $userLevel = $user->getUserLevel();
            if ($userLevel == 'BEGINNER') {
                $idSupervisor = $user->getConfigData('fnbrJuniorUser');
            } else if ($userLevel == 'JUNIOR') {
                $idSupervisor = $user->getConfigData('fnbrSeniorUser');
            } else if ($userLevel == 'SENIOR') {
                $idSupervisor = $user->getConfigData('fnbrMasterUser');
            }
            $supervisor = new fnbr\models\User($idSupervisor);
            $email = $supervisor->getPerson()->getEmail();
            $to[$email] = $email;
            $subject = 'FNBr - SubCorpus Completed';
            $body = "<p>From annotator: " . $user->getLogin() . ' - ' . $user->getPerson()->getName() . "</p>" . $body;
            $emailService->sendSystemEmail($to, $subject, $body);
        } else {
            throw new \exception("No completed Set");
        }
    }

    public function listCxn($cxn = '', $idLanguage = '')
    {
        $construction = new fnbr\models\Construction();
        $filter = (object)['cxn' => $cxn, 'idLanguage' => $idLanguage];
        $constructions = $construction->listByFilter($filter)->asQuery()->chunkResult('idConstruction', 'name');
        $result = array();
        foreach ($constructions as $idCxn => $name) {
            $node = array();
            $node['id'] = 'c' . $idCxn;
            $node['text'] = $name;
            $node['state'] = 'closed';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function listSubCorpusCxn($idCxn)
    {
        $sc = new fnbr\models\SubCorpus();
        $scs = $sc->listByCxn($idCxn)->asQuery()->getResult();
        foreach ($scs as $sc) {
            $node = array();
            $node['id'] = 's' . $sc['idSubCorpus'];
            $node['text'] = $sc['name'] . ' [' . $sc['quant'] . ']';
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
            $sc->addManualSubcorpusCxn($data);
        }
    }

    public function cxnGridData()
    {
        $cxn = new fnbr\models\Construction();
        $criteria = $cxn->listAll();
        $data = $cxn->gridDataAsJSON($criteria);
        return $data;
    }

    public function listCorpus($corpusName = '', $idLanguage = '')
    {
        $corpus = new fnbr\models\Corpus();
        $filter = (object)['corpus' => $corpusName, 'idLanguage' => $idLanguage];
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
            if ($doc['idDocument']) {
                $node = array();
                $node['id'] = 'd' . $doc['idDocument'];
                $node['text'] = $doc['name'] . ' [' . $doc['quant'] . ']';
                $node['state'] = 'open';
                $result[] = $node;
            }
        }
        return json_encode($result);
    }

    public function changeStatusAS($arrayAS, $newStatus)
    {
        $as = new fnbr\models\AnnotationSet();
        foreach ($arrayAS as $idAnnotationStatus) {
            $as->getById($idAnnotationStatus);
            $as->setIdAnnotationStatus($newStatus);
            $as->save();
        }
    }

    public function deleteAS($arrayAS)
    {
        $as = new fnbr\models\AnnotationSet();
        foreach ($arrayAS as $idAnnotationSet) {
            $as->getById($idAnnotationSet);
            $as->delete();
        }
    }

    public function getLabelHelp($idLanguage)
    {
        $gl = new fnbr\models\GenericLabel();
        $queryLabelHelp = $gl->listForHelp($idLanguage)->asQuery();
        $rows = $queryLabelHelp->getResult();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['idGenericLabel']] = [
                'layer' => $row['layer'],
                'name' => $row['name'],
                'definition' => $row['definition'],
                'example' => $row['example'],
                'rgbFg' => $row['rgbFg'],
                'rgbBg' => $row['rgbBg'],
            ];
        }
        return $result;
    }

    public function getASComments($idAnnotationSet)
    {
        $asc = new \fnbr\models\ASComments();
        $asc->getByAnnotationSet($idAnnotationSet);
        $data = $asc->getData();
        $as = $asc->getAnnotationset();
        $as->getById($idAnnotationSet);
        $data->luName = $as->getLUFullName();
        return $data;
    }

    public function saveASComments($data)
    {
        $asc = new \fnbr\models\ASComments();
        $asc->getByAnnotationSet($data->idAnnotationSet);
        $asc->setData($data);
        $asc->save();
    }

}
