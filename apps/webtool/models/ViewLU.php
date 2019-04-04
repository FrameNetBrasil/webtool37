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

class ViewLU extends map\ViewLUMap {

    public static function config()
    {
        return [];
    }

    public function listByFrame($idFrame, $idLanguage = '', $idLU = NULL)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('name');
        $criteria->where("idFrame = {$idFrame}");
        $criteria->where("idLanguage = {$idLanguage}");
        if ($idLU) {
            if (is_array($idLU)) {
                $criteria->where("idLU", "IN", $idLU);
            } else {
                $criteria->where("idLU = {$idLU}");
            }
        }
        $criteria->orderBy('name');
        return $criteria;
    }

    public function listByLemmaFrame($lemma, $idFrame)
    {
        $criteria = $this->getCriteria()->select('*');
        $criteria->where("idFrame = {$idFrame}");
        $criteria->where("lemmaName = '{$lemma}'");
        return $criteria;
    }

    public function listByIdEntityFrame($idEntityFrame, $idLanguage = '')
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('name');
        $criteria->where("frame.idEntity = {$idEntityFrame}");
        if ($idLanguage != '') {
            $criteria->where("idLanguage = {$idLanguage}");
        }
        return $criteria;
    }

    public function listQualiaRelations($idEntityLU,$idLanguage = '')
    {
        $relation = new ViewRelation();
        $criteria = $relation->getCriteria()->select('relationType, entity1Type, entity2Type, entity3Type, idEntity1, idEntity2, idEntity3');
        $criteria->where("relationGroup = 'rgp_qualia'");
        $criteria->where("idEntity1 = {$idEntityLU}");
        $criteria->setAlias('R');
        $luCriteria = $this->getCriteria()->select('name, R.relationType, R.idEntity2, frame.idEntity idEntityFrame, frame.entries.name nameFrame');
        $luCriteria->joinCriteria($criteria, "R.idEntity2 = idEntity");
        if ($idLanguage != '') {
            $luCriteria->where("idLanguage = {$idLanguage}");
        }
        return $luCriteria;
    }

}

