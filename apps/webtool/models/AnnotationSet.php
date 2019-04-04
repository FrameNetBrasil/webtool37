<?php

/**
 *
 *
 * @category   Maestro
 * @package    UFJF
 * @subpackage fnbr
 * @copyright  Copyright (c) 2003-2012 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version
 * @since
 */

namespace fnbr\models;

class AnnotationSet extends map\AnnotationSetMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'timeline' => array('notnull'),
                'idSubCorpus' => array('notnull'),
                'idSentence' => array('notnull'),
                'idAnnotationStatus' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getIdAnnotationSet();
    }

    public function setIdAnnotationStatus($value)
    {
        if (substr($value, 0, 3) == 'ast') {
            $ti = new TypeInstance();
            $filter = (object)['entry' => $value];
            $idStatus = $ti->listAnnotationStatus($filter)->asQuery()->getResult()[0]['idAnnotationStatus'];
        } else {
            $idStatus = $value;
        }
        parent::setIdAnnotationStatus($idStatus);
    }

    public function getFullAnnotationStatus()
    {
        $idAnnotationStatus = ($this->getIdAnnotationStatus() ?: '0');
        $criteria = $this->getCriteria()->
        select('idAnnotationStatus, annotationStatus.entry, annotationStatus.entries.name as annotationStatus, annotationStatus.idTypeInstance,' .
            'annotationStatus.color.rgbBg');
        if ($idAnnotationStatus) {
            $criteria->where("idAnnotationStatus = {$idAnnotationStatus}");
        }
        Base::entryLanguage($criteria, 'annotationStatus');
        return $criteria->asQuery()->asObjectArray()[0];
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idAnnotationSet');
        if ($filter->idAnnotationSet) {
            $criteria->where("idAnnotationSet LIKE '{$filter->idAnnotationSet}%'");
        }
        return $criteria;
    }

    public function listBySubCorpus($idSubCorpus)
    {
        $criteria = $this->getCriteria()->select('*');
        $criteria->where("idSubCorpus = {$idSubCorpus}");
        return $criteria;
    }

    public function getIdLU()
    {
        return $this->getSubCorpus()->getIdLU();
    }

    public function getLUFullName()
    {
        $idLU = $this->getSubCorpus()->getIdLU();
        if ($idLU) {
            $lu = new LU($this->getSubCorpus()->getIdLU());
            return $lu->getFullName();
        } else {
            return '';
        }
    }

    public function getWords($idSentence)
    {
        $criteria = $this->getCriteria()->
        select('sentence.text')->
        where("idSentence = {$idSentence}");
        $result = $criteria->asQuery()->getResult();
        //$text = utf8_decode($result[0][0]);
        $text = $result[0][0];
        $array = array();
        $punctuation = " .,;:?/'][\{\}\"!@#$%&*\(\)-_+=";
        $word = "";

        mb_internal_encoding("UTF-8"); // this IS A MUST!! PHP has trouble with multibyte when no internal encoding is set!
        $i = 0;
        for ($j = 0; $j < mb_strlen($text); $j++) {
            $char = mb_substr($text, $j, 1);
            $break = (mb_strpos($punctuation, $char) !== false);
            if ($break) {
                $word = mb_substr($text, $i, $j - $i);
                $array[$i] = $word;
                $array[$j] = $char;
                $i = $j + 1;
            }
        }
        $values[-1] = [0, '', -1];
        $order = 0;
        foreach ($array as $startChar => $wordForm) {
            $endChar = $startChar + mb_strlen($wordForm) - 1;
            $lWordForm = str_replace("'", "\\'", $wordForm);
            ++$order;
            $values[$startChar] = [$order, $lWordForm, $startChar, $endChar]; //"{$order}, {$startChar}, {$endChar}, {$idSentence}, 0, '{$lWordForm}'";
        }
        return $values;
    }

    public function getWordsChars($idSentence)
    {
        $criteria = $this->getCriteria()->
        select('sentence.text')->
        where("idSentence = {$idSentence}");
        $result = $criteria->asQuery()->getResult();
        //$text = utf8_decode($result[0]['text']);
        $text = $result[0]['text'];
        $array = array();
        $punctuation = " .,;:?/'][\{\}\"!@#$%&*\(\)-_+=";
        $word = "";

        mb_internal_encoding("UTF-8"); // this IS A MUST!! PHP has trouble with multibyte when no internal encoding is set!
        $i = 0;
        for ($j = 0; $j < mb_strlen($text); $j++) {
            $char = mb_substr($text, $j, 1);
            $break = (mb_strpos($punctuation, $char) !== false);
            if ($break) {
                $word = mb_substr($text, $i, $j - $i);
                $array[$i] = $word;
                $array[$j] = $char;
                $i = $j + 1;
            }
        }
        $chars = [];
        $order = 1;
        foreach ($array as $startChar => $wordForm) {
            $endChar = $startChar + mb_strlen($wordForm) - 1;
            $lWordForm = $wordForm;//str_replace("'", "\\'", $wordForm);
            $words[(string)$order] = [
                'order' => $order,
                'word' => $lWordForm,
                'startChar' => $startChar,
                'endChar' => $endChar
            ];
            for ($pos = (int)$startChar; $pos <= $endChar; $pos++) {
                $o = $pos - $startChar;
                $chars[$pos] = [
                    'offset' => (string)$o,
                    'char' => mb_substr($wordForm, $o, 1), // tf8_encode($wordForm{$o}), //str_replace("'", "\\'", $wordForm{$o}),
                    'order' => $order
                ];
            }
            ++$order;
        }
        $wordsChars = new \StdClass();
        $wordsChars->words = $words;
        $wordsChars->chars = $chars;
        return $wordsChars;
    }

    public function allowManyAnnotationSet() {
        $allow = \Manager::checkAccess('MASTER', A_EXECUTE) || \Manager::checkAccess('ANNO', A_EXECUTE);
        $allow = $allow || (\Manager::checkAccess('READER', A_EXECUTE));
        return $allow;
    }

    public function getAnnotationSets($idSentence)
    {
        $as = new ViewAnnotationSet();
        $criteriaLU = $as->getCriteria()
            ->select("idAnnotationSet, concat(frameEntries.name, '.', view_lu.name) as name, 'lu' as type")
            ->where("idSentence = {$idSentence}");
        $criteriaLU->setDistinct(true);
        $criteriaLU->associationAlias("subcorpuslu.lu.frame.entries", "frameEntries");
        if (!$this->allowManyAnnotationSet()) {
            $criteriaLU->where("idAnnotationSet = {$this->getId()}");
        }
        Base::entryLanguage($criteriaLU, "frameEntries.");

        $criteriaCxn = $as->getCriteria()
            ->select("idAnnotationSet, cxnEntries.name as name, 'cxn' as type")
            ->where("idSentence = {$idSentence}");
        $criteriaCxn->setDistinct(true);
        $criteriaCxn->associationAlias("subcorpuscxn.construction.entries", "cxnEntries");
        if (!$this->allowManyAnnotationSet()) {
            $criteriaCxn->where("idAnnotationSet = {$this->getId()}");
        }
        Base::entryLanguage($criteriaCxn, "cxnEntries.");

        $criteria = $criteriaLU;
        $criteria->union($criteriaCxn);
        $result = $criteria->asQuery()->getResult();

        return $result;
    }

    public function getLayers($idSentence)
    {
        $criteria = $this->getCriteria();
        $criteria->select('layers.idLayer, layers.layertype.entries.name as name, idAnnotationSet');
        $criteria->where("idSentence = {$idSentence}");
        Base::entryLanguage($criteria, 'layers.layertype');
        if (!$this->allowManyAnnotationSet()) {
            $criteria->where("idAnnotationSet = {$this->getId()}");
        }
        return $criteria->asQuery()->getResult();
    }

    public function getLabels($idSentence)
    {
        $criteria = $this->getCriteria();
        $criteria->select('layers.idLayer, layers.labels.idLabel, layers.labels.idLabelType');
        $criteria->where("idSentence = {$idSentence}");
        if (!$this->allowManyAnnotationSet()) {
            $criteria->where("idAnnotationSet = {$this->getId()}");
        }
        return $criteria->asQuery()->getResult();
    }

    public function getLabelTypesGLGF($idSentence)
    {
        $criteria = $this->getCriteria();
        $criteria->select("idAnnotationSet, l.idLayer, genericlabel.idEntity as idLabelType, genericlabel.name as labelType, genericlabel.idColor, '' as coreType");
        //Base::relation($criteria, 'LU lu', 'SubCorpus sc', 'rel_hassubcorpus');
        //$criteria->join('SubCorpus sc', 'AnnotationSet', "sc.idSubCorpus = AnnotationSet.idSubCorpus");
        $criteria->join('AnnotationSet', 'Sentence s', "AnnotationSet.idSentence = s.idSentence");
        $criteria->join('AnnotationSet', 'Layer l', "AnnotationSet.idAnnotationSet = l.idAnnotationSet");
        $criteria->join('Layer l', 'LayerType lt', "l.idLayerType=lt.idLayerType");
        Base::relation($criteria, 'LayerType lt', 'GenericLabel', 'rel_haslabeltype');
        //Base::relation($criteria, 'GenericLabel', 'POS p', 'rel_gfpos');
        $criteria->where("idSentence = {$idSentence}");
        //$criteria->where("lu.lemma.pos.entry = p.entry");
        $criteria->where("lt.entry = 'lty_gf'");
        $criteria->where("(GenericLabel.idLanguage = s.idLanguage)");
        if (!$this->allowManyAnnotationSet()) {
            $criteria->where("idAnnotationSet = {$this->getId()}");
        }
        $criteria->orderBy('idAnnotationSet, l.idLayer, GenericLabel.name');
        return $criteria;
    }

    public function getLabelTypesGL($idSentence)
    {
        $criteria = $this->getCriteria();
        $criteria->select("idAnnotationSet, l.idLayer, genericlabel.idEntity as idLabelType, genericlabel.name as labelType, genericlabel.idColor, '' as coreType");
        $criteria->join('AnnotationSet', 'Sentence s', "AnnotationSet.idSentence = s.idSentence");
        $criteria->join('AnnotationSet', 'Layer l', "AnnotationSet.idAnnotationSet = l.idAnnotationSet");
        $criteria->join('Layer l', 'LayerType lt', "l.idLayerType=lt.idLayerType");
        Base::relation($criteria, 'LayerType lt', 'GenericLabel', 'rel_haslabeltype');
        $criteria->where("idSentence = {$idSentence}");
        $criteria->where("lt.entry <> 'lty_gf'");
        $criteria->where("(genericlabel.idLanguage = s.idLanguage)");
        if (!$this->allowManyAnnotationSet()) {
            $criteria->where("idAnnotationSet = {$this->getId()}");
        }
        $criteria->orderBy('idAnnotationSet, l.idLayer, genericlabel.name');
        return $criteria;
    }

    public function getLabelTypesFE($idSentence, $forceId = false)
    {
        if (!$this->allowManyAnnotationSet() || $forceId) {
            $condition = "AND (a.idAnnotationSet = {$this->getId()})";
        }

        $idLanguage = \Manager::getSession()->idLanguage;

        $cmd = <<<HERE

        SELECT distinct a.idAnnotationSet,
            l.idLayer,
            fe.idEntity AS idLabelType,
            e.name AS labelType,
            fe.idColor,
            fe.typeEntry AS coreType,
            ti.info
        FROM View_AnnotationSet a
            INNER JOIN View_Layer l on (a.idAnnotationSet = l.idAnnotationSet)
            INNER JOIN View_SubCorpusLU sc on (a.idSubCorpus = sc.idSubCorpus)
            INNER JOIN View_LU lu on (sc.idLU = lu.idLU)
            INNER JOIN View_FrameElement fe on (lu.idFrame = fe.idFrame)
            INNER JOIN TypeInstance ti on (fe.typeEntry=ti.entry)
            INNER JOIN View_EntryLanguage e on (fe.entry = e.entry)
        WHERE (e.idLanguage = {$idLanguage} )
            AND (l.entry = 'lty_fe' )
            AND (a.idSentence = {$idSentence}) {$condition}
        ORDER BY a.idAnnotationSet, l.idLayer, ti.info, fe.typeEntry, e.name
HERE;

        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function getLabelTypesCE($idSentence)
    {
        if (!$this->allowManyAnnotationSet()) {
            $condition = "AND (a.idAnnotationSet = {$this->getId()})";
        }

        $idLanguage = \Manager::getSession()->idLanguage;

        $cmd = <<<HERE

        SELECT a.idAnnotationSet,
            l.idLayer,
            ce.idEntity AS idLabelType,
            e.name AS labelType,
            ce.idColor,
            '' AS coreType
        FROM View_AnnotationSet a
            INNER JOIN View_Layer l on (a.idAnnotationSet = l.idAnnotationSet)
            INNER JOIN View_SubCorpusCxn sc on (a.idSubCorpus = sc.idSubCorpus)
            INNER JOIN View_ConstructionElement ce on (sc.idConstruction = ce.idConstruction)
            INNER JOIN View_EntryLanguage e on (ce.entry = e.entry)
        WHERE (e.idLanguage = {$idLanguage} )
            AND (l.entry = 'lty_ce' )
            AND (a.idSentence = {$idSentence})
            {$condition}
        ORDER BY a.idAnnotationSet, l.idLayer, e.name
HERE;

        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function getLayerNameCnxFrame($idSentence)
    {
        if (!$this->allowManyAnnotationSet()) {
            $condition = "AND (a.idAnnotationSet = {$this->getId()})";
        }

        $idLanguage = \Manager::getSession()->idLanguage;
        $transaction = $this->getDb()->beginTransaction();
        $cmd = <<<HERE
            SELECT concat('lty_cefe_', f.idFrame) as idLayer, f.idFrame, e.name, a.idAnnotationSet
            FROM View_AnnotationSet a
                INNER JOIN View_SubCorpusCxn sc on (a.idSubCorpus = sc.idSubCorpus)
                INNER JOIN View_Construction c on (sc.idConstruction = c.idConstruction)
                INNER JOIN View_Relation r on (c.idEntity = r.idEntity1)
                INNER JOIN View_Frame f on (r.idEntity2 = f.idEntity)
                INNER JOIN View_EntryLanguage e on (f.entry = e.entry)
            WHERE (e.idLanguage = {$idLanguage})
                AND (r.relationType = 'rel_evokes' )
                {$condition}
                AND (a.idSentence = {$idSentence})
HERE;

        $transaction->commit();
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function getElementCxnFrame($idFrame)
    {
        $cmd = <<<HERE
        SELECT
            ce.idEntity idEntityCE, fe.idEntity idEntityFE
        FROM
            View_ConstructionElement ce
                INNER JOIN View_Relation r on (ce.idEntity = r.idEntity1)
                INNER JOIN View_FrameElement fe on (r.idEntity2 = fe.idEntity)
            WHERE (r.relationType = 'rel_evokes' )
                AND (fe.idFrame = {$idFrame})
HERE;

        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function getLabelTypesCEFE($idSentence)
    {
        if (!$this->allowManyAnnotationSet()) {
            $condition = "AND (a.idAnnotationSet = {$this->getId()})";
        }

        $idLanguage = \Manager::getSession()->idLanguage;

        $cmd = <<<HERE
        SELECT a.idAnnotationSet,
            concat('lty_cefe_', fe.idFrame) as idLayer,
            fe.idEntity AS idLabelType,
            e.name AS labelType,
            fe.idColor,
            '' AS coreType
        FROM View_AnnotationSet a
            INNER JOIN View_SubCorpusCxn sc on (a.idSubCorpus = sc.idSubCorpus)
            INNER JOIN View_ConstructionElement ce on (sc.idConstruction = ce.idConstruction)
            INNER JOIN View_Relation r on (ce.idEntity = r.idEntity1)
            INNER JOIN View_FrameElement fe on (r.idEntity2 = fe.idEntity)
            INNER JOIN View_EntryLanguage e on (fe.entry = e.entry)
        WHERE (e.idLanguage = {$idLanguage})
            AND (r.relationType = 'rel_evokes')
            {$condition}
            AND (a.idSentence = {$idSentence})
        ORDER BY a.idAnnotationSet, idLayer, e.name

HERE;

        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function getNI($idSentence, $idLanguage)
    {
        if (!$this->allowManyAnnotationSet()) {
            $condition = "AND (a.idAnnotationSet = {$this->getId()})";
        }

        $idLanguage = \Manager::getSession()->idLanguage;

        $cmd = <<<HERE

        SELECT lb.idLabel, l.idLayer, lb.idInstantiationType, eit.name as instantiationType, entry_fe.name as feName, fe.idColor as idColor, lb.idLabelType
        FROM Label lb
            INNER JOIN View_Layer l ON (lb.idLayer = l.idLayer)
            INNER JOIN View_AnnotationSet a ON (l.idAnnotationSet = a.idAnnotationSet)
            INNER JOIN View_InstantiationType it ON (lb.idInstantiationType = it.idTypeInstance)
            INNER JOIN View_EntryLanguage eit on (it.entry = eit.entry)
            INNER JOIN View_FrameElement fe
                ON (lb.idLabelType = fe.idEntity)
            INNER JOIN Entry entry_fe
                ON (fe.entry = entry_fe.entry)
        WHERE (it.entry <> 'int_normal')
            AND ((entry_fe.idLanguage = {$idLanguage}) or (entry_fe.idLanguage is null))
            AND ((eit.idLanguage = {$idLanguage}) or (eit.idLanguage is null))
            {$condition}
            AND (a.idSentence = {$idSentence})

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function getCEFEData($idSentence, $idCEFELayer)
    {
        $cmd = <<<HERE
        SELECT ifnull(lb.startChar,-1) AS startChar,
            ifnull(lb.endChar,-1) AS endChar,
            fe.idEntity AS idLabelType
        FROM View_AnnotationSet a
            INNER JOIN View_Layer l  ON (a.idAnnotationSet = l.idAnnotationSet)
            LEFT JOIN Label lb ON (l.idLayer=lb.idLayer)
            LEFT JOIN View_ConstructionElement ce on (lb.idLabelType = ce.idEntity)
            LEFT JOIN View_Relation r on (ce.idEntity = r.idEntity1)
            LEFT JOIN View_FrameElement fe on (r.idEntity2 = fe.idEntity)
        WHERE (r.relationType = 'rel_evokes')
            AND (a.idSentence = {$idSentence})
            AND (concat('lty_cefe_', fe.idFrame) = '{$idCEFELayer}')

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function getLayersOrderByTarget($idSentence)
    {
        if (!$this->allowManyAnnotationSet()) {
            $condition = "AND (a.idAnnotationSet = {$this->getId()})";
        }
        $idLanguage = \Manager::getSession()->idLanguage;
        $cmd = <<<HERE

        SELECT a.idAnnotationSet
        FROM View_AnnotationSet a
            INNER JOIN View_Layer l ON (a.idAnnotationSet = l.idAnnotationSet)
            INNER JOIN View_EntryLanguage el on (l.entry = el.entry)
            LEFT JOIN Label lb ON (l.idLayer=lb.idLayer)
            LEFT JOIN GenericLabel gl ON (lb.idLabelType=gl.idEntity)
            LEFT JOIN View_FrameElement fe ON (lb.idLabelType=fe.idEntity)
            LEFT JOIN View_ConstructionElement ce ON (lb.idLabelType=ce.idEntity)
        WHERE (el.idLanguage = {$idLanguage} )
            {$condition} AND  (l.entry = 'lty_target')
            AND (a.idSentence = {$idSentence} )
        ORDER BY ifnull(lb.startChar,-1), a.idAnnotationSet

HERE;

        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function getLayersData($idSentence)
    {
        if (!$this->allowManyAnnotationSet()) {
            $condition = "AND (a.idAnnotationSet = {$this->getId()})";
        }
        $idLanguage = \Manager::getSession()->idLanguage;
        $cmd = <<<HERE

        SELECT a.idAnnotationSet,
            l.idLayerType,
            l.idLayer,
            el.name AS layer,
            ifnull(lb.startChar,-1) AS startChar,
            ifnull(lb.endChar,-1) AS endChar,
            ifnull(gl.idEntity, ifnull(fe.idEntity, ce.idEntity)) AS idLabelType,
            lb.idLabel,
            l.entry as layerTypeEntry
        FROM View_AnnotationSet a
            INNER JOIN View_Layer l ON (a.idAnnotationSet = l.idAnnotationSet)
            INNER JOIN View_EntryLanguage el on (l.entry = el.entry)
            LEFT JOIN Label lb ON (l.idLayer=lb.idLayer)
            LEFT JOIN GenericLabel gl ON (lb.idLabelType=gl.idEntity)
            LEFT JOIN View_FrameElement fe ON (lb.idLabelType=fe.idEntity)
            LEFT JOIN View_ConstructionElement ce ON (lb.idLabelType=ce.idEntity)
        WHERE (el.idLanguage = {$idLanguage} )
            {$condition}
            AND (a.idSentence = {$idSentence} )
        ORDER BY a.idAnnotationSet, l.layerOrder, l.idLayer, ifnull(lb.startChar,-1)

HERE;

        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function addFELayer()
    {
        $layerType = new LayerType();
        $layerType->getByEntry('lty_fe');
        $layer = new Layer();
        $layer->setIdLayerType($layerType->getIdLayerType());
        $layer->setIdAnnotationSet($this->getId());
        $layer->setRank(0);
        $layer->save();
        return $layer->getIdLayer();
    }

    public function delFELayer()
    {
        $criteria = $this->getCriteria();
        $criteria->select('layers.idLayer');
        $criteria->where("idAnnotationSet = {$this->getId()}");
        $criteria->where("layers.layertype.entry = 'lty_fe'");
        $query = $criteria->asQuery();
        $rows = $query->getResult();
        $maxIdLayer = 0;
        foreach ($rows as $row) {
            if ($row['idLayer'] > $maxIdLayer) {
                $maxIdLayer = $row['idLayer'];
            }
        }
        if ($maxIdLayer > 0) {
            $layer = new Layer($maxIdLayer);
            $layer->delete();
        }
    }

    public function newTimeline()
    {
        $timeline = 'as_' . md5($this->getIdSubCorpus() . $this->getIdSentence());
        parent::setTimeLine(Base::newTimeLine($timeline, 'S'));
    }

    public function save()
    {
        if ($this->timeline == '') {
            $this->newTimeline();
        }
        parent::save();
    }

    public function putLayers($layers)
    {
        //mdump($layers);
        $layerCE = new \StdClass();
        $type = new Type();
        $instances = $type->getInstantiationType('int_normal')->asQuery()->getResult();
        $itNormal = $instances[0]['idInstantiationType'];
        $hasFE = [];
        $transaction = $this->beginTransaction();
        try {
            $label = new Label();
            foreach ($layers as $layer) {
                $idLayer = $layer->idLayer;
                if ($idLayer == '') {
                    continue;
                }
                if ($layer->layerTypeEntry == 'lty_ce') {
                    $layerCE = $layer;
                }
                $labels = array();
                if ($layer->idLayerType != 0) {
                    if (substr($layer->layerTypeEntry, 0, 8) == 'lty_cefe') {
                        $idFrame = substr($layer->layerTypeEntry, 9);//$layer->idFrame;
                        unset($layer);
                        $layer = clone $layerCE;
                        $layer->layerTypeEntry = 'lty_cefe';
                        $queryCEFE = $this->getElementCxnFrame($idFrame);
                        $cefe = $queryCEFE->chunkResult('idEntityCE', 'idEntityFE');
                        foreach ($layerCE as $key => $value) {
                            if (substr($key, 0, 2) == 'wf') {
                                if ($cefe[$value]) {
                                    $layer->$key = $cefe[$value];
                                }
                            }
                        }
                    } else {
                        $label->getDeleteCriteria()->where("idLayer = {$idLayer}")->delete();
                    }
                    $i = -1;
                    $l = 0;
                    $o = -1;
                    foreach ($layer as $key => $value) {
                        if (substr($key, 0, 2) == 'wf') {
                            $idLabelType = $layer->$key;
                            if ($idLabelType == '') {
                                continue;
                            }
                            $pos = (int)(substr($key, 2));
                            if (($idLabelType != $l) || ($pos != $o)) {
                                $i++;
                                $labels[$i] = (object)['idLabelType' => $idLabelType, 'startChar' => $pos, 'endChar' => $pos];
                                $l = $idLabelType;
                            } else {
                                $labels[$i]->endChar = $pos;
                            }
                            $o = $pos + 1;
                        }
                    }
                }
                if (count($labels)) {
                    if (substr($layer->layerTypeEntry, 0, 8) == 'lty_cefe') {
                        continue;
                    }
                    if ($layer->layerTypeEntry == 'lty_fe') {
                        $hasFE[$layer->idAnnotationSet] = true;
                    }
                    if ($layer->layerTypeEntry == 'lty_ce') {
                        $hasFE[$layer->idAnnotationSet] = true;
                    }
                    foreach ($labels as $labelObj) {
                        $label->setPersistent(FALSE);
                        $label->setIdLayer($idLayer);
                        $label->setIdLabelType($labelObj->idLabelType);
                        $label->setStartChar($labelObj->startChar);
                        $label->setEndChar($labelObj->endChar);
                        $label->setMulti(0);
                        $label->setIdInstantiationType($itNormal);
                        $label->save();
                    }
                }
                if ($layer->ni->$idLayer) {
                    $hasFE[$layer->idAnnotationSet] = true;
                    //mdump($layer->ni->$idLayer);
                    foreach ($layer->ni->$idLayer as $idLabelType => $ni) {
                        $label->setPersistent(FALSE);
                        $label->setIdLayer($idLayer);
                        $label->setIdLabelType($idLabelType);
                        $label->setidInstantiationType($ni->idInstantiationType);
                        $label->setStartChar(NULL);
                        $label->setEndChar(NULL);
                        $label->setMulti(0);
                        $label->save();
                    }
                }
            }
            $transaction->commit();
            return $hasFE;
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function createLayersForLU($lu, $data)
    {
        $layerType = new LayerType();
        $layerTypes = $layerType->listToLU($lu);
        foreach ($layerTypes as $lt) {
            $layer = new Layer();
            $layer->setIdLayerType($lt['idLayerType']);
            $layer->setIdAnnotationSet($this->getId());
            $layer->setRank(1);
            $layer->save();
            if ($lt['entry'] == 'lty_target') {
                if (isset($data->chars)) {
                    $startChar = $endChar = 0;
                    array_push($data->chars, '');
                    foreach($data->chars as $i => $char) {
                        if ($char == '') {
                            if ($startChar != 0) {
                                $label = new Label();
                                $label->setMulti(0);
                                $label->setIdInstantiationTypeFromEntry('int_normal');
                                $idLabelType = $layerType->listLabelType((object)['entry' => 'lty_target'])->asQuery()->getResult()[0]['idLabelType'];
                                $label->setIdLabelType($idLabelType);
                                $label->setIdLayer($layer->getId());
                                $label->setStartChar($startChar);
                                $label->setEndChar($endChar);
                                $label->save();
                                $startChar = $endChar = 0;
                            }
                        } else {
                            if ($startChar == 0) {
                                $startChar = $i;
                            }
                            $endChar = $i;
                        }
                    }

                } else {
                    $label = new Label();
                    $label->setMulti(0);
                    $label->setIdInstantiationTypeFromEntry('int_normal');
                    $idLabelType = $layerType->listLabelType((object)['entry' => 'lty_target'])->asQuery()->getResult()[0]['idLabelType'];
                    $label->setIdLabelType($idLabelType);
                    $label->setIdLayer($layer->getId());
                    $label->setStartChar($data->startChar);
                    $label->setEndChar($data->endChar);
                    $label->save();
                }
            }
        }
    }

    public function createLayersForCxn($cxn, $data)
    {
        $layerType = new LayerType();
        $layerTypes = $layerType->listToConstruction();
        foreach ($layerTypes as $lt) {
            $layer = new Layer();
            $layer->setIdLayerType($lt['idLayerType']);
            $layer->setIdAnnotationSet($this->getId());
            $layer->setRank(1);
            $layer->save();
        }
    }

    public function deleteBySubCorpus($idSubCorpus)
    {
        $transaction = $this->beginTransaction();
        try {
            $layer = new Layer();
            $criteria = $this->listBySubCorpus($idSubCorpus);
            $result = $criteria->asQuery()->getResult();
            foreach ($result as $as) {
                $idAS = $as['idAnnotationSet'];
                $layer->deleteByAnnotationSet($idAS);
                $deleteCriteria = $this->getDeleteCriteria()->where("idAnnotationSet = {$idAS}");
                $deleteCriteria->delete();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete()
    {
        $transaction = $this->beginTransaction();
        try {
            $idAnnotationSet = $this->getId();
            $layer = new Layer();
            $layer->deleteByAnnotationSet($idAnnotationSet);
            parent::delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

}
