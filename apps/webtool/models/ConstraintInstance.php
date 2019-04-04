<?php

namespace fnbr\models;


class ConstraintInstance extends map\ConstraintInstanceMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'idConstraintType' => array('notnull'),
                'idConstraint' => array('notnull'),
                'idConstrained' => array('notnull'),
                'idConstrainedBy' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('idConstraintInstance, idConstraintType, idConstraint, idConstrained, idConstrainedBy');
        if ($filter->idConstraintType) {
            $criteria->where("idConstraintType = {$filter->idConstraintType}");
        }
        if ($filter->idConstraint) {
            $criteria->where("idConstraint = {$filter->idConstraint}");
        }
        return $criteria;
    }

    public function getByIdConstraint($idConstraint)
    {
        $filter = (object)[
            'idConstraint' => $idConstraint
        ];
        $criteria = $this->listByFilter($filter);
        $this->retrieveFromCriteria($criteria);
    }

    public function delete()
    {
        $transaction = $this->beginTransaction();
        try {
            $view = new ViewConstraint();
            $data = $view->getConstraintData($this->getIdConstraint());
            mdump($data);
            if ($data->entry == 'rel_constraint_constraint') {
                Base::deleteAllEntityRelation($data->idConstrainedBy);
                $entity = new Entity($data->idConstrainedBy);
                $entity->delete();
            }
            // remove relations
            Base::deleteAllEntityRelation($this->getId());
            // remove this entity
            parent::delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function getConstraintData()
    {
        $view = new ViewConstraint();
        return $view->getConstraintData($this->getId());
    }

    public function listConstraints()
    {
        $constraint = new ViewConstraint();
        return $constraint->getByIdConstrained($this->getId());
    }

    public function listConstraintsCN()
    {
        $constraint = new ViewConstraint();
        $cn = $constraint->listConstraintsCNCE($this->getId());
        if ($cn[0]['name'] == '') {
            $cn = $constraint->listConstraintsCNCN($this->getId());
        }
        return $cn;
    }

    public function deleteConstraintLU($idEntityLU, $idEntityConstraint)
    {
        $er = new EntityRelation();
        $transaction = $er->beginTransaction();
        $criteria = $er->getDeleteCriteria();
        $criteria->where("idEntity1 = {$idEntityLU}");
        $criteria->where("idEntity2 = {$idEntityConstraint}");
        $criteria->delete();
        $transaction->commit();
    }

    public function deleteConstraintMetonymyFEFE($idEntity1FE, $idEntity2FE)
    {
        Base::deleteEntityRelation($idEntity1FE, 'rel_festandsforfe', $idEntity2FE);
    }

    public function deleteConstraintMetonymyFELU($idEntityFE, $idEntityLU)
    {
        Base::deleteEntityRelation($idEntityFE, 'rel_festandsforlu', $idEntityLU);
    }


}
