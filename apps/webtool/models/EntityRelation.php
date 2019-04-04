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

use Maestro\Services\Exception\ERunTimeException;

class EntityRelation extends map\EntityRelationMap {

    public static function config() {
        return array(
            'log' => array(),
            'validators' => array(
                'idRelationType' => array('notnull'),
                'idEntity1' => array('notnull'),
                'idEntity2' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription() {
        return $this->getIdEntityRelation();
    }

    public function listByFilter($filter) {
        $criteria = $this->getCriteria()->select('*')->orderBy('idEntityRelation');
        if ($filter->idEntityRelation) {
            $criteria->where("idEntityRelation LIKE '{$filter->idEntityRelation}%'");
        }
        return $criteria;
    }

    public function listFrameRelations($idEntityFrame = '') {
        $criteria = $this->getCriteria()->select('idEntity1 as superFrame, relationtype.entry as idType, idEntity2 as subFrame');
        $criteria->where("relationtype.relationgroup.entry = 'rgp_frame_relations'");
        if ($idEntityFrame != '') {
            $criteria->where("((idEntity1 = {$idEntityFrame}) or (idEntity2 = {$idEntityFrame}))");
        }
        return $criteria;
    }

    public function listFrameElementRelations($idEntityFrame1, $idEntityFrame2, $relationEntry) {
        $idLanguage = \Manager::getSession()->idLanguage;
        $frameElement = new FrameElement();
        $criteria1 = $frameElement->getCriteria()->select('idEntity');
        Base::relation($criteria1, 'FrameElement', 'Frame', 'rel_elementof');
        $criteria1->where("frame.idEntity = {$idEntityFrame1}");
        $fe1 = $criteria1->asQuery()->chunkResult('idEntity', 'idEntity');
        $criteria2 = $frameElement->getCriteria()->select('idEntity');
        Base::relation($criteria2, 'FrameElement', 'Frame', 'rel_elementof');
        $criteria2->where("frame.idEntity = {$idEntityFrame2}");
        $fe2 = $criteria2->asQuery()->chunkResult('idEntity', 'idEntity');
        $criteria = $this->getCriteria()->select('idEntity1 as superFE, relationtype.entry as idType, idEntity2 as subFE');
        $criteria->where("relationtype.entry = '{$relationEntry}'");
        $criteria->where("idEntity1", "in", $fe1);
        $criteria->where("idEntity2", "in", $fe2);
        return $criteria;
    }

    public function listFrameElementCoreRelations($idEntityFrame) {
        $idLanguage = \Manager::getSession()->idLanguage;
        $frameElement = new FrameElement();
        $criteria1 = $frameElement->getCriteria()->select('idEntity');
        Base::relation($criteria1, 'FrameElement', 'Frame', 'rel_elementof');
        $criteria1->where("frame.idEntity = {$idEntityFrame}");
        $fe1 = $criteria1->asQuery()->chunkResult('idEntity', 'idEntity');
        //$criteria2 = $frameElement->getCriteria()->select('idEntity');
        //Base::relation($criteria2, 'frameelement', 'frame', 'rel_elementof');
        //$criteria2->where("frame.idFrame = {$idFrame}");
        //$fe2 = $criteria2->asQuery()->chunkResult('idEntity','idEntity');
        $criteria = $this->getCriteria()->select('idEntity1 as superFE, relationtype.entry as idType, idEntity2 as subFE');
        $criteria->where("relationtype.relationgroup.entry = 'rgp_fe_relations'");
        $criteria->where("idEntity1", "in", $fe1);
        $criteria->where("idEntity2", "in", $fe1);
        return $criteria;
    }

    public function listCxnRelations($idEntityCxn = '') {
        $criteria = $this->getCriteria()->select('idEntity1 as superCxn, relationtype.entry as idType, idEntity2 as subCxn');
        $criteria->where("relationtype.relationgroup.entry = 'rgp_cxn_relations'");
        if ($idEntityCxn != '') {
            $criteria->where("((idEntity1 = {$idEntityCxn}) or (idEntity2 = {$idEntityCxn}))");
        }
        return $criteria;
    }

    public function listCxnFrameRelations($idEntityCxn = '') {
        $criteria = $this->getCriteria()->select('idEntity1, relationtype.entry as idType, idEntity2');
        $criteria->where("relationtype.entry = 'rel_evokes'");
        if ($idEntityCxn != '') {
            $criteria->where("((idEntity1 = {$idEntityCxn}) or (idEntity2 = {$idEntityCxn}))");
        }
        return $criteria;
    }

    public function listCERelations($idEntityCxn1, $idEntityCxn2, $relationEntry) {
        $idLanguage = \Manager::getSession()->idLanguage;
        $ce = new ConstructionElement();
        $criteria1 = $ce->getCriteria()->select('idEntity');
        Base::relation($criteria1, 'ConstructionElement', 'Construction', 'rel_elementof');
        $criteria1->where("construction.idEntity = {$idEntityCxn1}");
        $ce1 = $criteria1->asQuery()->chunkResult('idEntity', 'idEntity');
        $criteria2 = $ce->getCriteria()->select('idEntity');
        Base::relation($criteria2, 'ConstructionElement', 'Construction', 'rel_elementof');
        $criteria2->where("construction.idEntity = {$idEntityCxn2}");
        $ce2 = $criteria2->asQuery()->chunkResult('idEntity', 'idEntity');
        $criteria = $this->getCriteria()->select('idEntity1 as superCE, relationtype.entry as idType, idEntity2 as subCE');
        $criteria->where("relationtype.entry = '{$relationEntry}'");
        $criteria->where("idEntity1", "in", $ce1);
        $criteria->where("idEntity2", "in", $ce2);
        return $criteria;
    }

    public function listCEFERelations($idEntityCxn, $idEntityFrame, $relationEntry) {
        $idLanguage = \Manager::getSession()->idLanguage;
        $cxnElement = new ConstructionElement();
        $criteria1 = $cxnElement->getCriteria()->select('idEntity');
        Base::relation($criteria1, 'ConstructionElement', 'Construction', 'rel_elementof');
        $criteria1->where("construction.idEntity = {$idEntityCxn}");
        $ce = $criteria1->asQuery()->chunkResult('idEntity', 'idEntity');
        $frameElement = new FrameElement();
        $criteria2 = $frameElement->getCriteria()->select('idEntity');
        Base::relation($criteria2, 'FrameElement', 'Frame', 'rel_elementof');
        $criteria2->where("frame.idEntity = {$idEntityFrame}");
        $fe = $criteria2->asQuery()->chunkResult('idEntity', 'idEntity');
        $criteria = $this->getCriteria()->select('idEntity1, relationtype.entry as idType, idEntity2');
        $criteria->where("relationtype.entry = '{$relationEntry}'");
        $criteria->where("idEntity1", "in", $ce);
        $criteria->where("idEntity2", "in", $fe);
        return $criteria;
    }

    public function remove($idRelationType, $idEntity1, $idEntity2) {
        $delete = $this->getDeleteCriteria();
        $delete->where('idRelationType', '=', $idRelationType);
        $delete->where('idEntity1', '=', $idEntity1);
        $delete->where('idEntity2', '=', $idEntity2);
        $delete->delete();
    }

    public function removeAllFromEntity($idEntity) {
        $delete = $this->getDeleteCriteria();
        $delete->where("(idEntity1 = {$idEntity}) or (idEntity2 = {$idEntity})  or (idEntity3 = {$idEntity})");
        $delete->delete();
    }

    public function saveFrameRelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $this->setPersistent(false);
                $relationType->getByEntry($relation->idType);
                $idRelationType = $relationType->getIdRelationType();
                // remove and save - to avoid duplicates
                $this->remove($idRelationType, $relation->idSource, $relation->idTarget);
                $this->setIdRelationType($idRelationType);
                $this->setIdEntity1($relation->idSource);
                $this->setIdEntity2($relation->idTarget);
                $this->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error saving relations.");
        }
    }

    public function deleteFrameRelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $relationType->getByEntry($relation->idRelationType);
                $idRelationType = $relationType->getIdRelationType();
                $this->remove($idRelationType, $relation->superFrame, $relation->subFrame);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error deleting relations. " . $e);
        }
    }

    public function saveFERelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $this->setPersistent(false);
                $relationType->getByEntry($relation->idType);
                $idRelationType = $relationType->getIdRelationType();
                // remove and save - to avoid duplicates
                $this->remove($idRelationType, $relation->idSource, $relation->idTarget);
                $this->setIdRelationType($idRelationType);
                $this->setIdEntity1($relation->idSource);
                $this->setIdEntity2($relation->idTarget);
                $this->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error saving relations.");
        }
    }

    public function deleteFERelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $relationType->getByEntry($relation->idRelationType);
                $idRelationType = $relationType->getIdRelationType();
                $this->remove($idRelationType, $relation->superFrameElement, $relation->subFrameElement);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error deleting relations. " . $e);
        }
    }

    public function saveFECoreRelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $this->setPersistent(false);
                $relationType->getByEntry($relation->idType);
                $idRelationType = $relationType->getIdRelationType();
                // remove and save - to avoid duplicates
                $this->remove($idRelationType, $relation->idSource, $relation->idTarget);
                $this->setIdRelationType($idRelationType);
                $this->setIdEntity1($relation->idSource);
                $this->setIdEntity2($relation->idTarget);
                $this->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error saving relations.");
        }
    }

    public function deleteFECoreRelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $relationType->getByEntry($relation->idRelationType);
                $idRelationType = $relationType->getIdRelationType();
                $this->remove($idRelationType, $relation->superFE, $relation->subFE);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error deleting relations. " . $e);
        }
    }

    public function saveCxnRelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $this->setPersistent(false);
                $relationType->getByEntry($relation->idType);
                $idRelationType = $relationType->getIdRelationType();
                // remove and save - to avoid duplicates
                $this->remove($idRelationType, $relation->idSource, $relation->idTarget);
                $this->setIdRelationType($idRelationType);
                $this->setIdEntity1($relation->idSource);
                $this->setIdEntity2($relation->idTarget);
                $this->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error saving relations.");
        }
    }

    public function deleteCxnRelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $relationType->getByEntry($relation->idRelationType);
                $idRelationType = $relationType->getIdRelationType();
                $this->remove($idRelationType, $relation->superCxn, $relation->subCxn);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error deleting relations. " . $e);
        }
    }

    public function saveCERelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $this->setPersistent(false);
                $relationType->getByEntry($relation->idType);
                $idRelationType = $relationType->getIdRelationType();
                // remove and save - to avoid duplicates
                $this->remove($idRelationType, $relation->idSource, $relation->idTarget);
                $this->setIdRelationType($idRelationType);
                $this->setIdEntity1($relation->idSource);
                $this->setIdEntity2($relation->idTarget);
                $this->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error saving relations.");
        }
    }

    public function deleteCERelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $relationType->getByEntry($relation->idRelationType);
                $idRelationType = $relationType->getIdRelationType();
                $this->remove($idRelationType, $relation->superCE, $relation->subCE);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error deleting relations. " . $e);
        }
    }
    
    public function saveCxnFrameRelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $this->setPersistent(false);
                $relationType->getByEntry($relation->idType);
                $idRelationType = $relationType->getIdRelationType();
                // remove and save - to avoid duplicates
                $this->remove($idRelationType, $relation->idEntity1, $relation->idEntity2);
                $this->setIdRelationType($idRelationType);
                $this->setIdEntity1($relation->idEntity1);
                $this->setIdEntity2($relation->idEntity2);
                $this->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error saving relations.");
        }
    }

    public function deleteCxnFrameRelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $relationType->getByEntry($relation->idType);
                $idRelationType = $relationType->getIdRelationType();
                $this->remove($idRelationType, $relation->idEntity1, $relation->idEntity2);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error deleting relations. " . $e);
        }
    }

    public function saveCEFERelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $this->setPersistent(false);
                $relationType->getByEntry($relation->idType);
                $idRelationType = $relationType->getIdRelationType();
                // remove and save - to avoid duplicates
                $this->remove($idRelationType, $relation->idEntity1, $relation->idEntity2);
                $this->setIdRelationType($idRelationType);
                $this->setIdEntity1($relation->idEntity1);
                $this->setIdEntity2($relation->idEntity2);
                $this->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error saving relations.");
        }
    }

    public function deleteCEFERelations($relations) {
        $transaction = $this->beginTransaction();
        try {
            $relationType = new RelationType();
            foreach ($relations as $relation) {
                $relationType->getByEntry($relation->idType);
                $idRelationType = $relationType->getIdRelationType();
                $this->remove($idRelationType, $relation->idEntity1, $relation->idEntity2);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ERunTimeException("Error deleting relations. " . $e);
        }
    }

}
