<?php

class AnnotationMMService extends MService
{

    public function getObjectsData($params, $idLanguage)
    {
        $idAnnotationSetMM = $params->idAnnotationSetMM;

        $objectMM = new \fnbr\models\ObjectMM();
        $objectMM->getByIdAnnotationSetMM($idAnnotationSetMM );

        $line = [];

        $line[] = [
            'idObject' => 1,
            'startFrame' => 0,
            'endFrame' => 0,
            'frameElement' => 'frame.element'
        ];

        /*

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
*/
// last, create data
        $data = array();
        foreach ($line as $idLine => $object) {
            $data[] = $object;
        }
        //mdump($data);
        return json_encode($data);
        //return $data;
    }

    public function putObjects($objects)
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

}
