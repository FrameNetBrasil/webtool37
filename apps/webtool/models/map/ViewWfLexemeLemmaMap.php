<?php
/**
 * @category   Maestro
 * @package    UFJF
 *  @subpackage fnbr
 * @copyright  Copyright (c) 2003-2013 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version
 * @since
 */

// wizard - code section created by Wizard Module

namespace fnbr\models\map;

class ViewWfLexemeLemmaMap extends \MBusinessModel {

    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'view_wflexemelemma',
            'attributes' => array(
                'idWordForm' => array('column' => 'idWordForm','key' => 'primary','type' => 'integer'),
                'form' => array('column' => 'form','type' => 'string'),
                'idLexeme' => array('column' => 'idLexeme','type' => 'integer'),
                'lexeme' => array('column' => 'lexeme','type' => 'string'),
                'idPOSLexeme' => array('column' => 'idPOSLexeme','type' => 'integer'),
                'POSLexeme' => array('column' => 'POSLexeme','type' => 'string'),
                'idLanguage' => array('column' => 'idLanguage','type' => 'integer'),
                'idLexemeEntry' => array('column' => 'idLexemeEntry','type' => 'integer'),
                'lexemeOrder' => array('column' => 'lexemeOrder','type' => 'integer'),
                'lexemeOrder' => array('column' => 'lexemeOrder','type' => 'integer'),
                'headWord' => array('column' => 'headWord','type' => 'integer'),
                'idLemma' => array('column' => 'idLemma','type' => 'integer'),
                'idPOSLemma' => array('column' => 'idPOSLemma','type' => 'integer'),
                'POSLemma' => array('column' => 'POSLemma','type' => 'string'),
            ),
            'associations' => array(
            )
        );
    }
    

}
