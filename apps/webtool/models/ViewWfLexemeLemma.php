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

class ViewWfLexemeLemma extends map\ViewWfLexemeLemmaMap {

    public static function config()
    {
        return [];
    }

    public function listByFilter($filter = NULL)
    {
        $criteria = $this->getCriteria()->select('idWordForm, form, idLexeme, lexeme, idPOSLexeme, POSLexeme, idLanguage, idLexemeEntry, lexemeOrder, breakBefore, headWord, idLemma, lemma, idPOSLemma, POSLemma, language');
        if (is_null($filter)) {
            $criteria->where("form = ''");
        } else {
            if ($filter->form != '') {
                $criteria->where("form = '{$filter->form}'");
            }
            if ($filter->lexeme != '') {
                $criteria->where("lexeme = '{$filter->lexeme}'");
            }
        }
        return $criteria;
    }


}

