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

class GenericLabel extends map\GenericLabelMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'entry' => array('notnull'),
                'idEntity' => array('notnull'),
                'idColor' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getIdGenericLabel();
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idGenericLabel');
        if ($filter->idGenericLabel) {
            $criteria->where("idGenericLabel LIKE '{$filter->idGenericLabel}%'");
        }
        return $criteria;
    }

    public function listByLayerType($idLayerType, $idLanguage = 1)
    {
        $criteria = $this->getCriteria()->select('idGenericLabel, name, idEntity, idColor, color.rgbFg, color.rgbBg')
            ->orderBy('name');
        Base::relation($criteria, 'LayerType', 'GenericLabel', 'rel_haslabeltype');
        $criteria->where("layerType.idLayerType = {$idLayerType}");
        $criteria->where("idLanguage = {$idLanguage}");
        return $criteria;
    }

    public function listForHelp($idLanguage = 1)
    {
        $criteria = $this->getCriteria()->select('idGenericLabel, entry.name as layer, name, definition, example, idEntity, idColor, color.rgbFg, color.rgbBg')
            ->orderBy('layertype.entry,name');
        Base::relation($criteria, 'LayerType', 'GenericLabel', 'rel_haslabeltype');
        $criteria->join('LayerType','Entry', "LayerType.entry = Entry.entry");
        $criteria->where("layerType.entry in ('lty_gf', 'lty_pt')");
        $criteria->where("idLanguage = {$idLanguage}");
        return $criteria;
    }

    public function getTargetIdEntity($filter = NULL)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idGenericLabel');
        if ($filter->idGenericLabel) {
            $criteria->where("idGenericLabel LIKE '{$filter->idGenericLabel}%'");
        }
        return $criteria;
    }

    public function exists($data)
    {
        $criteria = $this->getCriteria()->select('idGenericLabel');
        Base::relation($criteria, 'LayerType', 'GenericLabel', 'rel_haslabeltype');
        $criteria->where("lower(name) = lower('{$data->name}')");
        $criteria->where("layerType.idLayerType = {$data->idLayerType}");
        $criteria->where("idLanguage = {$data->idLanguage}");
        return ($criteria->asQuery()->count() > 0);
    }

    public function inUse()
    {
        $cmd = <<<HERE
                SELECT idLabelType
                FROM Label
                WHERE (idLabelType = {$this->getIdEntity()})

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return (count($query->getResult()) > 0);
    }

    public function saveData($data)
    {
        $transaction = $this->beginTransaction();
        try {
            if (!$this->isPersistent()) {
                $entity = new Entity();
                $entity->setAlias('glb_' . strtolower($data->name) . '_' . $data->idLayerType);
                $entity->setType('GL');
                $entity->save();
                $layerType = new LayerType($data->idLayerType);
                Base::createEntityRelation($layerType->getIdEntity(), 'rel_haslabeltype', $entity->getId());
                $this->setIdEntity($entity->getId());
            }
            $this->setName($data->name);
            $this->setIdColor($data->idColor);
            $this->setIdLanguage($data->idLanguage);
            $this->setDefinition($data->definition);
            mdump(trim($data->example));
            mdump(strlen(trim($data->example)));
            mdump(substr(trim($data->example), -100));
            $this->setExample($data->example);
            Base::entityTimelineSave($this->getIdEntity());
            parent::save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete() {
        $transaction = $this->beginTransaction();
        try {
            $idEntity = $this->getIdEntity();
            // remove relations
            Base::deleteAllEntityRelation($idEntity);
            Base::entityTimelineDelete($this->getIdEntity());
            // remove this fe
            parent::delete();
            // remove entity
            $entity = new Entity($idEntity);
            $entity->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function save()
    {
        Base::entityTimelineSave($this->getIdEntity());
        parent::save();
    }

}
