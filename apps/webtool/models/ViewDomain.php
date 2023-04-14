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

class ViewDomain extends map\ViewDomainMap
{
    public static function config()
    {
        return [];
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('idDomain, entry, idEntity, name, idEntityRel, entityType, idLanguage, entryRel, nameRel')->orderBy('name, nameRel');
        $idLanguage = \Manager::getSession()->idLanguage;
        $criteria->where("idLanguage = {$idLanguage}");
        if ($filter->idDomain) {
            $criteria->where("idDomain = {$filter->idDomain}");
        }
        if ($filter->idEntity) {
            $criteria->where("idEntity = {$filter->idEntity}");
        }
        if ($filter->idEntityRel) {
            $criteria->where("idEntityRel = {$filter->idEntityRel}");
        }
        if ($filter->entityType) {
            $criteria->where("entityType = '{$filter->entityType}'");
        }
        return $criteria;
    }

}
