<?php

/**
 * 
 *
 * @category   Maestro
 * @package    UFJF
 *  @subpackage fnbr
 * @copyright  Copyright (c) 2003-2012 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version    
 * @since      
 */

namespace fnbr\models;

class WordForm extends map\WordFormMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'form' => array('notnull'),
                'idLexeme' => array('notnull'),
                'idLanguage' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getIdWordForm();
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idWordForm');
        if ($filter->idWordForm) {
            $criteria->where("idWordForm LIKE '{$filter->idWordForm}%'");
        }
        return $criteria;
    }

    public function listLUByWordForm($wordform)
    {
        $criteria = $this->getCriteria();
        $criteria->select('lexeme.lexemeentries.lemma.lus.idLU');
        //$criteria->where("upper(form) = upper('{$wordform}')");
        $criteria->where("form = lower('{$wordform}') OR (form LIKE lower('{$wordform}-%')) OR (form LIKE lower('%-{$wordform}'))");
        $lus = $criteria->asQuery()->chunkResult('idLU', 'idLU');
        if (count($lus) > 0) {
            $lu = new LU();
            //$criteria = $lu->getCriteria()->select("idLU, concat(frame.entries.name,'.',name) as fullName, locate(' ', name) as mwe");
            $criteria = $lu->getCriteria()->select("idLU, concat(frame.entries.name,'.',name) as fullName, count(lemma.lexemeentries.idLexemeEntry)-1 as mwe");
            //Base::relation($criteria, 'LU', 'Frame frame', 'rel_evokes');
            Base::entryLanguage($criteria, 'frame');
            $criteria->where("idLU", "IN", $lus);
            $criteria->where("lemma.idLanguage", "=", "frame.entries.idLanguage");
            $criteria->groupBy("idLU, concat(entry.name,'.',lu.name)");
            //return $criteria->asQuery()->chunkResult('idLU', 'fullName');
            return $criteria->asQuery()->asObjectArray();
        } else {
            return [];
        }
    }

    public function lookFor($words) {
        $criteria = $this->getCriteria()->select('form as i, form');
        $criteria->where("form", "in", $words);
        return $criteria->asQuery()->chunkResult('i','form');
    }

    public function listForLookup($wordform = '')
    {
        $idLanguage = \Manager::getSession()->idLanguage;
        $form = trim($wordform);
        $form = (strlen($wordform) == strlen($form)) ? $form . '%' : $form;
        $criteria = $this->getCriteria()->select("idWordForm, concat(form, '  [', lexeme.name, '  ', lexeme.pos.entries.name,']','  [',lexeme.language.language,']') as fullname")->orderBy('form');
        $criteria->where("lexeme.idLanguage = {$idLanguage}");
        $criteria->where("lexeme.pos.entries.idLanguage = {$idLanguage}");
        $criteria->where("form LIKE '{$form}'");
        return $criteria;
    }

    public function listLexemes($words, $idLanguage = null)
    {
        $idLanguage = \Manager::getSession()->idLanguage ?? $idLanguage;
        $criteria = $this->getCriteria()->select('form, lexeme.name as lexeme, lexeme.pos.POS as POSLexeme');
        $criteria->where("form", "in", $words);
        $criteria->where("lexeme.idLanguage", "=", $idLanguage);
        return $criteria->asQuery();
    }

    public function save() {
        parent::save();
        Timeline::addTimeline("wordform",$this->getId(),"S");
    }

    public function saveOffline() {
        parent::save();
        Timeline::addTimeline("wordform",$this->getId(),"S");
    }

    public function hasLU($wordformList)
    {
        $idLanguage = \Manager::getSession()->idLanguage;
        $list = [];
        foreach($wordformList as $i => $wf) {
            if ($wf != '') {
                $wf = str_replace("'","\'", $wf);
                $list[$i] = "'{$wf}'";
            }
        }
        $in = implode(',', $list);
        $criteria = $this->getCriteria();
        $criteria->select('form, count(lexeme.lexemeentries.lemma.lus.idLU) as n');
        $criteria->where("form IN ($in)");
        $criteria->where("lemma.idLanguage = {$idLanguage}");
        $criteria->groupBy("form");
        $criteria->having("count(lexeme.lexemeentries.lemma.lus.idLU) > 0");
        return ($criteria->asQuery()->chunkResult('form','n'));
    }

}

