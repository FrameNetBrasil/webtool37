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

class Translation extends map\TranslationMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'resource' => array('notnull'),
                'text' => array('notnull'),
                'idLanguage' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdTranslation();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*, language.language')->orderBy('resource');
        if ($filter->idTranslation){
            $criteria->where("idTranslation = {$filter->idTransalation}");
        }
        if ($filter->resource){
            $criteria->where("resource LIKE '{$filter->resource}%'");
        }
        return $criteria;
    }

    public function listForUpdate($filter){
        $criteria = $this->getCriteria()->select("idTranslation, resource, text, language.language");
        if ($filter->resource){
            $criteria->where("resource LIKE '{$filter->resource}%'");
        }
        $criteria->orderBy("language.language");
        return $criteria;
    }
    
    public function newResource($resource){
        $languages = Base::languages();
        foreach($languages as $idLanguage=>$language) {
            $this->setPersistent(false);
            $this->setResource($resource);
            $this->setText($resource);
            $this->setIdLanguage($idLanguage);
            $this->save();
        }
    }
    
    public function updateResource($oldResource, $newResource){
        $criteria = $this->getUpdateCriteria();
        $criteria->addColumnAttribute('resource');
        $criteria->where("resource = '{$oldResource}'");
        $criteria->update($newResource);
    }
}

?>