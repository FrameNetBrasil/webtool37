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

class ViewFrame extends map\ViewFrameMap
{
    public static function config()
    {
        return [];
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('idFrame, entry, active, idEntity, entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        if ($filter->idFrame) {
            $criteria->where("idFrame = {$filter->idFrame}");
        }
        if ($filter->idEntity) {
            $criteria->where("idEntity = {$filter->idEntity}");
        }
        if ($filter->frame) {
            $criteria->where("entries.name LIKE '{$filter->frame}%'");
        }
        if ($filter->lu) {
            $criteria->distinct(true);
            $criteria->where("lus.name LIKE '{$filter->lu}%'");
        }
        if ($filter->idLU) {
            if (is_array($filter->idLU)) {
                $criteria->where("lus.idLU", "IN", $filter->idLU);
            } else {
                $criteria->where("lus.idLU = {$filter->idLU}");
            }
        }
        if ($filter->fe) {
            $criteria->distinct(true);
            $criteria->associationAlias("fes.entries", "feEntries");
            Base::entryLanguage($criteria,"feEntries.");
            $criteria->where("feEntries.name LIKE '{$filter->fe}%'");
        }
        if ($filter->idDomain) {
            Base::relation($criteria, 'ViewFrame', 'Domain', 'rel_hasdomain');
            $criteria->where("Domain.idDomain = {$filter->idDomain}");
        }
        return $criteria;
    }


}
