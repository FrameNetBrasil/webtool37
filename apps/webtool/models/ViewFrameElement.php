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

class ViewFrameElement extends map\ViewFrameElementMap {

    public static function config()
    {
        return [];
    }

    public function relations($idFrameElement = '', $relationType = '', $relationGroup = '')
    {
        $criteria = $this->getCriteria()->select('vr.*');
        $criteria->join('ViewFrameElement','ViewRelation vr', "(vr.idEntity1 = ViewFrameElement.idEntity) or (vr.idEntity2 = ViewFrameElement.idEntity) or (vr.idEntity3 = ViewFrameElement.idEntity)");
        if ($idFrameElement != '') {
            $criteria->where("idFrameElement = {$idFrameElement}" );
        }
        if ($relationType != '') {
            $criteria->where("vr.relationType = '{$relationType}'" );
        }
        if ($relationGroup != '') {
            $criteria->where("vr.relationGroup = '{$relationGroup}'" );
        }
        return $criteria;
    }

    public function hasAnnotations($idFrameElement) {
        $criteria = $this->getCriteria()->select('*');
        $criteria->where("idFrameElement = {$idFrameElement}" );
        $criteria->where("idEntity = labels.idLabelType" );
        $count = $criteria->asQuery()->count();
        return ($count > 0);
    }

    public function getByIdEntity($idEntity) {
        $criteria = $this->getCriteria()->select('*,entries.name as name, entries.nick as nick, frameEntries.name as frameName');
        $criteria->where("idEntity = {$idEntity}");
        $criteria->associationAlias("frame.entries", "frameEntries");
        Base::entryLanguage($criteria);
        Base::entryLanguage($criteria, 'frameEntries.');
        return (object)$criteria->asQuery()->getResult()[0];
    }


    public function listByIdEntityFrame($idEntityFrame, $idLanguage = '')
    {
        $criteria = $this->getCriteria()->select('*,entries.name as name')->orderBy('entries.name');
        $criteria->where("frame.idEntity = {$idEntityFrame}");
        if ($idLanguage != '') {
            $criteria->where("entries.idLanguage = {$idLanguage}");
        }
        return $criteria;
    }

    public function listFEFrameRelations($idEntityFE,$idLanguage = '')
    {
        $relation = new ViewRelation();
        $criteria = $relation->getCriteria()->select('relationType, entity1Type, entity2Type, entity3Type, idEntity1, idEntity2, idEntity3');
        $criteria->where("relationType = 'rel_constraint_frame'");
        $criteria->where("idEntity2 = {$idEntityFE}");
        $criteria->setAlias('R');
        $frame = new Frame();
        $frameCriteria = $frame->getCriteria()->select('entries.name, idEntity');
        $frameCriteria->joinCriteria($criteria, "R.idEntity3 = idEntity");
        if ($idLanguage != '') {
            $frameCriteria->where("entries.idLanguage = {$idLanguage}");
        }
        return $frameCriteria;
    }

}

