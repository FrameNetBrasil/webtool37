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

class ViewAnnotationSet extends map\ViewAnnotationSetMap {

    public static function config()
    {
        return [];
    }

    public function listBySubCorpus($idSubCorpus, $sortable = NULL) {
        $criteria = $this->getCriteria()->
        select('idAnnotationSet, idSentence, sentence.text, entries.name as annotationStatus, idAnnotationStatus, annotationstatustype.color.rgbBg')->
        where("idSubCorpus = {$idSubCorpus}");
        if ($sortable) {
            if ($sortable->field == 'status') {
                $criteria->orderBy('entries.name ' . $sortable->order);
            }
            if ($sortable->field == 'idSentence') {
                $criteria->orderBy('idSentence ' . $sortable->order);
            }
        }
        Base::entryLanguage($criteria);
        return $criteria;
    }

    public function listSentencesByAS($idAnnotationSet) {
        $criteria = $this->getCriteria()->
        select('idAnnotationSet, idSentence, sentence.text, entries.name as annotationStatus, idAnnotationStatus, annotationstatustype.color.rgbBg');
        if (is_array($idAnnotationSet)) {
            $criteria->where('idAnnotationSet', 'IN', $idAnnotationSet);
        } else {
            $criteria->where('idAnnotationSet', '=', $idAnnotationSet);
        }
        $criteria->orderBy('idAnnotationSet');
        Base::entryLanguage($criteria);
        return $criteria;
    }

    public function listFECEBySubCorpus($idSubCorpus) {
        $idLanguage = \Manager::getSession()->idLanguage;
        $cmd = <<<HERE
        SELECT *
        FROM view_labelfecetarget
        WHERE (idSubCorpus = {$idSubCorpus})
            AND (idLanguage = {$idLanguage} )
        ORDER BY idSentence,startChar

HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->treeResult('idSentence', 'startChar,endChar,rgbFg,rgbBg,instantiationType');
        return $result;
    }

    public function listFECEByAS($idAnnotationSet) {
        $idLanguage = \Manager::getSession()->idLanguage;
        if (is_array($idAnnotationSet)) {
            $set = implode(',', $idAnnotationSet);
            $condition = "(idAnnotationSet IN ({$set}))";
        } else {
            $condition = "(idAnnotationSet = {$idAnnotationSet})";
        }
        $cmd = <<<HERE
        SELECT l.idSentence, l.startChar, l.endChar, l.rgbFg, l. rgbBg, l.instantiationType, fe.entry as feEntry, e.name feName, layerTypeEntry
        FROM view_labelfecetarget l left join view_frameelement fe on (l.idFrameElement = fe.idFrameElement)
        LEFT JOIN entry e on (fe.entry = e.entry)
        WHERE {$condition} AND (l.idLanguage = {$idLanguage} )
        AND ((e.idLanguage = {$idLanguage}) OR (e.idLanguage is null))
        ORDER BY idSentence,startChar

HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->treeResult('idSentence', 'startChar,endChar,rgbFg,rgbBg,instantiationType,feEntry,feName,layerTypeEntry');
        return $result;
    }

    public function listTargetBySentence($idSentence) {
        $idLanguage = \Manager::getSession()->idLanguage;
        $cmd = <<<HERE
        SELECT startChar,endChar,rgbFg,rgbBg,instantiationType
        FROM view_labelfecetarget
        WHERE (idSentence = {$idSentence})
            AND (layerTypeEntry = 'lty_target')
            AND (idLanguage = {$idLanguage} )
        ORDER BY idSentence,startChar

HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->getResult();
        return $result;
    }


    public function listLUCountByLanguage()
    {
        $cmd = <<<HERE
select lu.idlanguage, l.language, count(distinct lu.name) as n
from view_annotationset a
join view_subcorpuslu slu on (a.idSubcorpus = slu.idSubCorpus)
join view_lu lu on (slu.idLu = lu.idLU)
join language l on (lu.idLanguage = l.idLanguage)
group by lu.idlanguage, l.language
order by 2

HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->getResult();
        return $result;
    }

    public function listASCountByLanguage()
    {
        $cmd = <<<HERE
select lu.idlanguage, l.language, count(distinct a.idAnnotationSet) as n
from view_annotationset a
join view_subcorpuslu slu on (a.idSubcorpus = slu.idSubCorpus)
join view_lu lu on (slu.idLu = lu.idLU)
join language l on (lu.idLanguage = l.idLanguage)
group by lu.idlanguage, l.language
order by 2

HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->getResult();
        return $result;
    }

    public function listCountTargetInTextByLanguage()
    {
        $result = [];
        // count words in document by language
        $cmd = <<<HERE
select idLanguage, language
from language
order by language

HERE;
        $languages = $this->getDb()->getQueryCommand($cmd)->getResult();
        foreach($languages as $language) {
            $idLanguage = $language['idLanguage'];
            $cmd = <<<HERE
select text
from view_sentence
where idLanguage = {$idLanguage}

HERE;
            $wordCount = 0;
            $sentences = $this->getDb()->getQueryCommand($cmd)->getResult();
            foreach ($sentences as $sentence) {
                $text = $sentence['text'];
                $words = explode(' ', $text);
                $wordCount += count($words);
            }
            $asCount = 0;
            $cmd = <<<HERE
select count(distinct a.idAnnotationSet) as n
from view_annotationset a
join view_subcorpuslu slu on (a.idSubcorpus = slu.idSubCorpus)
join view_lu lu on (slu.idLu = lu.idLU)
where lu.idLanguage = {$idLanguage}

HERE;
            $as = $this->getDb()->getQueryCommand($cmd)->getResult();
            $asCount = $as[0]['n'];
            if ($asCount > 0) {
                $result[] = [
                    'idLanguage' => $idLanguage,
                    'language' => $language['language'],
                    'n' => ($asCount / $wordCount)
                ];
            }
        }
        return $result;
    }

}

