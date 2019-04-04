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

class Label extends map\LabelMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                //'startChar' => array('notnull'),
                //'endChar' => array('notnull'),
                'multi' => array('notnull'),
                'idLabelType' => array('notnull'),
                'idLayer' => array('notnull'),
                'idInstantiationType' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdLabel();
    }

    public function setIdInstantiationTypeFromEntry($entry)
    {
        $ti = new TypeInstance();
        $idInstantiationType = $ti->getIdInstantiationTypeByEntry($entry);
        parent::setIdInstantiationType($idInstantiationType);
    }
    
    public function setIdLabelTypeFromEntry($entry)
    {
        $cmd = <<<HERE

        SELECT FrameElement.idEntity
        FROM FrameElement
        WHERE (FrameElement.entry like '{$entry}%')
        UNION
        SELECT GenericLabel.idEntity
        FROM GenericLabel
        WHERE (GenericLabel.entry like '{$entry}%')
        UNION
        SELECT ConstructionElement.idEntity
        FROM ConstructionElement
        WHERE (ConstructionElement.entry like '{$entry}%')
            
HERE;
        $idLabelType = $this->getDb()->getQueryCommand($cmd)->getResult()[0][0];
        parent::setIdLabelType($idLabelType);
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idLabel');
        if ($filter->idLabel){
            $criteria->where("idLabel LIKE '{$filter->idLabel}%'");
        }
        return $criteria;
    }

    public function deleteByIdLabelType($idLabelType) {
        $criteria = $this->getDeleteCriteria();
        $criteria->where("idLabelType = {$idLabelType}");
        $criteria->delete();
    }

    public function save()
    {
        parent::save();
    }

    public function delete()
    {
        parent::delete();
    }

}
