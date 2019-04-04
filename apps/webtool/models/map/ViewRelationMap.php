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

class ViewRelationMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'view_relation',
            'attributes' => array(
                'idEntityRelation' => array('column' => 'idEntityRelation','key' => 'primary','type' => 'integer'),
                'domain' => array('column' => 'domain','type' => 'string'),
                'relationGroup' => array('column' => 'relationGroup','type' => 'string'),
                'idRelationType' => array('column' => 'idRelationType','type' => 'integer'),
                'relationType' => array('column' => 'relationType','type' => 'string'),
                'prefix' => array('column' => 'prefix','type' => 'string'),
                'idEntity1' => array('column' => 'idEntity1','type' => 'integer'),
                'idEntity2' => array('column' => 'idEntity2','type' => 'integer'),
                'idEntity3' => array('column' => 'idEntity3','type' => 'integer'),
                'entity1Type' => array('column' => 'entity1Type','type' => 'string'),
                'entity2Type' => array('column' => 'entity2Type','type' => 'string'),
                'entity3Type' => array('column' => 'entity3Type','type' => 'string'),
            ),
            'associations' => array(
                'entity1' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity1:idEntity'),
                'entity2' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity2:idEntity'),
                'entity3' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity3:idEntity'),
            )
        );
    }
    

}
