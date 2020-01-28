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

class DocumentMM extends map\DocumentMMMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'idDocument' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getByIdDocument($idDocument)
    {
        $criteria = $this->getCriteria()->select('*');
        $criteria->where("idDocument = {$idDocument}");
        $this->retrieveFromCriteria($criteria);
    }

    public function save($data) {
        $document = new Document();
        $document->setData($data);
        $document->save($data);
        $data->idDocument = $document->getId();
        $this->setData($data);
        parent::save();
    }

    public function saveMM() {
        parent::save();
    }

    public function listByCorpus($idCorpus)
    {
        $criteria = $this->getCriteria()->select('document.idDocument, document.entry, document.entries.name as name, count(document.paragraphs.sentences.idSentence) as quant')->orderBy('document.entries.name');
        $criteria->setAssociationType('document.paragraphs.sentences', 'left');
        $criteria->setAssociationType('document.paragraphs', 'left');
        Base::entryLanguage($criteria, 'document');
        $criteria->where("idCorpus = {$idCorpus}");
        $criteria->groupBy("document.idDocument, document.entry, document.entries.name");
        return $criteria;
    }

    public function listForLookup($name)
    {
        $criteria = $this->getCriteria()->select('document.idDocument,document.entries.name as name')->orderBy('document.entries.name');
        Base::entryLanguage($criteria, 'document');
        if ($name != '*') {
            $name = (strlen($name) > 1) ? $name : 'none';
            $criteria->where("upper(document.entries.name) LIKE upper('{$name}%')");
        }
        return $criteria;
    }
}
