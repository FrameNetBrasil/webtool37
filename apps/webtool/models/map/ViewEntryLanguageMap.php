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

class ViewEntryLanguageMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'view_entrylanguage',
            'attributes' => array(
                'idEntry' => array('column' => 'idEntry','type' => 'integer','key' => 'primary'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'name' => array('column' => 'name','type' => 'string'),
                'description' => array('column' => 'description','type' => 'string'),
                'nick' => array('column' => 'nick','type' => 'string'),
                'idLanguage' => array('column' => 'idLanguage','type' => 'integer'),
                'language' => array('column' => 'language','type' => 'integer'),
            ),
            'associations' => array(
            )
        );
    }
    

}
