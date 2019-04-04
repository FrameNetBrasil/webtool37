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

class ViewDomainMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'view_domain',
            'attributes' => array(
                'idDomain' => array('column' => 'idDomain', 'type' => 'integer','key' => 'primary'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'idEntityRel' => array('column' => 'idEntityRel','type' => 'integer'),
                'entityType' => array('column' => 'entityType','type' => 'string'),
                'idLanguage' => array('column' => 'idLanguage','type' => 'integer'),
                'nameRel' => array('column' => 'nameRel','type' => 'string'),
            ),
            'associations' => array(
            )
        );
    }

}
