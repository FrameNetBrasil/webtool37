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

class ViewSubCorpusLUMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'view_subcorpuslu',
            'attributes' => array(
                'idSubCorpus' => array('column' => 'idSubCorpus','key' => 'primary','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'rank' => array('column' => 'rank','type' => 'int'),
                'idLU' => array('column' => 'idLU','type' => 'integer'),
            ),
            'associations' => array(
                'annotationsets' => array('toClass' => 'fnbr\models\ViewAnnotationSet', 'cardinality' => 'oneToMany' , 'keys' => 'idSubCorpus:idSubCorpus'),
                'lu' => array('toClass' => 'fnbr\models\ViewLU', 'cardinality' => 'oneToOne' , 'keys' => 'idLU:idLU'),
            )
        );
    }
    

}
