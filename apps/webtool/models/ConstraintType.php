<?php


namespace fnbr\models;

class ConstraintType extends map\ConstraintTypeMap
{

    public static function config()
    {
        return [];
    }

    public function getByEntry($entry)
    {
        $criteria = $this->getCriteria()->select('*')->where("entry = '{$entry}'");
        $this->retrieveFromCriteria($criteria);
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('idConstraintType, entry, prefix, typeEntity1, typeEntity2, idTypeInstance, entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        if ($filter->idConstraintType) {
            $criteria->where("idConstraintType = {$filter->idConstraintType}");
        }
        if ($filter->constraintType) {
            $criteria->where("upper(entries.name) LIKE upper('{$filter->constraintType}%')");
        }
        return $criteria;
    }

    public function hasInstances() {
        $ci = new ConstraintInstance();
        $filter = (object)[
            'idConstraintType' => $this->getId()
        ];
        $criteria = $ci->listByFilter($filter);
        $result = $criteria->asQuery()->getResult();
        return (count($result) > 0);
    }

    public function save($data) {
        $data->entry = 'con_' . mb_strtolower(str_replace('con_','', $data->name));
        $transaction = $this->beginTransaction();
        try {
            $entry = new Entry();
            if ($this->isPersistent()) {
                $entry->updateEntry($this->getEntry(), $data->entry);
            } else {
                $entry->newEntryByData($data);
            }
            $this->setData($data);
            parent::save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }


    }

}
