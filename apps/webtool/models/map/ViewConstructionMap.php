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

class ViewConstructionMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'view_construction',
            'attributes' => array(
                'idConstruction' => array('column' => 'idConstruction', 'type' => 'integer','key' => 'primary'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'active' => array('column' => 'active','type' => 'integer'),
                'idLanguage' => array('column' => 'idLanguage','type' => 'integer'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
            ),
            'associations' => array(
                'entries' => array('toClass' => 'fnbr\models\ViewEntryLanguage', 'cardinality' => 'oneToOne' , 'keys' => 'entry:entry'),
                'language' => array('toClass' => 'fnbr\models\Language', 'cardinality' => 'oneToOne' , 'keys' => 'idLanguage:idLanguage'),
                'ces' => array('toClass' => 'fnbr\models\ViewConstructionElement', 'cardinality' => 'oneToMany' , 'keys' => 'idConstruction:idConstruction'),
            )
        );
    }
    

}
