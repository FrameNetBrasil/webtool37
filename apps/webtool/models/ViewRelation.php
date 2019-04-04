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

class ViewRelation extends map\ViewRelationMap {

    public static function config()
    {
        return [];
    }

    public function listByType($relationType, $entity1Type, $entity2Type = '', $idEntity1 = '', $idEntity2 = '')
    {
        $criteria = $this->getCriteria()->select('relationType, entity1Type, entity2Type, entity3Type, idEntity1, idEntity2, idEntity3');
        $criteria->where("relationType = '{$relationType}'");
        $criteria->where("entity1Type = '{$entity1Type}'");
        if ($entity2Type != '') {
            $criteria->where("entity2Type = '{$entity2Type}'");
        }
        if ($idEntity1 != '') {
            $criteria->where("idEntity1 = {$idEntity1}");
        }
        if ($idEntity2 != '') {
            $criteria->where("idEntity2 = {$idEntity2}");
        }
        return $criteria;
    }


}

