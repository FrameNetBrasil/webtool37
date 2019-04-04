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

class ViewLUMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'view_lu',
            'attributes' => array(
                'idLU' => array('column' => 'idLU','key' => 'primary','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'senseDescription' => array('column' => 'senseDescription','type' => 'string'),
                'active' => array('column' => 'active','type' => 'integer'),
                'importNum' => array('column' => 'importNum','type' => 'integer'),
                'incorporatedFE' => array('column' => 'incorporatedFE','type' => 'integer'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
                'idLemma' => array('column' => 'idLemma','type' => 'integer'),
                'idFrame' => array('column' => 'idFrame', 'type' => 'integer'),
                'frameEntry' => array('column' => 'frameEntry','type' => 'string'),
                'lemmaName' => array('column' => 'lemmaName','type' => 'string'),
                'idPOS' => array('column' => 'idPOS','type' => 'integer'),
                'idLanguage' => array('column' => 'idLanguage','type' => 'integer'),
            ),
            'associations' => array(
                'frame' => array('toClass' => 'fnbr\models\ViewFrame', 'cardinality' => 'oneToOne' , 'keys' => 'idFrame:idFrame'),
                'language' => array('toClass' => 'fnbr\models\Language', 'cardinality' => 'oneToOne' , 'keys' => 'idLanguage:idLanguage'),
            )
        );
    }
    

}
