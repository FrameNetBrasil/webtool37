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

class ViewConstructionElementMap extends \MBusinessModel {

    
    public static function ORMMap() {
        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'view_constructionelement',
            'attributes' => array(
                'idConstructionElement' => array('column' => 'idConstructionElement','key' => 'primary','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'active' => array('column' => 'active','type' => 'integer'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
                'idColor' => array('column' => 'idColor','type' => 'integer'),
                'idConstruction' => array('column' => 'idConstruction', 'type' => 'integer'),
                'constructionEntry' => array('column' => 'constructionEntry','type' => 'string'),
                'constructionIdEntity' => array('column' => 'constructionIdEntity','type' => 'integer'),
            ),
            'associations' => array(
                'entries' => array('toClass' => 'fnbr\models\ViewEntryLanguage', 'cardinality' => 'oneToOne' , 'keys' => 'entry:entry'),
                'construction' => array('toClass' => 'fnbr\models\ViewConstruction', 'cardinality' => 'oneToOne' , 'keys' => 'idConstruction:idConstruction'),
                'color' => array('toClass' => 'fnbr\models\Color', 'cardinality' => 'oneToOne' , 'keys' => 'idColor:idColor'),
            )
        );
    }
    

}
