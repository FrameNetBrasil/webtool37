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

class ViewAnnotationSetMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'view_annotationsetmm',
            'attributes' => array(
                'idAnnotationSetMM' => array('column' => 'idAnnotationSetMM','key' => 'primary','type' => 'integer'),
                'idSentenceMM' => array('column' => 'idSentenceMM','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'idAnnotationStatus' => array('column' => 'idAnnotationStatus','key' => 'foreign','type' => 'integer'),
                'idDocumentMM' => array('column' => 'idDocumentMM','key' => 'foreign','type' => 'integer'),
            ),
            'associations' => array(
                'entries' => array('toClass' => 'fnbr\models\ViewEntryLanguage', 'cardinality' => 'oneToOne' , 'keys' => 'entry:entry'),
                'sentencemm' => array('toClass' => 'fnbr\models\SentenceMM', 'cardinality' => 'oneToOne' , 'keys' => 'idSentenceMM:idSentenceMM'),
                'annotationsetmm' => array('toClass' => 'fnbr\models\AnnotationSetMM', 'cardinality' => 'oneToOne' , 'keys' => 'idAnnotationSetMM:idAnnotationSetMM'),
            )
        );
    }
    

}
