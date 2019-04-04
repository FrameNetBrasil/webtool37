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

class Document extends map\DocumentMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'entry' => array('notnull'),
                'author' => array('notnull'),
                'timeline' => array('notnull'),
                'idGenre' => array('notnull'),
                'idCorpus' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getIdDocument();
    }

    public function getEntryObject()
    {
        $criteria = $this->getCriteria()->select('entries.name, entries.description, entries.nick');
        $criteria->where("idDocument = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->asObjectArray()[0];
    }

    public function getName()
    {
        $criteria = $this->getCriteria()->select('entries.name as name');
        $criteria->where("idDocument = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->getResult()[0]['name'];
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*,entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        if ($filter->idDocument) {
            $criteria->where("idDocument = {$filter->idDocument}");
        }
        return $criteria;
    }

    public function listForLookup($name)
    {
        $criteria = $this->getCriteria()->select('idDocument,entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        if ($name != '*') {
            $name = (strlen($name) > 1) ? $name : 'none';
            $criteria->where("upper(entries.name) LIKE upper('{$name}%')");
        }
        return $criteria;
    }

    public function listByCorpus($idCorpus)
    {
        $criteria = $this->getCriteria()->select('idDocument, entry, entries.name as name, count(paragraphs.sentences.idSentence) as quant')->orderBy('entries.name');
        $criteria->setAssociationType('paragraphs.sentences', 'left');
        $criteria->setAssociationType('paragraphs', 'left');
        Base::entryLanguage($criteria);
        $criteria->where("idCorpus = {$idCorpus}");
        $criteria->groupBy("idDocument, entry, entries.name");
        return $criteria;
    }

    public function getByEntry($entry)
    {
        $criteria = $this->getCriteria()->select('*');
        $criteria->where("entry = '{$entry}'");
        $this->retrieveFromCriteria($criteria);
    }

    public function getByName($name, $idLanguage)
    {
        $criteria = $this->getCriteria()->select('*');
        $criteria->where("entries.idLanguage = '{$idLanguage}'");
        $criteria->where("entries.name = '{$name}'");
        $this->retrieveFromCriteria($criteria);
    }

    public function getRelatedSubCorpus()
    {
        $criteria = $this->getCriteria()->select('paragraphs.sentences.annotationsets.subcorpus.idSubCorpus');
        $criteria->where("(paragraphs.sentences.annotationsets.subcorpus.name = 'document-related') or (paragraphs.sentences.annotationsets.subcorpus.name = 'sample')");
        $criteria->where("idDocument = {$this->getId()}");
        $result = $criteria->asQuery()->getResult();
        $idSubCorpus = $result[0]['idSubCorpus'];
        return $idSubCorpus;
    }

    public function save($data)
    {
        $transaction = $this->beginTransaction();
        try {
            if (!$this->isPersistent()) {
                $entry = new Entry();
                $entry->newEntry($this->getEntry());
            }
            //$this->setIdGenre(1); // not informed
            $this->setTimeLine(Base::newTimeLine($this->getEntry(), 'S'));
            parent::save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function updateEntry($newEntry)
    {
        $transaction = $this->beginTransaction();
        try {
            $entry = new Entry();
            Base::updateTimeLine($this->getEntry(), $newEntry);
            $entry->updateEntry($this->getEntry(), $newEntry);
            $this->setEntry($newEntry);
            parent::save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function createParagraph($paragraphNum = 1)
    {
        $paragraph = new Paragraph();
        $paragraph->setIdDocument($this->getIdDocument());
        $paragraph->setDocumentOrder($paragraphNum);
        $paragraph->save();
        return $paragraph;
    }

    public function createSentence($paragraph, $order, $text, $idLanguage)
    {
        $sentence = new Sentence();
        if (substr($text, 0, 1) == ':') {
            $text = substr($text, 1);
        }
        $sentence->setText($text);
        $sentence->setParagraphOrder($order);
        $sentence->setIdParagraph($paragraph->getId());
        $sentence->setIdLanguage($idLanguage);
        $sentence->save();
        return $sentence;
    }

    /**
     * Upload FullText - plain text (without processing) - UTF8
     * @param type $data
     * @param type $file
     * @return type
     * @throws EModelException
     */
    public function uploadFullText($data, $file)
    {
        $idLanguage = $data->idLanguage;
        $transaction = $this->beginTransaction();
        try {
            $this->createSubCorpusFullText($data);
            $breakParagraph = $breakSentence = false;
            $p = $paragraphNum = $sentenceNum = 0;
            $text = '';
            $filename = (is_object($file) ? $file->getTmpName() : $file);
            $rows = file($filename);
            foreach ($rows as $row) {
                $row = str_replace("\t", " ", $row);
                $row = str_replace("\n", " ", $row);
                $row = trim($row);
                if ($row == '') {
                    continue;
                }
                $paragraph = $this->createParagraph(++$paragraphNum); // cada linha do arquivo é um paragrafo
                $words = preg_split('/ /', $row);
                $wordsSize = count($words);
                if ($wordsSize == 0) {
                    continue;
                }
                $text = ''; // texto de cada sentença
                // $break = false;
                foreach ($words as $word) {
                    if ($word == '$START') {
                        continue;
                    }
                    $word = str_replace('"', "'", str_replace('<', '', str_replace('>', '', str_replace('=', ' ', str_replace('$', '', $word)))));
                    mdump($word);
                    $text .= $word;
                    if (preg_match("/\.|\?|!/", $word)) { // quebra de sentença
                    } else {
                        $text .= ' ';
                    }
                    /*
                    if (preg_match("/\.|\?|!/", $word)) { // quebra de sentença
                        if (trim($text) != '') {
                            $sentenceNum++;
                            mdump($paragraphNum . ' - ' . $sentenceNum . ' - ' . $text);
                            $sentence = $this->createSentence($paragraph, $sentenceNum, $text, $idLanguage);
                            $data->idSentence = $sentence->getId();
                            $this->createAnnotationFullText($data);
                            $break = true;
                        }
                        $text = '';
                    } else {
                        $text .= ' ';
                    }
                    */
                }
                //if ((!$break) && ($text != '')) {
                if (trim($text) != '') {
                    $sentenceNum++;
                    mdump($paragraphNum . ' - ' . $sentenceNum . ' - ' . $text);
                    $sentence = $this->createSentence($paragraph, $sentenceNum, $text, $idLanguage);
                    $data->idSentence = $sentence->getId();
                    $this->createAnnotationFullText($data);
                }
            }
            $transaction->commit();
        } catch (\EModelException $e) {
            // rollback da transação em caso de algum erro
            $transaction->rollback();
            throw new EModelException($e->getMessage());
        }
        return $result;
    }

    public function createSubCorpusFullText($data)
    {
        $subCorpus = new SubCorpus();
        $subCorpus->addManualSubCorpusDocument($data);
        $data->idSubCorpus = $subCorpus->getId();
    }

    public function createAnnotationFullText($data)
    {
        $annotationSet = new AnnotationSet();
        $annotationSet->setIdSubCorpus($data->idSubCorpus);
        $annotationSet->setIdSentence($data->idSentence);
        $annotationSet->setIdAnnotationStatus('ast_manual');
        $annotationSet->save();
    }

    /**
     * Upload XML - xml doc with <p> and <s> - UTF8
     * @param type $data
     * @param type $file
     * @return type
     * @throws EModelException
     */
    public function uploadXML($data, $file)
    {
        $idLanguage = $data->idLanguage;
        $transaction = $this->beginTransaction();
        try {
            $fileName = $file->getTmpName();
            // carrega o XML
            if (file_exists($fileName)) {
                $xml = simplexml_load_file($fileName);
                if ($xml === FALSE) {
                    throw new \EModelException('Error processing XML file.');
                }
                $json = json_encode($xml);
                $array = json_decode($json, TRUE);
            } else {
                throw new \EModelException('Failed to open XML file.');
            }
            //mdump($array);
            $text = [];
            foreach ($array['p'] as $p => $par) {
                $sentences = [];
                foreach ($par as $s => $l) {
                    if (is_array($l)) {
                        foreach ($l as $x => $l1) {
                            //mdump($p . ' - ' . $x . ' - ' . $l1);
                            $sentences[] = $l1;
                        }
                    } else {
                        //mdump($p . ' - ' . '0' . ' - ' . $l);
                        $sentences[] = $l;
                    }
                }
                $text[] = $sentences;
            }
            mdump($text);

            $this->createSubCorpusFullText($data);
            foreach($text as $p => $sentences) {
                $paragraphNum = $p + 1;
                $paragraph = $this->createParagraph($paragraphNum);
                $sentenceNum = 0;
                foreach($sentences as $s => $sentence)  {
                    $row = str_replace("\t", " ", $sentence);
                    $row = str_replace("\n", " ", $row);
                    $row = trim($row);
                    if ($row == '') {
                        continue;
                    }
                    $sentenceNum = $sentenceNum + 1;
                    mdump($paragraphNum . ' - ' . $sentenceNum . ' - ' . $text);
                    $sentence = $this->createSentence($paragraph, $sentenceNum, $row, $idLanguage);
                    $data->idSentence = $sentence->getId();
                    $this->createAnnotationFullText($data);
                }
            }

            $transaction->commit();
        } catch (\EModelException $e) {
            // rollback da transação em caso de algum erro
            $transaction->rollback();
            throw new EModelException($e->getMessage());
        }
        return $result;
    }

    public function listAnnotationReport($options, $sort = 'frame', $order = 'asc')
    {
        $idLanguage = \Manager::getSession()->idLanguage;

        $none = ($options['fe'] == 0) && ($options['gf'] == 0) && ($options['pt'] == 0) && ($options['ni'] == 0);

        if ($none) {
            $feSelect = "";
            $feFrom = <<<HERE
INNER JOIN view_labelfecetarget vl on (annotationset.idAnnotationSet = vl.idAnnotationset)
INNER JOIN view_frameelement fe on (vl.idFrameElement = fe.idFrameElement)
INNER JOIN entry e3 ON (fe.entry = e3.entry)
HERE;
            $feWhere = <<<HERE
AND (e3.idLanguage = {$idLanguage})
AND (vl.layerTypeEntry = 'lty_fe')
AND (vl.idLanguage = {$idLanguage})
HERE;
            $feGroupBy = "";
        }

        if ($options['fe']) {
            $feSelect = "fe.entry feEntry, e3.name fe,";
            $feFrom = <<<HERE
INNER JOIN view_labelfecetarget vl on (annotationset.idAnnotationSet = vl.idAnnotationset)
INNER JOIN view_frameelement fe on (vl.idFrameElement = fe.idFrameElement)
INNER JOIN entry e3 ON (fe.entry = e3.entry)
HERE;
            $feWhere = <<<HERE
AND (e3.idLanguage = {$idLanguage})
AND (vl.layerTypeEntry = 'lty_fe')
AND (vl.idLanguage = {$idLanguage})
HERE;
            $feGroupBy = ", fe.entry, e3.name";
        }

        if ($options['gf']) {
            $gfSelect = "gl1.name gf,";
            $gfFrom = <<<HERE
LEFT JOIN layer l1 on (annotationset.idAnnotationSet = l1.idAnnotationset)
LEFT JOIN layerType lt1 on (l1.idLayerType = lt1.idLayerType)
LEFT JOIN label lb1 on (lb1.idLayer = l1.idLayer)
LEFT JOIN genericlabel gl1 ON (lb1.idLabelType = gl1.idEntity)
HERE;
            $gfWhere = <<<HERE
AND ((gl1.idLanguage = {$idLanguage}) OR (gl1.idLanguage is null))
AND (lt1.entry = 'lty_gf')
HERE;
            if ($options['fe']) {
                $gfWhere .= " AND (vl.startChar = lb1.startchar)";
            }
            $gfGroupBy = ", gl1.name";
        }

        if ($options['pt']) {
            $ptSelect = "gl2.name pt,";
            $ptFrom = <<<HERE
LEFT JOIN layer l2 on (annotationset.idAnnotationSet = l2.idAnnotationset)
LEFT JOIN layerType lt2 on (l2.idLayerType = lt2.idLayerType)
LEFT JOIN label lb2 on (lb2.idLayer = l2.idLayer)
LEFT JOIN genericlabel gl2 ON (lb2.idLabelType = gl2.idEntity)
HERE;
            $ptWhere = <<<HERE
AND ((gl2.idLanguage = {$idLanguage}) OR (gl2.idLanguage is null))
AND (lt2.entry = 'lty_pt')
HERE;
            if ($options['fe']) {
                $ptWhere .= " AND (vl.startChar = lb2.startchar)";
            }
            if ($options['gf']) {
                $ptWhere .= " AND (lb1.startChar = lb2.startchar)";
            }
            $ptGroupBy = ", gl2.name";
        }

        if ($options['ni']) {
            $niSelect = "vl.instantiationType ni,";
            $niFrom = "";
            $niWhere = <<<HERE
AND (vl.startChar = -1)
HERE;
            $niGroupBy = ", vl.instantiationType";
        }

        $from = <<<HERE
  FROM document 
  INNER JOIN paragraph ON (document.idDocument = paragraph.idDocument)
  INNER JOIN sentence ON (paragraph.idParagraph = sentence.idParagraph)
  INNER JOIN annotationset ON (sentence.idSentence = annotationset.idSentence)
  INNER JOIN view_subcorpuslu sc ON (annotationset.idSubCorpus = sc.idSubCorpus)
  INNER JOIN view_lu lu ON (sc.idLU = lu.idLU)
  INNER JOIN lemma lm ON (lu.idLemma = lm.idLemma)
  INNER JOIN entry e1 ON (lu.frameEntry = e1.entry)
  INNER JOIN entry e2 ON (document.entry = e2.entry)
  INNER JOIN language l on (lu.idLanguage = l.idLanguage)
  {$feFrom}
  {$gfFrom}
  {$ptFrom}
  {$niFrom}
  WHERE (e1.idLanguage = {$idLanguage} )
  AND (e2.idLanguage = {$idLanguage} )
  AND (lu.idLanguage = {$idLanguage})
  AND (document.idDocument = {$this->getIdDocument()} )
  AND (lu.idLanguage = sentence.idLanguage )
  {$feWhere}
  {$gfWhere}
  {$ptWhere}
  {$niWhere}
HERE;

        if (($sort == '') || ($sort == 'frame')) {
            $cmd = "SELECT document.idDocument,e1.name frame,lu.name lu,l.language lang,{$feSelect}{$gfSelect}{$ptSelect}{$niSelect}count(*) count";
            $cmd .= $from . " GROUP BY document.idDocument,e1.name,lu.name,l.language{$feGroupBy}{$gfGroupBy}{$ptGroupBy}{$niGroupBy}";
        }
        if ($sort == 'lu') {
            $cmd = "SELECT document.idDocument,lu.name lu,e1.name frame,l.language lang,{$feSelect}{$gfSelect}{$ptSelect}{$niSelect}count(*) count";
            $cmd .= $from . " GROUP BY document.idDocument,lu.name,e1.name,l.language{$feGroupBy}{$gfGroupBy}{$ptGroupBy}{$niGroupBy}";
        }

        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;

    }

}
